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
        $systemPrompt    = $this->buildSystemPrompt($businessContext);

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
                ->withHeaders(['Authorization' => 'Bearer ' . env('GROQ_API_KEY')])
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
                ->get(['id', 'category_id', 'name', 'price', 'is_custom']);
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
                $lines[] = "- {$p->name}: {$priceLabel}";
            }
        }

        // The Custom Corndog builder starts from a fixed base price.
        $lines[] = 'Custom Corndog: rakit sendiri, harga mulai Rp16.000.';

        return implode("\n", $lines);
    }

    /**
     * The strict, grounded system prompt. The retrieved menu context is injected
     * verbatim, and the model is told to answer ONLY from it.
     */
    private function buildSystemPrompt(string $businessContext): string
    {
        $alamat = 'Jl. Rungkut Mejoyo Utara No.61, Surabaya';
        $jamBuka = 'Senin–Kamis 10.00–21.00, Jumat–Sabtu 10.00–22.00, Minggu tutup (WIB)';

        return "Kamu adalah asisten virtual Corndog-Ku. Panggil pelanggan dengan sebutan 'Kak'. Gunakan bahasa Indonesia santai, ramah, dan luwes (seperti admin sosmed kekinian). Jangan menggunakan bahasa baku atau kaku.\n\n"
             . "PERANMU: HANYA menjawab tentang menu Corndog-Ku, harga, cara pemesanan, lokasi, dan jam buka.\n\n"
             . "INFO LOKASI & JAM BUKA (Gunakan bahasa santai saat menjawab):\n"
             . "- Lokasi: {$alamat}\n"
             . "- Jam Buka: {$jamBuka}\n\n"
             . "INFO MENU SAAT INI:\n"
             . $businessContext . "\n\n"
             . "ATURAN MUTLAK (SANKSI TEGAS):\n"
             . "1. DILARANG KERAS membahas coding, IT, pelajaran, atau topik di luar Corndog-Ku.\n"
             . "2. Jika ditanya hal di luar konteks, tolak dengan kalimat template ini: 'Duh maaf banget Kak, aku cuma bisa bantu jawab seputar menu dan pesanan Corndog-Ku aja nih! 🌭'\n"
             . "3. Jawab sesingkat dan seasik mungkin. Jangan bertele-tele.\n\n"
             . "CONTOH PERCAKAPAN:\n"
             . "User: 'Lokasinya dimana min?'\n"
             . "Kamu: 'Lokasi Corndog-Ku ada di {$alamat}, Kak! Mampir yuk! 🌭'\n"
             . "User: 'Buka jam berapa?'\n"
             . "Kamu: 'Kita buka dari jam {$jamBuka} ya Kak. Ditunggu orderannya!'\n"
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
