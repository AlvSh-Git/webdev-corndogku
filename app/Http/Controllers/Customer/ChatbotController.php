<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $userMessage = $request->input('message');
        $botReply    = 'Maaf, asisten sedang tidak tersedia. Silakan coba lagi nanti.';

        // ── Lightweight RAG: inject live menu/price data into the system prompt ──
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
                    'model'    => 'llama-3.1-8b-instant',
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $botReply = $response->json('choices.0.message.content') ?? $botReply;
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
            $lines[] = "Kategori {$category}:";
            foreach ($items as $p) {
                $price = (int) $p->price;
                $priceLabel = $price > 0
                    ? 'Rp' . number_format($price, 0, ',', '.')
                    : 'komponen custom (gratis)';

                // Inject the real composition/ingredients so the model is grounded
                // and cannot invent what's inside an item.
                $desc = trim((string) $p->description) ?: $this->fallbackDescription($p->name);

                $lines[] = "- {$p->name} ({$priceLabel}): {$desc}";
            }
        }

        // The Custom Corndog builder starts from a fixed base price.
        $lines[] = 'Custom Corndog: rakit sendiri, harga mulai Rp16.000.';

        return implode("\n", $lines);
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

        return "Kamu adalah asisten virtual Corndog-Ku. Panggil pelanggan dengan sebutan 'Kak'. Gunakan bahasa Indonesia santai, ramah, dan luwes (seperti admin sosmed kekinian). Jangan menggunakan bahasa baku atau kaku.\n\n"
             . "PERANMU: HANYA menjawab tentang menu Corndog-Ku, harga, cara pemesanan, lokasi, jam buka, dan nomor telepon/kontak.\n\n"
             . "INFO LOKASI, JAM BUKA & KONTAK (Gunakan bahasa santai saat menjawab):\n"
             . "- Lokasi: {$alamat}\n"
             . "- Jam Buka: {$jamBuka}\n"
             . "- Status Saat Ini: {$status}\n"
             . "- Nomor Telepon/WA: {$phone}\n\n"
             . "INFO MENU SAAT INI:\n"
             . $businessContext . "\n\n"
             . "ATURAN MUTLAK (SANKSI TEGAS):\n"
             . "1. DILARANG KERAS membahas coding, IT, pelajaran, atau topik di luar Corndog-Ku.\n"
             . "2. Jika ditanya hal di luar konteks, tolak dengan kalimat template ini: 'Duh maaf banget Kak, aku cuma bisa bantu jawab seputar menu dan pesanan Corndog-Ku aja nih! 🌭'\n"
             . "3. Jawab sesingkat dan seasik mungkin. Jangan bertele-tele.\n"
             . "4. Kalau ditanya soal buka/tutup sekarang, jawab sesuai 'Status Saat Ini' di atas.\n"
             . "5. ANTI-HALUSINASI: Kamu DILARANG KERAS mengarang, menebak, atau menambahkan menu, harga, atau komposisi bahan sendiri. HANYA gunakan informasi yang ada di [INFO MENU SAAT INI]. Jika bahan tidak disebutkan di sana, jangan dikarang!\n"
             . "6. FILTERING: Jika pelanggan meminta menu TANPA bahan tertentu (misal: 'tanpa keju' atau 'alergi sosis'), kamu WAJIB mengecek deskripsi menu dan DILARANG merekomendasikan menu yang mengandung bahan tersebut.\n\n"
             . "CONTOH PERCAKAPAN:\n"
             . "User: 'Lokasinya dimana min?'\n"
             . "Kamu: 'Lokasi Corndog-Ku ada di {$alamat}, Kak! Mampir yuk! 🌭'\n"
             . "User: 'Buka jam berapa?'\n"
             . "Kamu: 'Jam buka kita {$jamBuka} ya Kak. Ditunggu orderannya!'\n"
             . "User: 'Sekarang buka gak?'\n"
             . "Kamu: '{$status}, Kak! 🌭'\n"
             . "User: 'nomor wa atau teleponnya berapa min?'\n"
             . "Kamu: 'Kakak bisa hubungi Corndog-Ku di nomor {$phone} ya! Ditunggu orderannya! 🌭'\n"
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
            $history[] = ['role' => 'user', 'content' => $log->message];
            if (!empty($log->response)) {
                $history[] = ['role' => 'assistant', 'content' => $log->response];
            }
        }

        return $history;
    }
}
