@extends('layouts.guest')

@section('title', 'Log In — Corndog-Ku')

@section('content')

{{-- White form card --}}
<div class="bg-white rounded-2xl px-8 py-10"
     style="box-shadow: var(--shadow-card);">

    <h1 class="text-2xl font-bold text-center mb-7" style="color: var(--color-black);">
        Log In
    </h1>

    @if (session('error'))
        <div class="mb-4 text-sm text-red-600 bg-red-50 rounded-lg px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-4">
        @csrf

        {{-- Email or Username --}}
        <div>
            <input type="text"
                   name="email"
                   placeholder="Email atau Username"
                   value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   class="w-full px-4 py-3 rounded-lg text-sm outline-none transition-colors"
                   style="border: 1px solid var(--color-border);
                          background-color: var(--color-white);
                          color: var(--color-black);">
            @error('email')
                <p class="mt-1 text-xs" style="color: var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <input type="password"
                   name="password"
                   placeholder="Password"
                   required
                   class="w-full px-4 py-3 rounded-lg text-sm outline-none transition-colors"
                   style="border: 1px solid var(--color-border);
                          background-color: var(--color-white);
                          color: var(--color-black);">
            @error('password')
                <p class="mt-1 text-xs" style="color: var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 rounded-full font-bold text-sm tracking-widest
                       transition-opacity hover:opacity-90 mt-1"
                style="background-color: var(--color-primary); color: var(--color-white);">
            LOG IN
        </button>
    </form>

    {{-- Lupa Password --}}
    <div class="mt-3 text-center">
        <a href="#"
           class="text-xs font-medium hover:underline"
           style="color: var(--color-primary);">
            Lupa Password
        </a>
    </div>

    {{-- ATAU divider --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px" style="background-color: var(--color-border);"></div>
        <span class="text-xs font-semibold" style="color: #9c9c9c;">ATAU</span>
        <div class="flex-1 h-px" style="background-color: var(--color-border);"></div>
    </div>

    {{-- Social buttons --}}
    <div class="flex gap-3">
        <button type="button"
                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg
                       text-sm font-medium transition-colors hover:bg-gray-50"
                style="border: 1px solid var(--color-border); color: var(--color-black);">
            {{-- Google icon --}}
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
        </button>

        <button type="button"
                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg
                       text-sm font-medium transition-colors hover:bg-gray-50"
                style="border: 1px solid var(--color-border); color: var(--color-black);">
            {{-- Apple icon --}}
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98l-.09.06c-.22.16-2.24 1.31-2.22 3.91.03 3.1 2.72 4.13 2.75 4.14l-.08.57zM13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            Apple
        </button>
    </div>

    {{-- Register link --}}
    <p class="text-center text-sm mt-6" style="color: #555;">
        Baru di Corndog-Ku?&nbsp;
        <a href="{{ route('register') }}"
           class="font-semibold hover:underline"
           style="color: var(--color-primary);">
            Daftar
        </a>
    </p>
</div>

@endsection
