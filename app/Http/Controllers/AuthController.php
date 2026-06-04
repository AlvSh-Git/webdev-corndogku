<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        if ($request->filled('redirect_to')) {
            $safe = $this->safeRedirectUrl($request->query('redirect_to'));
            if ($safe) {
                session(['url.intended' => $safe]);
            }
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('login');
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $identifier, 'password' => $request->password], $request->boolean('remember'))) {
            // Block deactivated accounts (e.g. a cashier set to "Non Active"
            // in User Maintenance) from logging in.
            if (Auth::user()->status !== 'active') {
                Auth::logout();
                return back()
                    ->withErrors(['login' => 'Akun Anda non-aktif. Silakan hubungi pemilik toko.'])
                    ->withInput($request->only('login', 'remember'));
            }

            $request->session()->regenerate();

            $user = Auth::user();
                if ($user && $user->cart_data) {
                    $savedCart = json_decode($user->cart_data, true);
                    if (is_array($savedCart)) {
                        // Masukkan kembali array cart ke dalam session
                        session()->put('cart', $savedCart);
                    }
                    // Kosongkan kembali kolom di database setelah di-restore
                    $user->update(['cart_data' => null]);
                }

            if (Auth::user()->role === 'customer') {
                return redirect()->intended('/');
            }

            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withErrors(['login' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.'])
            ->withInput($request->only('login', 'remember'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if ($user) {
            $user->update([
                'cart_data' => !empty($cart) ? json_encode($cart) : null
            ]);
         }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        if ($request->filled('redirect_to')) {
            $safe = $this->safeRedirectUrl($request->query('redirect_to'));
            if ($safe) {
                session(['url.intended' => $safe]);
            }
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $base     = Str::slug(explode('@', $request->email)[0], '');
        $username = $base ?: 'user';

        $user = null;
        do {
            try {
                $user = User::create([
                    'name'     => $request->name,
                    'username' => $username,
                    'email'    => $request->email,
                    'phone'    => $request->phone,
                    'password' => bcrypt($request->password),
                    'role'     => 'customer',
                    'status'   => 'active',
                ]);
            } catch (QueryException $e) {
                if ($e->errorInfo[1] !== 1062) throw $e;
                $username = ($base ?: 'user') . Str::random(4);
            }
        } while ($user === null);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    private function redirectByRole($user)
    {
        return match ($user->role) {
            'owner'            => redirect()->route('owner.dashboard'),
            'cashier', 'employee' => redirect()->route('cashier.dashboard'),
            default            => redirect('/'),
        };
    }

    private function safeRedirectUrl(?string $url): ?string
    {
        if (!$url) return null;
        $parsed  = parse_url($url);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (isset($parsed['host']) && $parsed['host'] !== $appHost) {
            return null;
        }
        return $url;
    }
}
