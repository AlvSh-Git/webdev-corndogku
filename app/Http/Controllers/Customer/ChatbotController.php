<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private const SYSTEM_PROMPT = 'Kamu adalah asisten virtual toko Corndog-Ku. Jawablah dengan ramah, singkat (maksimal 2 kalimat), dan jangan gunakan format markdown seperti bintang/tebal. Jika ditanya menu, tawarkan Original, Mozza Cheese, atau Squid Nori. Harga mulai 15rb-20rb. Lokasi di Jl. Rungkut Mejoyo Utara No.61. Jika user alergi corndog, tanyakan kenapa mampir ke sini dengan nada bercanda.';

    public function sendMessage(Request $request)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $userMessage = $request->input('message');
        $botReply    = 'Maaf, asisten sedang tidak tersedia. Silakan coba lagi nanti.';

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . env('GROQ_API_KEY')])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'    => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                        ['role' => 'user',   'content' => $userMessage],
                    ],
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
}
