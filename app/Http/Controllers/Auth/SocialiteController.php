<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                $base     = Str::slug(explode('@', $googleUser->getEmail())[0], '');
                $username = $base ?: 'user';

                $user = null;
                do {
                    try {
                        $user = User::create([
                            'name'      => $googleUser->getName(),
                            'email'     => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'avatar'    => $googleUser->getAvatar(),
                            'password'  => bcrypt(Str::random(24)),
                            'role'      => 'customer',
                            'status'    => 'active',
                            'username'  => $username,
                        ]);
                    } catch (QueryException $e) {
                        if ($e->errorInfo[1] !== 1062) throw $e;
                        $username = ($base ?: 'user') . Str::random(4);
                    }
                } while ($user === null);
            }
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}
