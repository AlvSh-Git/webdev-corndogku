<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\NormalizesPhone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppResetController extends Controller
{
    use NormalizesPhone;

    /** OTP validity window. */
    private const OTP_TTL_MINUTES = 5;

    /**
     * Show the WhatsApp OTP reset view (clone of the Login layout).
     */
    public function show()
    {
        return view('auth.reset-password-wa');
    }

    /**
     * Step 1 — look the user up by email, and if they have a WhatsApp number
     * on file, generate a 6-digit OTP, cache it (keyed by email), and deliver
     * it over WhatsApp via Fonnte to that number.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || empty($user->phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan atau belum mendaftarkan nomor WhatsApp.',
            ], 404);
        }

        $targetPhone = $this->normalizePhone($user->phone);

        $otp = (string) random_int(100000, 999999);
        Cache::put($this->otpKey($user->email), $otp, now()->addMinutes(self::OTP_TTL_MINUTES));

        // Remember which account we're resetting for Step 2 / Step 3.
        $request->session()->put('wa_reset_email', $user->email);

        $message  = "Halo Kak! Kode OTP untuk reset password Corndog-Ku kamu adalah: *{$otp}*. ";
        $message .= "Jangan berikan kode ini ke siapapun ya!";

        try {
            $response = Http::withHeaders(['Authorization' => config('services.fonnte.token')])
                ->timeout(30)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $targetPhone,
                    'message' => $message,
                ]);

            $body = $response->json();

            Log::info('Fonnte WA OTP reset', [
                'email'  => $user->email,
                'phone'  => $targetPhone,
                'status' => $response->status(),
                'body'   => $body,
            ]);

            if ($response->successful() && ($body['status'] ?? false) === true) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kode OTP telah dikirim ke WhatsApp kamu.',
                ]);
            }

            // Delivery failed — drop the cached OTP so it can't be used.
            Cache::forget($this->otpKey($user->email));

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . ($body['reason'] ?? 'Layanan WhatsApp tidak merespons.'),
            ], 502);

        } catch (\Throwable $e) {
            Cache::forget($this->otpKey($user->email));
            Log::error('Fonnte WA OTP reset error', ['email' => $user->email, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghubungi layanan WhatsApp. Coba lagi nanti.',
            ], 502);
        }
    }

    /**
     * Step 2 — verify the OTP only. On success, grant a session authorization
     * so Step 3 can set a new password without re-sending the code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->session()->get('wa_reset_email');
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi reset tidak valid. Silakan ulangi dari awal.',
            ], 403);
        }

        $cacheKey  = $this->otpKey($email);
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
            ], 422);
        }

        if (!hash_equals($cachedOtp, $request->input('otp'))) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah.',
            ], 422);
        }

        // OTP is correct — burn it and hand out a short-lived session pass.
        Cache::forget($cacheKey);
        $request->session()->put('otp_verified_email', $email);

        return response()->json([
            'success' => true,
            'message' => 'OTP terverifikasi. Silakan buat password baru.',
        ]);
    }

    /**
     * Step 3 — set the new password. Requires a verified OTP session pass for
     * the matching phone number; the pass is cleared after a successful reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->session()->get('otp_verified_email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi tidak valid. Silakan ulangi dari awal.',
            ], 403);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $request->session()->forget(['otp_verified_email', 'wa_reset_email']);
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);
        $request->session()->forget(['otp_verified_email', 'wa_reset_email']);

        return response()->json([
            'success'  => true,
            'message'  => 'Password berhasil diperbarui. Silakan masuk dengan password baru.',
            'redirect' => route('login'),
        ]);
    }

    private function otpKey(string $email): string
    {
        return 'wa_reset_otp:' . $email;
    }

    /**
     * Normalize a phone number to Fonnte's expected 62XXXXXXXXXX form.
     */
}
