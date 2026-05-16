<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('email');

        // Accept both email and username in the same field
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $identifier, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email/username atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register');
    }

    private function redirectByRole($user)
    {
        return match ($user->role) {
            'owner'   => redirect()->route('owner.dashboard'),
            'cashier' => redirect()->route('cashier.dashboard'),
            default   => redirect('/'),
        };
    }
}
