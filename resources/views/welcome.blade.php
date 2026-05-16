<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corndog-Ku — Beranda</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes ticker {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-track { animation: ticker 24s linear infinite; }
        .ticker-track:hover { animation-play-state: paused; }

        .product-card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .product-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.13);
            transform: translateY(-2px);
        }

        /* Hero decorative wave pattern (approximates Figma "Pattern 08") */
        .hero-pattern::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='320' height='200' viewBox='0 0 320 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 60 Q80 20 160 60 Q240 100 320 60' stroke='rgba(255,190,84,0.10)' stroke-width='4' fill='none'/%3E%3Cpath d='M0 120 Q80 80 160 120 Q240 160 320 120' stroke='rgba(255,190,84,0.08)' stroke-width='4' fill='none'/%3E%3Cpath d='M0 180 Q80 140 160 180 Q240 220 320 180' stroke='rgba(255,190,84,0.06)' stroke-width='4' fill='none'/%3E%3Ccircle cx='30' cy='40' r='3' fill='rgba(255,190,84,0.15)'/%3E%3Ccircle cx='290' cy='80' r='2' fill='rgba(255,190,84,0.12)'/%3E%3Ccircle cx='160' cy='20' r='2.5' fill='rgba(255,190,84,0.12)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-sans antialiased" style="background-color: var(--color-light); color: var(--color-black);">

{{-- ══════════════════════════════════════════════════════════════
     1. NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-6">

        {{-- Brand --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}"
                 alt="Corndog-Ku"
                 class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-lg tracking-tight" style="color: var(--color-black);">Corndog-Ku</span>
        </a>

        {{-- Desktop nav links --}}
        <nav class="hidden md:flex items-center gap-8 flex-1 justify-center">
            <a href="{{ route('welcome') }}"
               class="text-sm font-semibold"
               style="color: var(--color-primary);">Beranda</a>
            <a href="{{ route('menu') }}"
               class="text-sm font-medium transition-colors hover:opacity-70"
               style="color: var(--color-black);">Menu &amp; Varian Rasa</a>
        </nav>

        {{-- Right: cart + auth actions --}}
        <div class="flex items-center gap-2 flex-none">

            {{-- Cart --}}
            <a href="{{ route('cart') }}"
               class="relative w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                             1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold
                             flex items-center justify-center"
                      style="background-color: var(--color-accent); color: var(--color-black);">0</span>
            </a>

            @auth
                <span class="hidden sm:block text-sm font-semibold"
                      style="color: var(--color-black);">
                    Halo, {{ auth()->user()->name }}
                </span>
                <a href="{{ route('profile') }}"
                   class="w-9 h-9 rounded-full flex items-center justify-center
                          text-white text-sm font-extrabold transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary);"
                   title="{{ auth()->user()->name }}">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-semibold
                                   border transition-opacity hover:opacity-70"
                            style="border-color: var(--color-border); color: var(--color-black);">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('register') }}"
                   class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-semibold
                          border transition-colors hover:opacity-80"
                   style="border-color: var(--color-border); color: var(--color-black);">Daftar</a>
                <a href="{{ route('login') }}"
                   class="inline-flex px-4 py-2 rounded-full text-sm font-semibold
                          transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary); color: var(--color-white);">Log In</a>
            @endauth

        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════
     2. HERO SECTION
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden hero-pattern"
         style="background-color: #A6171C;
                background-image: radial-gradient(ellipse at 65% 45%, #7A0D10 0%, #A6171C 60%);">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">

            {{-- Left: text content --}}
            <div class="flex-1 text-white text-center lg:text-left">

                {{-- Top quote badge --}}
                <div class="hidden lg:inline-flex items-center gap-2 mb-5 px-4 py-2 rounded-full text-xs text-white/80"
                     style="background-color: rgba(255,255,255,0.12);">
                    <span>⭐</span>
                    <span>"Pelayanannya cepat, corndognya fresh, toppingnya gak pelit. Auto order lagi!"</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-4">
                    Crispy Corndog,<br>Happy Mood
                </h1>
                <p class="text-sm sm:text-base opacity-80 mb-8 max-w-md mx-auto lg:mx-0 leading-relaxed">
                    Nikmati corndog hangat dengan topping melimpah dan mozzarella yang lumer di setiap gigitan.
                    Dibuat fresh setiap hari untuk nemenin mood kamu kapan aja.
                </p>

                {{-- CTA + Social proof row --}}
                <div class="flex flex-col sm:flex-row items-center lg:items-start gap-5 mb-6">

                    {{-- TRY NOW — white outlined pill (matches Figma) --}}
                    <a href="#menu"
                       class="inline-flex px-8 py-3 rounded-full font-bold text-sm tracking-widest
                              border-2 transition-all hover:bg-white"
                       style="border-color: var(--color-white); color: var(--color-white);"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='var(--color-white)'">
                        TRY NOW
                    </a>

                    {{-- Social proof --}}
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2 flex-none">
                            <div class="w-9 h-9 rounded-full border-2 border-white flex items-center justify-center
                                        text-white text-xs font-bold" style="background-color: #60A5FA;">A</div>
                            <div class="w-9 h-9 rounded-full border-2 border-white flex items-center justify-center
                                        text-white text-xs font-bold" style="background-color: #A855F7;">D</div>
                            <div class="w-9 h-9 rounded-full border-2 border-white flex items-center justify-center
                                        text-white text-xs font-bold" style="background-color: #F472B6;">B</div>
                            <div class="w-9 h-9 rounded-full border-2 border-white flex items-center justify-center
                                        text-white text-xs font-bold" style="background-color: #4ADE80;">C</div>
                        </div>
                        <div class="text-xs text-white/80 leading-snug">
                            <p class="font-semibold text-white text-sm">2,500+ Happy Corndog-Ku Lovers</p>
                            <p class="flex items-center gap-1 mt-0.5">
                                <span style="color: var(--color-accent);">★★★★★</span>
                                <span>4.8</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Social icons --}}
                <div class="flex items-center gap-3 justify-center lg:justify-start opacity-70">
                    <span class="text-xs text-white">Ikuti kami:</span>
                    @foreach(['IG','TK','YT'] as $s)
                        <span class="w-7 h-7 rounded-full border border-white/50 flex items-center
                                     justify-center text-[10px] font-bold text-white cursor-pointer
                                     hover:border-white transition-colors">{{ $s }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Right: hero corndog image --}}
            <div class="flex-none flex items-end justify-center w-48 lg:w-64 self-stretch">
                <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}"
                     alt="Corndog-Ku"
                     class="w-full max-h-96 object-contain object-bottom drop-shadow-2xl">
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     3. SCROLLING TICKER
══════════════════════════════════════════════════════════════ --}}
<div class="overflow-hidden py-3.5" style="background-color: var(--color-primary);">
    <div class="ticker-track flex whitespace-nowrap">
        @for ($i = 0; $i < 2; $i++)
            <div class="flex items-center gap-0">
                @foreach(array_fill(0, 7, 'ISI HARI MU DENGAN CORNDOG') as $t)
                    <span class="flex items-center gap-6 px-6">
                        <span class="w-3 h-3 rounded-full bg-white flex-none"></span>
                        <span class="text-white font-bold text-sm tracking-widest">{{ $t }}</span>
                    </span>
                @endforeach
            </div>
        @endfor
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     4. PROMO CARDS  (2-column: left=2 stacked, right=1 large)
══════════════════════════════════════════════════════════════ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 overflow-hidden">
    <div class="flex flex-col md:flex-row gap-4 min-h-[380px]">

        {{-- Left: two stacked cards --}}
        <div class="flex flex-col gap-4 flex-1">

            {{-- Card A: Sweet & Chilly — Korean Bingsu --}}
            <div class="relative rounded-2xl overflow-hidden flex-1 min-h-[170px] flex flex-col justify-between p-5"
                 style="background-color: var(--color-accent);">
                {{-- Food photo (right) --}}
                <img src="{{ asset('assets/img/BS_STRAWBERRY_CREAMY.png') }}"
                     alt="Korean Bingsu"
                     class="absolute right-3 bottom-0 h-36 object-contain pointer-events-none">
                {{-- Tag overlay --}}
                <div class="relative z-10">
                    <p class="text-xs font-semibold" style="color: rgba(0,0,0,0.55);">Sweet &amp; Chilly</p>
                    <p class="text-2xl font-black leading-tight mt-0.5" style="color: var(--color-black);">
                        KOREAN<br>BINGSU
                    </p>
                </div>
                <div class="relative z-10 flex items-end justify-between">
                    <button class="text-xs font-bold px-4 py-1.5 rounded-full hover:opacity-80"
                            style="background-color: var(--color-primary); color: white;">
                        Order Now 🔥
                    </button>
                    <div class="text-right pr-40">
                        <p class="text-xs font-medium" style="color: rgba(0,0,0,0.5);">Up to</p>
                        <p class="text-3xl font-black leading-none" style="color: var(--color-primary);">40%</p>
                    </div>
                </div>
            </div>

            {{-- Card B: Super Cheesy Bites --}}
            <div class="relative rounded-2xl overflow-hidden flex-1 min-h-[170px] flex flex-col justify-between p-5"
                 style="background-color: var(--color-primary);">
                <img src="{{ asset('assets/img/CM_CHOCO_CHRUNCH_CHEESE.png') }}"
                     alt="Cheesy Bites"
                     class="absolute right-3 bottom-0 h-40 object-contain pointer-events-none">
                <div class="relative z-10">
                    <p class="text-xs font-semibold text-white/60">SUPER</p>
                    <p class="text-2xl font-black text-white leading-tight mt-0.5">CHEESY BITES</p>
                </div>
                <div class="relative z-10 flex items-end justify-between">
                    <button class="text-xs font-bold px-4 py-1.5 rounded-full hover:opacity-80"
                            style="background-color: var(--color-white); color: var(--color-primary);">
                        Order Now 🔥
                    </button>
                    <div class="text-right pr-40">
                        <p class="text-xs font-medium text-white/60">Up to</p>
                        <p class="text-3xl font-black text-white leading-none">50%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Loaded Korean Corndogs (large card) --}}
        <div class="relative rounded-2xl overflow-hidden md:flex-1 min-h-[380px] flex flex-col justify-between p-6"
             style="background-color: #FFF3D6;">
            <img src="{{ asset('assets/img/CA_MOZZA_POTATO.png') }}"
                 alt="Loaded Korean Corndogs"
                 class="absolute inset-0 w-full h-full object-cover"
                 style="opacity: 0.55;">
            {{-- Text overlay --}}
            <div class="relative z-10">
                <p class="text-xs font-bold tracking-widest text-gray-800/70">LOADED</p>
                <p class="text-3xl font-black leading-tight text-gray-900 mt-1">
                    KOREAN<br>CORNDOGS
                </p>
            </div>
            {{-- BUY NOW badge --}}
            <div class="relative z-10 flex justify-end">
                <div class="w-28 h-28 rounded-full bg-white shadow-lg flex flex-col items-center justify-center">
                    <p class="text-2xl font-black leading-none" style="color: var(--color-black);">BUY</p>
                    <p class="text-2xl font-black leading-none" style="color: var(--color-black);">NOW</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     5. CUSTOMIZE CORNDOG — CTA BANNER
══════════════════════════════════════════════════════════════ --}}
<section class="py-8 sm:py-10" style="background-color: var(--color-light);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('customize') }}"
           class="group relative flex flex-col sm:flex-row items-center justify-between gap-0 sm:gap-6
                  rounded-3xl overflow-hidden no-underline
                  transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
           style="background-color: var(--color-accent);
                  box-shadow: 0 8px 32px rgba(166,23,28,0.15);">

            {{-- Decorative scattered dots --}}
            <div class="absolute top-4 left-8 w-3 h-3 rounded-full pointer-events-none opacity-30"
                 style="background-color: var(--color-primary);"></div>
            <div class="absolute top-10 left-20 w-2 h-2 rounded-full pointer-events-none opacity-20"
                 style="background-color: var(--color-primary);"></div>
            <div class="absolute bottom-6 left-[38%] w-2.5 h-2.5 rounded-full pointer-events-none opacity-25"
                 style="background-color: var(--color-primary);"></div>
            <div class="absolute top-6 right-[30%] w-2 h-2 rounded-full pointer-events-none opacity-20"
                 style="background-color: var(--color-primary);"></div>

            {{-- Wavy SVG background pattern --}}
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-10"
                 preserveAspectRatio="none" viewBox="0 0 800 240"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120 Q200 60 400 120 Q600 180 800 120 L800 240 L0 240 Z"
                      fill="#A6171C"/>
                <path d="M0 160 Q200 100 400 160 Q600 220 800 160 L800 240 L0 240 Z"
                      fill="#A6171C" opacity="0.5"/>
            </svg>

            {{-- LEFT: Text content --}}
            <div class="relative z-10 flex-1 px-8 sm:px-12 pt-10 pb-6 sm:py-10 text-center sm:text-left">

                {{-- Badge pill --}}
                <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold mb-5"
                     style="background-color: rgba(0,0,0,0.12); color: var(--color-black);">
                    ✦ Hanya di Corndog-Ku
                </div>

                {{-- Big title --}}
                <div class="leading-none tracking-tight mb-1">
                    <span class="block text-5xl sm:text-6xl font-black"
                          style="color: var(--color-black);">CUSTOM</span>
                    <span class="block text-5xl sm:text-6xl font-black"
                          style="color: var(--color-primary);">CORNDOG</span>
                </div>

                {{-- Wavy underline SVG --}}
                <svg class="mt-1 mb-4 mx-auto sm:mx-0" width="200" height="12"
                     viewBox="0 0 200 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 6 Q25 2 48 6 Q71 10 94 6 Q117 2 140 6 Q163 10 186 6 Q194 4 198 6"
                          stroke="#A6171C" stroke-width="3" stroke-linecap="round" fill="none"/>
                </svg>

                {{-- Subtitle --}}
                <p class="text-sm sm:text-base font-semibold mb-7 max-w-xs mx-auto sm:mx-0"
                   style="color: rgba(0,0,0,0.60);">
                    Buat Corndog Kustom-mu Sendiri —
                    pilih isi, varian, dan saos sesuai seleramu!
                </p>

                {{-- CTA button --}}
                <div class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full
                            font-bold text-sm text-white
                            transition-all duration-200 group-hover:scale-105 group-hover:shadow-lg"
                     style="background-color: var(--color-primary);">
                    Mulai Buat Sekarang
                    <svg class="w-4 h-4 flex-none" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12"/>
                    </svg>
                </div>
            </div>

            {{-- RIGHT: Corndog image + blob --}}
            <div class="relative z-10 flex-none flex items-end justify-center
                        px-8 sm:px-10 pb-0 sm:pb-0 pt-4 sm:pt-0">

                {{-- Organic peach blob behind image --}}
                <div class="relative w-52 h-52 sm:w-64 sm:h-64 lg:w-72 lg:h-72 flex items-end justify-center"
                     style="border-radius: 58% 42% 46% 54% / 52% 44% 56% 48%;
                            background-color: #FDECD8;">

                    {{-- Spark accent --}}
                    <span class="absolute top-2 right-3 text-2xl pointer-events-none"
                          style="color: var(--color-primary);">✦</span>

                    <img src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
                         alt="Custom Corndog"
                         class="relative w-44 h-44 sm:w-56 sm:h-56 lg:w-64 lg:h-64 object-contain drop-shadow-2xl
                                transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-2">
                </div>

                {{-- Small sparkle outside blob --}}
                <span class="absolute bottom-8 right-4 sm:right-8 text-base pointer-events-none opacity-50"
                      style="color: var(--color-primary);">✦</span>
            </div>

        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     6. MENU CATEGORIES + PRODUCT GRID  (id="menu")
══════════════════════════════════════════════════════════════ --}}
<section id="menu" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-16">

    <h2 class="text-2xl font-bold text-center mb-6" style="color: var(--color-black);">
        Menu Categories
    </h2>

    {{-- Category pills — horizontally scrollable on mobile --}}
    <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex gap-2 min-w-max sm:min-w-0 sm:flex-wrap justify-center">
            <button type="button"
                    class="cat-tab px-5 py-2 rounded-full text-sm font-semibold border whitespace-nowrap transition-all hover:opacity-80 active"
                    data-cat="Semua"
                    style="background-color: var(--color-primary); color: white; border-color: var(--color-primary);">
                Semua
            </button>
            @foreach ($categories as $cat)
                <button type="button"
                        class="cat-tab px-5 py-2 rounded-full text-sm font-semibold border whitespace-nowrap transition-all hover:opacity-80"
                        data-cat="{{ $cat }}"
                        style="background-color: white; color: var(--color-black); border-color: var(--color-border);">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Active label + result count --}}
    <div class="flex items-center justify-between mt-5 mb-4">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Menampilkan</span>
            <span id="active-cat-label" class="ml-1 text-xs font-bold" style="color: var(--color-primary);">Semua</span>
        </div>
        <span id="result-count" class="text-xs text-gray-400 font-medium">
            {{ $products->count() }} produk
        </span>
    </div>

    {{-- Product grid --}}
    <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach ($products as $product)
            <div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden cursor-pointer
                        hover:shadow-xl transition-all duration-200"
                 data-category="{{ $product->category->name }}"
                 data-price="{{ $product->price }}"
                 data-name="{{ strtolower($product->name) }}"
                 style="box-shadow: var(--shadow-card);">

                {{-- Image: uniform crop locked into card top --}}
                <div class="overflow-hidden rounded-t-2xl">
                    <img src="{{ asset($product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-48 object-cover rounded-t-2xl transition-transform duration-300 hover:scale-105"
                         onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                </div>

                {{-- Text area --}}
                <div class="px-4 pt-3 pb-4 flex flex-col flex-1">
                    <p class="font-bold text-sm leading-snug" style="color: var(--color-primary);">
                        {{ $product->name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">
                        {{ $product->description }}
                    </p>
                    <div class="flex items-center justify-between mt-3 gap-2">
                        <p class="text-sm font-black" style="color: var(--color-primary);">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                        <button type="button"
                                class="btn-pesan flex-none px-3 py-1 rounded-full text-xs font-bold transition-opacity hover:opacity-80"
                                style="background-color: var(--color-accent); color: var(--color-black);"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-description="{{ $product->description }}"
                                data-image="{{ asset($product->image) }}">
                            Pesan
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty state --}}
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="text-5xl mb-4">🌽</div>
        <p class="font-bold text-lg" style="color: var(--color-black);">Produk tidak ditemukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba pilih kategori lain.</p>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════════
     7. LOCATION & HOURS
══════════════════════════════════════════════════════════════ --}}
<section class="py-16" style="background-color: var(--color-white);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

            {{-- Map embed --}}
            <div class="rounded-2xl overflow-hidden min-h-[300px]"
                 style="box-shadow: var(--shadow-card);">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.9!2d112.76!3d-7.32!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMTknMTIuMCJTIDExMsKwNDUnMzYuMCJF!5e0!3m2!1sen!2sid!4v1"
                    class="w-full h-full min-h-[300px]"
                    style="border: 0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            {{-- Hours table --}}
            <div class="rounded-2xl p-6"
                 style="background-color: var(--color-primary); box-shadow: var(--shadow-card);">
                <h3 class="text-xl font-bold text-white mb-5">Location &amp; Hours</h3>
                @php
                    $hours = [
                        'Monday'    => '13:00 – 21:00',
                        'Tuesday'   => '13:00 – 21:00',
                        'Wednesday' => '13:00 – 21:00',
                        'Thursday'  => '13:00 – 21:00',
                        'Friday'    => '13:00 – 21:00',
                        'Saturday'  => '13:00 – 21:00',
                        'Sunday'    => '13:00 – 21:00',
                    ];
                    $today = now()->format('l');
                @endphp
                <div class="flex flex-col gap-1">
                    @foreach ($hours as $day => $time)
                        <div class="flex justify-between items-center px-3 py-2 rounded-lg text-sm
                                    {{ $day === $today ? 'font-bold' : '' }}"
                             style="{{ $day === $today
                                 ? 'background-color: rgba(255,255,255,0.20); color: white;'
                                 : 'color: rgba(255,255,255,0.70);' }}">
                            <span>{{ $day }}</span>
                            <span>{{ $time }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-white/55 mt-4">
                    Jl. Rungkut Mejoyo Utara No.61, Surabaya
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     8. TESTIMONIALS — "Your trust in us"
══════════════════════════════════════════════════════════════ --}}
<section class="py-16" style="background-color: #FFF9E6;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Title: white pill badge, left-aligned (matches Figma) --}}
        <div class="mb-8">
            <span class="inline-block bg-white font-bold text-lg px-5 py-2 rounded-full shadow-sm"
                  style="color: var(--color-black);">Your trust in us</span>
        </div>

        @php
            $reviews = [
                ['name' => 'Michael Lee',   'date' => 'December 10, 2023', 'text' => 'The best service I\'ve received in a long time! The team was incredibly responsive, and their expertise was evident in the final product. There was a small miscommunication initially, but they quickly resolved it. I would definitely work with them again.'],
                ['name' => 'Sari Dewi',     'date' => 'January 5, 2024',   'text' => 'The best service I\'ve received in a long time! The team was incredibly responsive, and their expertise was evident in the final product. There was a small miscommunication initially, but they quickly resolved it. I would definitely work with them again.'],
                ['name' => 'Budi Santoso',  'date' => 'February 14, 2024', 'text' => 'The best service I\'ve received in a long time! The team was incredibly responsive, and their expertise was evident in the final product. There was a small miscommunication initially, but they quickly resolved it. I would definitely work with them again.'],
                ['name' => 'Rina Wijaya',   'date' => 'March 2, 2024',     'text' => 'The best service I\'ve received in a long time! The team was incredibly responsive, and their expertise was evident in the final product. There was a small miscommunication initially, but they quickly resolved it. I would definitely work with them again.'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($reviews as $review)
                <div class="bg-white rounded-2xl p-5 flex flex-col gap-3"
                     style="box-shadow: 0 2px 12px rgba(0,0,0,0.07);">

                    {{-- Header row: avatar + name/date + Google G --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white
                                        font-bold text-sm flex-none"
                                 style="background-color: var(--color-primary);">
                                {{ substr($review['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm leading-tight"
                                   style="color: var(--color-black);">{{ $review['name'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $review['date'] }}</p>
                            </div>
                        </div>
                        {{-- Google G icon --}}
                        <svg class="w-5 h-5 flex-none mt-0.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>

                    {{-- Gold stars --}}
                    <div class="flex gap-0.5">
                        @for ($i = 0; $i < 5; $i++)
                            <span class="text-base" style="color: var(--color-accent);">★</span>
                        @endfor
                    </div>

                    {{-- Review text --}}
                    <p class="text-xs leading-relaxed text-gray-600 flex-1">{{ $review['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     9. FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer style="background-color: var(--color-primary);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            {{-- Brand column --}}
            <div>
                <img src="{{ asset('assets/img/logo.png') }}"
                     alt="Corndog-Ku"
                     class="w-14 h-14 rounded-full object-cover border-2 border-white/30 mb-4">
                <p class="text-white font-bold text-2xl leading-snug mb-1">
                    Beli dimana saja,<br>pesan kapan saja
                </p>
                <p class="text-white/70 text-sm font-semibold mt-3 mb-3">Tersedia Order Online</p>
                {{-- Delivery app badges --}}
                <div class="flex gap-3">
                    <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 shadow-sm">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                             style="background-color: #EE4D2D;">S</div>
                        <span class="text-xs font-bold" style="color: #EE4D2D;">ShopeeFood</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 shadow-sm">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                             style="background-color: #00B14F;">G</div>
                        <span class="text-xs font-bold" style="color: #00B14F;">GrabFood</span>
                    </div>
                </div>
            </div>

            {{-- Contact Us --}}
            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Contact Us</h4>
                <ul class="space-y-2 text-sm text-white/70">
                    <li>@corndogku_id</li>
                    <li>+62 823-2511-0652</li>
                    <li class="pt-1">Jl. Rungkut Mejoyo Utara No.61, Surabaya</li>
                </ul>
            </div>

            {{-- Follow Us --}}
            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Follow Us</h4>
                <div class="flex items-center gap-3">
                    {{-- WhatsApp --}}
                    <a href="#"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center
                              hover:border-white transition-colors"
                       aria-label="WhatsApp">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center
                              hover:border-white transition-colors"
                       aria-label="Instagram">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3
                    text-xs text-white/40"
             style="border-top: 1px solid rgba(255,255,255,0.15);">
            <span>&copy; {{ date('Y') }} Corndog-Ku. All rights reserved.</span>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                <span class="text-white/20">|</span>
                <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

{{-- ══════════════════════════════════════════════════════════════
     PRODUCT DETAIL MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="product-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden transition-opacity duration-300" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">

    {{-- Modal box --}}
    <div id="product-modal-box" class="bg-white rounded-3xl overflow-hidden shadow-[0_0_60px_rgba(0,0,0,0.5)] border border-gray-700 w-full max-w-md flex flex-col relative transform scale-100">

        {{-- X close button --}}
        <button type="button" id="modal-close"
                class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center
                       rounded-full bg-white/80 hover:bg-gray-100 transition-colors"
                aria-label="Tutup">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Product image --}}
        <img id="modal-image" src="" alt="" class="w-full h-56 object-cover">

        {{-- Content --}}
        <div class="p-6 flex flex-col gap-4">

            <div>
                <h3 id="modal-title"
                    class="text-xl font-bold leading-tight"
                    style="color: var(--color-black);"></h3>
                <p id="modal-price"
                   class="text-base font-semibold mt-1"
                   style="color: var(--color-primary);"></p>
            </div>

            <p id="modal-description"
               class="text-sm leading-relaxed"
               style="color: #525252;"></p>

            {{-- Quantity selector --}}
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold" style="color: var(--color-black);">Quantity</span>
                <div class="flex items-center gap-2 px-2 py-1.5 rounded-[8px]"
                     style="background-color: rgba(255,203,99,0.24); border: 1px solid #ffcb63;">
                    <button type="button" id="modal-qty-minus"
                            class="w-7 h-7 rounded-full flex items-center justify-center
                                   font-bold text-base leading-none hover:opacity-70"
                            style="background-color: #ffcb63; color: #525252;">
                        &#8722;
                    </button>
                    <span id="modal-qty"
                          class="w-7 text-center font-bold text-sm"
                          style="color: #3d3d3d;">1</span>
                    <button type="button" id="modal-qty-plus"
                            class="w-7 h-7 rounded-full flex items-center justify-center
                                   font-bold text-base leading-none hover:opacity-70"
                            style="background-color: var(--color-primary); color: white;">
                        +
                    </button>
                </div>
            </div>

            {{-- CTA button --}}
            <button type="button" id="modal-btn-cart"
                    class="w-full py-3 font-bold text-sm text-white hover:opacity-90 transition-opacity"
                    style="border-radius: 12px; background-color: var(--color-primary);">
                Tambah ke Keranjang
            </button>

        </div>{{-- /.content --}}

    </div>{{-- /#product-modal-box --}}

</div>{{-- /#product-modal --}}

<script>
$(function () {
    var activeCat = 'Semua';

    function applyFilters() {
        var visible = 0;
        $('#product-grid .product-card').each(function () {
            var cat  = $(this).data('category');
            var show = (activeCat === 'Semua') || (cat === activeCat);
            $(this).toggleClass('hidden', !show);
            if (show) visible++;
        });
        $('#result-count').text(visible + ' produk');
        $('#empty-state').toggleClass('hidden', visible > 0);
    }

    $(document).on('click', '.cat-tab', function () {
        activeCat = $(this).data('cat');
        $('.cat-tab').removeClass('active')
                     .css({ 'background-color': 'white', 'color': 'var(--color-black)', 'border-color': 'var(--color-border)' });
        $(this).addClass('active')
               .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'border-color': 'var(--color-primary)' });
        $('#active-cat-label').text(activeCat);
        applyFilters();
    });

    applyFilters();

    /* ══════════════════════════════════════════════════════════
       PRODUCT DETAIL MODAL
    ══════════════════════════════════════════════════════════ */
    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    $(document).on('click', '.btn-pesan', function () {
        var $b = $(this);
        $('#modal-title').text($b.data('name'));
        $('#modal-price').text(fmtRp(parseInt($b.data('price'), 10) || 0) + ' / pcs');
        $('#modal-description').text($b.data('description'));
        $('#modal-image').attr({ src: $b.data('image'), alt: $b.data('name') });
        $('#modal-qty').text('1');
        $('#product-modal').removeClass('hidden').addClass('flex');
        $('body').css('overflow', 'hidden');
    });

    $('#modal-qty-plus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        $('#modal-qty').text(q + 1);
    });

    $('#modal-qty-minus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        if (q > 1) $('#modal-qty').text(q - 1);
    });

    function closeModal() {
        $('#product-modal').addClass('hidden').removeClass('flex');
        $('body').css('overflow', '');
    }

    $('#modal-close').on('click', closeModal);
    $('#product-modal').on('click', function (e) {
        if (!$(e.target).closest('#product-modal-box').length) closeModal();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

});
</script>

</body>
</html>
