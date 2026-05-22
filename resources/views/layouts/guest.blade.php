<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corndog-Ku')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased relative overflow-x-hidden" style="background-color: #FFFBEE; min-height: 100vh;">

    {{-- Red blob — top-right decorative --}}
    <div class="absolute top-0 right-0 pointer-events-none select-none hidden lg:block" style="z-index:0; overflow:hidden; width:160px; height:160px;">
        <div style="position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:var(--color-primary); border-radius:50%;"></div>
        <div style="position:absolute; top:80px; right:-10px; width:70px; height:50px; background:var(--color-primary); border-radius:0 0 40px 40px; transform:rotate(-20deg);"></div>
    </div>

    {{-- Amber accent — bottom-left --}}
    <div class="absolute bottom-0 left-0 pointer-events-none select-none hidden lg:block"
         style="z-index:0; width:200px; height:160px; background:var(--color-accent); border-radius:0 60% 0 0; opacity:0.7;"></div>

    {{-- Decorative corndog images — left side (desktop only) --}}
    <div class="absolute hidden lg:flex flex-col pointer-events-none select-none"
         style="z-index:1; left:-0.5rem; top:18%; gap:0;">
        <img src="{{ asset('assets/img/CA_CHEETOS.png') }}" alt=""
             style="width:140px; transform:rotate(8deg); filter:drop-shadow(2px 4px 8px rgba(0,0,0,0.15));">
        <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}" alt=""
             style="width:110px; transform:rotate(8deg); margin-top:-2.5rem; margin-left:3rem; opacity:0.85; filter:drop-shadow(2px 4px 8px rgba(0,0,0,0.12));">
    </div>

    {{-- Kembali link --}}
    <a href="@yield('kembali_href', url('/'))"
       class="absolute top-6 left-6 flex items-center gap-1 text-sm font-semibold z-10 hover:opacity-80 transition-opacity hidden sm:flex"
       style="color:#B20000;">
        ← Kembali
    </a>

    {{-- Page content — centred --}}
    <div class="relative flex min-h-screen items-center justify-center px-4 py-20 sm:py-16" style="z-index:2;">
        <div class="w-full max-w-sm">
            @yield('content')
        </div>
    </div>

</body>
</html>
