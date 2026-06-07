<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Customer-service chatbot backed by the Groq LLM.
class ChatbotController extends Controller
{
    // Handle one chat message and return the bot's reply.
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $userMessage = $request->input('message');
        $botReply    = 'Maaf, asisten sedang tidak tersedia. Silakan coba lagi nanti.';

        // Lightweight RAG: inject live menu/price data into the system prompt.
        $businessContext = $this->buildBusinessContext();
        $storeInfo       = $this->buildStoreInfo();
        $systemPrompt    = $this->buildSystemPrompt($businessContext, $storeInfo);

        // Pull a few recent turns as conversation history — ONLY for logged-in
        // users (a null user_id would otherwise mix in every guest's logs).
        $chatHistory = $this->recentHistory();

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $chatHistory,
            [['role' => 'user', 'content' => $userMessage]]
        );

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . config('services.groq.key')])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => $messages,
                    // Cap the reply: keeps answers short (per the persona) AND keeps us
                    // under Groq's free-tier tokens-per-minute ceiling so a customer can
                    // actually hold a multi-turn conversation without hitting a 429.
                    'max_tokens'  => 300,
                    'temperature' => 0.4,
                ]);

            if ($response->successful()) {
                $botReply = $response->json('choices.0.message.content') ?? $botReply;
            } elseif ($response->status() === 429) {
                // Rate limited (free-tier TPM). Give the customer a friendly nudge
                // instead of the generic "assistant unavailable" wording.
                $botReply = 'Waduh, lagi rame banget nih Kak 😅 Tunggu beberapa detik terus tanya lagi ya!';
                Log::warning('Groq API rate limited', ['body' => $response->body()]);
            } else {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Groq API exception', ['error' => $e->getMessage()]);
        }

        ChatbotLog::create([
            'user_id'  => Auth::id(),
            'message'  => $userMessage,
            'response' => $botReply,
        ]);

        return response()->json(['reply' => $botReply]);
    }

    /**
     * Builds a clean, readable text block of the CURRENT available menu, grouped
     * by category, straight from the database. This is the "retrieval" half of
     * the lightweight RAG — it grounds the model so it can't invent prices/items.
     *
     * NOTE: the products table uses `is_available` (not `is_active`).
     */
    private function buildBusinessContext(): string
    {
        try {
            $products = Product::with('category')
                ->where('is_available', true)
                ->orderBy('category_id')
                ->orderBy('name')
                ->get(['id', 'category_id', 'name', 'description', 'price', 'is_custom']);
        } catch (\Throwable $e) {
            Log::error('Chatbot context query failed', ['error' => $e->getMessage()]);
            return 'Data menu sementara tidak tersedia.';
        }

        if ($products->isEmpty()) {
            return 'Belum ada menu yang tersedia saat ini.';
        }

        $lines = [];
        foreach ($products->groupBy(fn ($p) => $p->category?->name ?? 'Lainnya') as $category => $items) {
            // The Custom category is a bag of build-your-own components, not real
            // menu items. Listing all 10 with a generic description wastes tokens and
            // confuses the model — collapse it into one compact line instead.
            if (mb_strtolower($category) === 'custom') {
                $lines[] = 'Kategori Custom Corndog (rakit sendiri, mulai Rp16.000) — pilih komponen: '
                    . $this->customComponentList($items) . '.';
                continue;
            }

            $lines[] = "Kategori {$category}:";
            foreach ($items as $p) {
                $price = (int) $p->price;
                $priceLabel = $price > 0
                    ? 'Rp' . number_format($price, 0, ',', '.')
                    : 'gratis';

                // Inject the real composition/ingredients so the model is grounded and
                // cannot invent what's inside an item. Condensed to the essentials so
                // the whole menu stays well under the API's token budget.
                $desc = trim((string) $p->description) ?: $this->fallbackDescription($p->name);

                $lines[] = "- {$p->name} ({$priceLabel}): {$this->condense($desc)}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Renders the build-your-own components as one compact, comma-separated line,
     * each with its surcharge (free components show no price). Keeps the Custom
     * category from bloating the prompt with ten near-identical bullet lines.
     */
    private function customComponentList($items): string
    {
        return $items
            ->map(function ($p) {
                $price = (int) $p->price;
                return $price > 0
                    ? "{$p->name} (+Rp" . number_format($price, 0, ',', '.') . ')'
                    : $p->name;
            })
            ->implode(', ');
    }

    /**
     * Trims a marketing description down to its grounding essentials — the leading
     * ingredient phrase — dropping the flavour-text tail. Halves the menu's token
     * footprint so multi-turn chats stay under the free-tier rate limit, while the
     * ingredients needed for allergy filtering (which always lead) are preserved.
     */
    private function condense(string $text, int $max = 80): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        $cut = mb_substr($text, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');

        return rtrim($lastSpace ? mb_substr($cut, 0, $lastSpace) : $cut, " ,.;");
    }

    /**
     * Last-resort composition text derived from the product name, used only when
     * the database `description` is empty. Keeps the RAG context from ever
     * shipping a blank ingredient line (which is what invites hallucination).
     */
    private function fallbackDescription(string $name): string
    {
        $n = mb_strtolower($name);

        $hints = [
            'mozza'      => 'mengandung keju mozzarella lumer',
            'cheese'     => 'mengandung keju',
            'keju'       => 'mengandung keju',
            'sosis'      => 'mengandung sosis',
            'sausage'    => 'mengandung sosis',
            'potato'     => 'dengan balutan kentang crispy',
            'ramen'      => 'dengan topping ramen crispy',
            'choco'      => 'dengan glaze coklat',
            'coklat'     => 'dengan glaze coklat',
            'milk'       => 'dengan glaze susu',
            'greentea'   => 'dengan glaze greentea',
            'taro'       => 'dengan glaze taro',
            'strawberry' => 'dengan rasa strawberry',
            'es teler'   => 'minuman es segar',
            'bingsoo'    => 'es serut ala Korea',
        ];

        $matched = [];
        foreach ($hints as $needle => $phrase) {
            if (str_contains($n, $needle)) {
                $matched[] = $phrase;
            }
        }

        return $matched
            ? ('Menu ' . implode(', ', $matched) . '.')
            : 'Komposisi belum tercatat — sarankan pelanggan tanya admin untuk detail bahan.';
    }

    /**
     * Collects the LIVE store state — canonical address, opening hours, and the
     * real-time open/closed status — all sourced from the base Controller so the
     * chatbot never drifts out of sync with the actual schedule/database.
     */
    private function buildStoreInfo(): array
    {
        // Fetch the schedule once and reuse it for both status + hours.
        $schedule = $this->operationalSchedule();
        $status   = $this->calcStoreStatus($schedule);

        if ($status['is_open']) {
            $statusLine = 'Sedang BUKA sekarang';
        } elseif (!empty($status['reopen_day']) && !empty($status['reopen_time'])) {
            $statusLine = "Sedang TUTUP, buka lagi {$status['reopen_day']} jam {$status['reopen_time']}";
        } else {
            $statusLine = 'Sedang TUTUP';
        }

        return [
            'address' => $this->storeAddress(),
            'hours'   => $this->scheduleHours($schedule),
            'status'  => $statusLine,
            'phone'   => (string) config('store.phone'),
            // Grounds "cara pesan?" so the model stops inventing channels (it was
            // hallucinating social-media ordering). This is the real app flow.
            'order'   => 'Pesan lewat website Corndog-Ku: pilih menu atau rakit Custom Corndog, masukkan ke keranjang, lalu checkout & bayar online. Bisa juga datang langsung ke toko.',
        ];
    }

    /**
     * The strict, grounded system prompt. The retrieved menu context and the
     * live store state are injected verbatim, and the model is told to answer
     * ONLY from it.
     */
    private function buildSystemPrompt(string $businessContext, array $storeInfo): string
    {
        $alamat  = $storeInfo['address'];
        $jamBuka = $storeInfo['hours'];
        $status  = $storeInfo['status'];
        $phone   = $storeInfo['phone'];
        $caraPesan = $storeInfo['order'];

        return "Kamu adalah asisten virtual Corndog-Ku. Panggil pelanggan dengan sebutan 'Kak'. Gunakan bahasa Indonesia santai, ramah, dan luwes (seperti admin sosmed kekinian). Jangan menggunakan bahasa baku atau kaku.\n\n"
             . "PERANMU: menjawab SEMUA hal seputar Corndog-Ku — menu, harga, komposisi/bahan, rekomendasi, alergi, cara pesan, lokasi, jam buka, dan kontak. Semua ini DALAM KONTEKS, jawab dengan ramah.\n\n"
             . "INFO LOKASI, JAM BUKA & KONTAK (Gunakan bahasa santai saat menjawab):\n"
             . "- Lokasi: {$alamat}\n"
             . "- Jam Buka: {$jamBuka}\n"
             . "- Status Saat Ini: {$status}\n"
             . "- Nomor Telepon/WA: {$phone}\n"
             . "- Cara Pesan: {$caraPesan}\n\n"
             . "INFO MENU SAAT INI:\n"
             . $businessContext . "\n\n"
             . "ATURAN MUTLAK (SANKSI TEGAS):\n"
             . "1. DILARANG KERAS membahas coding, IT, pelajaran, politik, atau topik yang benar-benar TIDAK ada hubungannya dengan Corndog-Ku.\n"
             . "2. Kalimat penolakan template ini: 'Duh maaf banget Kak, aku cuma bisa bantu jawab seputar menu dan pesanan Corndog-Ku aja nih! 🌭'. HANYA pakai kalimat ini kalau topiknya benar-benar di luar Corndog-Ku. JANGAN PERNAH memakai kalimat penolakan ini untuk pertanyaan soal menu, bahan, alergi, rekomendasi, harga, atau cara pesan — itu semua WAJIB kamu jawab.\n"
             . "3. Jawab sesingkat dan seasik mungkin. Jangan bertele-tele.\n"
             . "4. Kalau ditanya soal buka/tutup sekarang, jawab sesuai 'Status Saat Ini' di atas.\n"
             . "5. ANTI-HALUSINASI: Kamu DILARANG KERAS mengarang, menebak, atau menambahkan menu, harga, atau komposisi bahan sendiri. HANYA gunakan informasi yang ada di [INFO MENU SAAT INI]. Jika bahan tidak disebutkan di sana, jangan dikarang!\n"
             . "6. FILTERING: Jika pelanggan meminta menu TANPA bahan tertentu (misal: 'tanpa keju' atau 'alergi sosis'), kamu WAJIB mengecek deskripsi menu dan DILARANG merekomendasikan menu yang mengandung bahan tersebut.\n"
             . "7. JANGAN DUMP SEMUA MENU. Kalau ditanya 'menu apa aja' atau 'jual apa', JANGAN tulis ulang seluruh daftar. Sebutkan saja kategori yang ada (Corndog Asin, Corndog Manis, Toppoki, Combo, Es Teler, Bingsoo, Custom) plus 1-2 contoh laris, lalu tanya pelanggan mau kategori yang mana. Sebut harga/detail item HANYA kalau diminta spesifik.\n\n"
             . "CONTOH PERCAKAPAN:\n"
             . "User: 'Lokasinya dimana min?'\n"
             . "Kamu: 'Lokasi Corndog-Ku ada di {$alamat}, Kak! Mampir yuk! 🌭'\n"
             . "User: 'Buka jam berapa?'\n"
             . "Kamu: 'Jam buka kita {$jamBuka} ya Kak. Ditunggu orderannya!'\n"
             . "User: 'Sekarang buka gak?'\n"
             . "Kamu: '{$status}, Kak! 🌭'\n"
             . "User: 'nomor wa atau teleponnya berapa min?'\n"
             . "Kamu: 'Kakak bisa hubungi Corndog-Ku di nomor {$phone} ya! Ditunggu orderannya! 🌭'\n"
             . "User: 'menu apa aja yang ada?'\n"
             . "Kamu: 'Banyak Kak! Kita ada Corndog Asin, Corndog Manis, Toppoki, Combo, Es Teler, Bingsoo, sampai Custom rakit sendiri 🌭 Yang paling laris Corndog Original sama Toppoki Korean. Kakak mau lihat kategori yang mana dulu nih?'\n"
             . "User: 'Ada corndog yang gak pake keju?'\n"
             . "Kamu: 'Ada dong Kak! Kakak bisa pesen Corndog Full Sausage, isinya full sosis tanpa keju ya! 🌭'\n"
             . "User: 'Tolong buatkan kode Java.'\n"
             . "Kamu: 'Duh maaf banget Kak, aku cuma bisa bantu jawab seputar menu dan pesanan Corndog-Ku aja nih! 🌭'";
    }

    /**
     * Last few conversation turns for the logged-in user, as alternating
     * user/assistant messages. Returns [] for guests to avoid leaking other
     * guests' logs (user_id IS NULL would match all of them).
     */
    private function recentHistory(): array
    {
        if (! Auth::id()) {
            return [];
        }

        $history = [];
        $recent = ChatbotLog::where('user_id', Auth::id())
            ->latest()
            ->take(3)
            ->get()
            ->reverse();

        foreach ($recent as $log) {
            // Truncate each turn: history is only for light context, and an old
            // long reply (e.g. a menu rundown) would otherwise eat the per-minute
            // token budget and trigger rate limits on later messages.
            $history[] = ['role' => 'user', 'content' => mb_substr($log->message, 0, 300)];
            if (!empty($log->response)) {
                $history[] = ['role' => 'assistant', 'content' => mb_substr($log->response, 0, 300)];
            }
        }

        return $history;
    }
}
