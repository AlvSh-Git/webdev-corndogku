<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corndog-Ku')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

@php $isLogin = request()->routeIs('login'); @endphp

<div class="flex min-h-screen" style="background-color: var(--color-light);">

    {{-- ════════════════════════════════════════════════════════════
         LEFT DECORATIVE PANEL  (hidden on mobile)
         Matches Figma "login page 2" / "daftar" left half:
         amber background + two stacked red chevron shapes + auth tabs
    ════════════════════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden flex-col"
         style="background-color: var(--color-accent);">

        {{-- Circular logo — top-left --}}
        <div class="absolute top-6 left-6 z-20">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('assets/img/logo.png') }}"
                     alt="Corndog-Ku"
                     class="w-14 h-14 rounded-full object-cover border-2 border-white">
            </a>
        </div>

        {{-- Red chevron — back layer --}}
        <div class="absolute inset-0 z-0"
             style="background-color: var(--color-primary);
                    clip-path: polygon(38% 0%, 100% 0%, 100% 100%, 38% 100%, 5% 50%);"></div>

        {{-- Red chevron — front layer (slightly inset, lighter overlap) --}}
        <div class="absolute inset-0 z-0"
             style="background-color: #8C1217;
                    clip-path: polygon(52% 0%, 100% 0%, 100% 100%, 52% 100%, 20% 50%);
                    opacity: 0.85;"></div>

        {{-- Auth tab pills — vertically centred on the amber strip --}}
        <div class="relative z-10 flex flex-col gap-3 justify-center flex-1"
             style="padding-left: 14%;">

            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center px-8 py-3 rounded-full
                      font-bold text-base tracking-wide transition-all w-36"
               style="{{ $isLogin
                   ? 'background-color: var(--color-white); color: var(--color-black);'
                   : 'background-color: var(--color-accent);   color: var(--color-black);' }}">
                LOG IN
            </a>

            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center px-8 py-3 rounded-full
                      font-bold text-base tracking-wide transition-all w-36"
               style="{{ !$isLogin
                   ? 'background-color: var(--color-white); color: var(--color-black);'
                   : 'background-color: var(--color-accent);   color: var(--color-black);' }}">
                DAFTAR
            </a>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         RIGHT CONTENT AREA — white card centred
    ════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-sm">
            @yield('content')
        </div>
    </div>

</div>

</body>
</html>
