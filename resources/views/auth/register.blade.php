@extends('layouts.guest')

@section('title', 'Daftar — Corndog-Ku')
@section('kembali_href', route('login'))

@section('content')

<div class="bg-white px-8 py-10 w-full"
     style="border-radius:20px; box-shadow:3px 4px 10px rgba(0,0,0,0.25);">

    <h1 class="text-3xl font-normal text-black mb-6 text-center">Daftar</h1>

    @if (session('error'))
        <div class="mb-4 text-sm text-red-600 bg-red-50 rounded-lg px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="flex flex-col gap-3">
        @csrf

        {{-- Nama --}}
        <div>
            <input type="text"
                   name="name"
                   placeholder="Nama"
                   value="{{ old('name') }}"
                   required autofocus
                   class="w-full px-3 py-2.5 text-sm text-gray-700 outline-none transition-colors focus:border-gray-400 placeholder-[#BDBDBD]"
                   style="border:1px solid #D9D9D9; border-radius:5px; background:#fff;">
            @error('name')
                <p class="mt-1 text-xs" style="color:var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <input type="email"
                   name="email"
                   placeholder="Email"
                   value="{{ old('email') }}"
                   required
                   class="w-full px-3 py-2.5 text-sm text-gray-700 outline-none transition-colors focus:border-gray-400 placeholder-[#BDBDBD]"
                   style="border:1px solid #D9D9D9; border-radius:5px; background:#fff;">
            @error('email')
                <p class="mt-1 text-xs" style="color:var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <input type="tel"
                   name="phone"
                   placeholder="Nomor Telepon"
                   value="{{ old('phone') }}"
                   class="w-full px-3 py-2.5 text-sm text-gray-700 outline-none transition-colors focus:border-gray-400 placeholder-[#BDBDBD]"
                   style="border:1px solid #D9D9D9; border-radius:5px; background:#fff;">
            @error('phone')
                <p class="mt-1 text-xs" style="color:var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <input type="password"
                   name="password"
                   placeholder="Password"
                   required
                   class="w-full px-3 py-2.5 text-sm text-gray-700 outline-none transition-colors focus:border-gray-400 placeholder-[#BDBDBD]"
                   style="border:1px solid #D9D9D9; border-radius:5px; background:#fff;">
            @error('password')
                <p class="mt-1 text-xs" style="color:var(--color-danger);">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <input type="password"
                   name="password_confirmation"
                   placeholder="Confirm Password"
                   required
                   class="w-full px-3 py-2.5 text-sm text-gray-700 outline-none transition-colors focus:border-gray-400 placeholder-[#BDBDBD]"
                   style="border:1px solid #D9D9D9; border-radius:5px; background:#fff;">
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-2.5 text-white text-sm font-bold tracking-widest transition-opacity hover:opacity-90 mt-1"
                style="background:var(--color-primary); border-radius:5px;">
            BERIKUTNYA
        </button>
    </form>

    {{-- ATAU divider --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-[#D9D9D9]"></div>
        <span class="text-xs font-semibold" style="color:rgba(0,0,0,0.25);">ATAU</span>
        <div class="flex-1 h-px bg-[#D9D9D9]"></div>
    </div>

    {{-- Google register --}}
    <a href="{{ route('auth.google') }}"
       class="w-full flex items-center justify-center gap-3 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
       style="border:1px solid #D9D9D9; border-radius:5px;">
        <svg class="w-5 h-5 flex-none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Sign up with Google
    </a>

    {{-- Login link --}}
    <p class="text-center text-sm font-medium mt-6 text-black">
        Punya akun?&nbsp;
        <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color:var(--color-primary);">
            Log In
        </a>
    </p>

</div>

@endsection
