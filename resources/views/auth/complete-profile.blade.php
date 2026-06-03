<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lengkapi Profil — Corndog-Ku</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen relative text-gray-900 font-sans overflow-hidden bg-[#FEFDF2]">

    {{-- Background texture --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/login-logout_background.png') }}"
             alt="" class="w-full h-full object-cover" aria-hidden="true">
    </div>

    {{-- Back --}}
    <a href="{{ route('login') }}"
       class="absolute top-6 left-6 md:left-10 text-red-700 hover:text-red-900 flex items-center gap-2 z-50 font-bold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    {{-- Centred card --}}
    <div class="absolute inset-0 flex flex-col justify-center items-center z-30 px-4 py-10">
        <div class="max-w-md w-full bg-white p-8 md:p-10 rounded-2xl shadow-xl">

            <div class="text-center mb-6">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku" class="h-10 w-auto mx-auto mb-4">
                <h1 class="text-3xl font-helvetica font-semibold text-gray-900">Lengkapi Profil</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Satu langkah lagi! Lengkapi data berikut untuk menyelesaikan pendaftaran.
                </p>
            </div>

            {{-- Flash error --}}
            @if (session('error'))
                <div class="mb-5 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('register.complete.post') }}" method="POST">
                @csrf
                <div class="space-y-3.5">

                    {{-- Nama --}}
                    <div>
                        <input type="text" name="name" placeholder="Nama Lengkap"
                               value="{{ old('name', $google['name'] ?? '') }}" required autofocus
                               class="w-full px-4 py-2.5 border rounded-md text-sm outline-none focus:border-red-400 transition-colors
                                      @error('name') border-red-400 @else border-gray-200 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email (readonly — from Google) --}}
                    <div>
                        <input type="email" name="email" placeholder="Email"
                               value="{{ $google['email'] ?? '' }}" readonly
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none
                                      bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div>
                        <input type="tel" name="phone" placeholder="Nomor WhatsApp"
                               value="{{ old('phone') }}" required
                               class="w-full px-4 py-2.5 border rounded-md text-sm outline-none focus:border-red-400 transition-colors
                                      @error('phone') border-red-400 @else border-gray-200 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <input type="password" name="password" placeholder="Password"
                               required
                               class="w-full px-4 py-2.5 border rounded-md text-sm outline-none focus:border-red-400 transition-colors
                                      @error('password') border-red-400 @else border-gray-200 @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                               required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none focus:border-red-400 transition-colors">
                    </div>

                </div>

                <button type="submit"
                        class="w-full mt-6 bg-[#B82B21] text-white font-bold py-2.5 px-4 rounded-md
                               hover:bg-red-800 transition-colors tracking-widest text-sm">
                    SELESAIKAN PENDAFTARAN
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-gray-600">
                Bukan kamu?
                <a href="{{ route('login') }}" class="text-red-700 font-bold hover:underline">Masuk dengan akun lain</a>
            </div>
        </div>
    </div>

    {{-- Decorative corndog images — large, anchored to bottom corners --}}
    <div class="absolute bottom-0 left-0 z-20 hidden lg:block origin-bottom-left">
        <img src="{{ asset('assets/img/login-logout_corndog_01.png') }}"
             alt="" class="h-[400px] xl:h-[550px] 2xl:h-[650px] w-auto drop-shadow-2xl"
             aria-hidden="true">
    </div>
    <div class="absolute bottom-0 left-12 xl:left-20 z-10 hidden lg:block origin-bottom-left">
        <img src="{{ asset('assets/img/login-logout_corndog_02.png') }}"
             alt="" class="h-[300px] xl:h-[450px] 2xl:h-[500px] w-auto drop-shadow-xl opacity-90"
             aria-hidden="true">
    </div>
    <div class="absolute bottom-0 right-0 z-20 hidden lg:block origin-bottom-right">
        <img src="{{ asset('assets/img/login-logout_corndog_03.png') }}"
             alt="" class="h-[350px] xl:h-[500px] 2xl:h-[600px] w-auto drop-shadow-2xl"
             aria-hidden="true">
    </div>

</div>

</body>
</html>
