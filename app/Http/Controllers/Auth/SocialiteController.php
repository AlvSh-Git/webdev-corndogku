<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

// Google SSO via Laravel Socialite.
class SocialiteController extends Controller
{
    // Start the Google OAuth redirect.
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Log in existing users, or start onboarding for new ones.
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        // Returning user — link the Google account if needed and log them in.
        if ($user) {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, true);

            return redirect()->intended('/');
        }

        // New user — defer DB creation until they complete their profile
        // (mandatory phone + password). Stash the Google data in the session.
        session(['google_user' => [
            'name'      => $googleUser->getName(),
            'email'     => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar'    => $googleUser->getAvatar(),
        ]]);

        return redirect()->route('register.complete');
    }

    /**
     * Show the "complete your profile" form for a brand-new Google sign-in.
     */
    public function showCompleteProfile()
    {
        $google = session('google_user');

        if (!$google) {
            return redirect()->route('login')
                ->with('error', 'Sesi pendaftaran Google sudah berakhir. Silakan coba lagi.');
        }

        return view('auth.complete-profile', ['google' => $google]);
    }

    /**
     * Persist the new Google user once they supply a phone and password.
     */
    public function completeProfile(Request $request)
    {
        $google = session('google_user');

        if (!$google) {
            return redirect()->route('login')
                ->with('error', 'Sesi pendaftaran Google sudah berakhir. Silakan coba lagi.');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $base     = Str::slug(explode('@', $google['email'])[0], '');
        $username = $base ?: 'user';

        $user = null;
        do {
            try {
                $user = User::create([
                    'name'      => $request->name,
                    'username'  => $username,
                    'email'     => $google['email'],
                    'phone'     => $request->phone,
                    'google_id' => $google['google_id'] ?? null,
                    'avatar'    => $google['avatar'] ?? null,
                    'password'  => bcrypt($request->password),
                    'role'      => 'customer',
                    'status'    => 'active',
                ]);
            } catch (QueryException $e) {
                if ($e->errorInfo[1] !== 1062) throw $e;
                $username = ($base ?: 'user') . Str::random(4);
            }
        } while ($user === null);

        Auth::login($user, true);
        $request->session()->forget('google_user');
        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
