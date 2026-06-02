<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Corndog-Ku — Beranda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Londrina+Shadow&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .swal2-container { z-index: 999999 !important; }
        @keyframes ticker {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-track { animation: ticker 28s linear infinite; }
        .ticker-track:hover { animation-play-state: paused; }

        .product-card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .product-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.13);
            transform: translateY(-2px);
        }

        /* Hero Pattern 08 — wave overlay */
        .hero-pattern::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='400' height='250' viewBox='0 0 400 250' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 60 Q100 20 200 60 Q300 100 400 60' stroke='rgba(255,190,84,0.08)' stroke-width='3' fill='none'/%3E%3Cpath d='M0 120 Q100 80 200 120 Q300 160 400 120' stroke='rgba(255,190,84,0.06)' stroke-width='3' fill='none'/%3E%3Cpath d='M0 180 Q100 140 200 180 Q300 220 400 180' stroke='rgba(255,190,84,0.05)' stroke-width='3' fill='none'/%3E%3Ccircle cx='40' cy='40' r='3' fill='rgba(255,190,84,0.10)'/%3E%3Ccircle cx='360' cy='100' r='2' fill='rgba(255,190,84,0.10)'/%3E%3Ccircle cx='200' cy='20' r='2' fill='rgba(255,190,84,0.08)'/%3E%3Ccircle cx='80' cy='200' r='1.5' fill='rgba(255,190,84,0.08)'/%3E%3Ccircle cx='320' cy='230' r='2.5' fill='rgba(255,190,84,0.07)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            opacity: 0.9;
            pointer-events: none;
            z-index: 0;
        }

        /* Promo card "CORNDOG-KU" watermark */
        .font-londrina { font-family: 'Londrina Shadow', cursive; }
        .font-helvetica { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }

        /* Promo card hover lift */
        .promo-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .promo-card:hover {
            transform: translateY(-4px);
            box-shadow: 6px 8px 32px rgba(0,0,0,0.18);
        }

        /* ── Marquee category rail ── */
        @keyframes marquee-rail {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .marquee-track {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            animation: marquee-rail 24s linear infinite;
            white-space: nowrap;
        }
        .marquee-track:hover { animation-play-state: paused; }

        /* ── Horizontal scroll — no visible scrollbar ── */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* ── Seamless marquee ticker (sections 2-4 Figma poster area) ── */
        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 24s linear infinite;
            display: inline-flex;
            white-space: nowrap;
        }
    </style>
</head>
<body class="font-sans antialiased" style="background-color: var(--color-light); color: var(--color-black);">

{{-- ══════════════════════════════════════════════════════════════
     1. NAVBAR — edge-to-edge with wide inner container
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 w-full bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">

    {{-- Main Navbar Content --}}
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12 h-16 flex items-center justify-between gap-6">

        {{-- Brand --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}"
                alt="Corndog-Ku"
                class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-base tracking-tight hidden sm:inline"
          style="color: var(--color-black);">Corndog-Ku</span>
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

        {{-- Right: Cart → Avatar → Greeting → Logout --}}
        <div class="flex items-center gap-4 flex-none">

            {{-- Cart --}}
            <a href="{{ route('cart') }}"
               class="relative w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                             1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span id="cart-badge" class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold
                             flex items-center justify-center"
                      style="background-color: var(--color-accent); color: var(--color-black);">{{ count(session()->get('cart', [])) }}</span>
            </a>

            @auth
                <a href="{{ route('profile') }}"
                   class="w-9 h-9 rounded-full flex items-center justify-center
                          text-white text-sm font-extrabold transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary);"
                   title="{{ auth()->user()->name }}">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </a>
                <span class="hidden sm:block text-sm font-semibold"
                      style="color: var(--color-black);">
                    Halo, {{ auth()->user()->name }}
                </span>
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

    {{-- Banner Toko Tutup ditaruh tepat di bawah content navbar namun masih di dalam <header> --}}
    @php $storeInfo = $storeInfo ?? ['is_open' => true, 'reason' => 'schedule', 'reopen_day' => '', 'reopen_time' => '']; @endphp
    @if (!$storeInfo['is_open'])
    <div id="store-closed-banner"
         class="w-full py-3 px-4 flex items-center justify-center gap-2 text-sm font-semibold"
         style="background-color:#FEF3C7; color:#92400E; border-top: 1px solid #FDE68A; border-bottom: 2px solid #FDE68A;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-none" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                     4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>
            Maaf, toko sedang tutup.
            @if ($storeInfo['reopen_day'] && $storeInfo['reopen_time'])
                Toko akan buka kembali pada
                <strong>{{ $storeInfo['reopen_day'] }}</strong> pukul
                <strong>{{ $storeInfo['reopen_time'] }}</strong>.
            @endif
        </span>
    </div>
    @endif
</header>


{{-- ══════════════════════════════════════════════════════════════
     2-4. HERO POSTER + TICKER + PROMO CARDS — Figma "thumbnail users"
══════════════════════════════════════════════════════════════ --}}
<div class="relative w-full overflow-x-hidden bg-[#FEFDF2] pb-16">

    {{-- Hero — solid red section with two separate corndog images absolutely
         positioned left & right, and centered text. Fluid min-height + py so
         nothing overflows on mobile. --}}
    <section class="relative z-20 w-full mt-0 md:mt-4 bg-[#8D1818] hero-pattern
                    min-h-[400px] md:min-h-[500px] flex items-center justify-center
                    overflow-hidden py-16">

        <img src="{{ asset('assets/img/gmbr_banner_corndog_02.png') }}" alt="Corndog Left"
             class="absolute left-[-30px] md:left-[-10px] lg:left-0 top-[55%] lg:top-[60%] -translate-y-1/2
                    h-[300px] md:h-[450px] lg:h-[550px] xl:h-[650px] w-auto max-w-none object-contain
                    opacity-30 md:opacity-100 pointer-events-none z-0">

        <img src="{{ asset('assets/img/gmbr_banner_corndog_01.png') }}" alt="Corndog Right"
             class="absolute right-[-20px] md:right-4 lg:right-12 top-[55%] lg:top-[60%] -translate-y-1/2
                    h-[200px] md:h-[300px] lg:h-[450px] xl:h-[480px] w-auto max-w-none object-contain
                    opacity-30 md:opacity-100 pointer-events-none z-0">

        <div class="relative z-10 flex flex-col items-start text-left px-4 max-w-lg md:max-w-2xl mx-auto">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
                Crispy Corndog,<br>Happy Mood
            </h1>
            <p class="text-sm md:text-base text-gray-100 mb-8">
                Nikmati corndog hangat dengan topping melimpah dan mozzarella yang lumer di setiap gigitan. Dibuat fresh setiap hari untuk nemenin mood kamu kapan aja.
            </p>
            <a href="{{ route('menu') }}"
               class="bg-white text-[#8D1818] font-bold py-3 px-8 rounded-full shadow-lg hover:scale-105 transition-transform">
                TRY NOW
            </a>
        </div>
    </section>

    {{-- Ticker band — z-10 + negative margin so the poster's corndog stick breaks on top of it --}}
    <div class="relative z-10 w-full bg-[#B82B21] text-white py-2.5 md:py-3 overflow-hidden whitespace-nowrap shadow-md">
        <div class="animate-marquee font-bold tracking-widest text-base md:text-xl flex gap-8 items-center">
            @for ($i = 0; $i < 2; $i++)
                @foreach(array_fill(0, 7, '● ISI HARI MU DENGAN CORNDOG') as $t)
                    <span>{{ $t }}</span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- Cards grid --}}
    <div class="relative container mx-auto px-4 mt-12 max-w-5xl">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">

            {{-- Left: 2 stacked cards with floating Order Now buttons --}}
            <div class="flex flex-col gap-6">
                <a href="{{ route('menu') }}" class="group block relative rounded-[2rem] overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <img src="{{ asset('assets/img/home_card_01.png') }}"
                         alt="Promo 1"
                         class="w-full block">
                    <div class="absolute bottom-6 left-8 sm:bottom-8 sm:left-10">
                        <span class="inline-flex items-center gap-1 bg-white text-black font-bold text-[10px] sm:text-xs py-1.5 px-4 rounded-full shadow-lg group-hover:bg-gray-100 transition-colors">
                            Order Now 🔥
                        </span>
                    </div>
                </a>

                <a href="{{ route('menu') }}" class="group block relative rounded-[2rem] overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <img src="{{ asset('assets/img/home_card_02.png') }}"
                         alt="Promo 2"
                         class="w-full block">
                    <div class="absolute bottom-6 left-8 sm:bottom-8 sm:left-10">
                        <span class="inline-flex items-center gap-1 bg-white text-black font-bold text-[10px] sm:text-xs py-1.5 px-4 rounded-full shadow-lg group-hover:bg-gray-100 transition-colors">
                            Order Now 🔥
                        </span>
                    </div>
                </a>
            </div>

            {{-- Right: 1 large card --}}
            <div class="flex h-full">
                <a href="{{ route('menu') }}" class="group block w-full h-full">
                    <img src="{{ asset('assets/img/home_card_03.png') }}"
                         alt="Promo 3"
                         class="w-full h-full object-cover rounded-[2rem] shadow-md group-hover:shadow-xl group-hover:-translate-y-1 transition-all duration-300">
                </a>
            </div>
        </div>

    </div>
</div>

{{-- (old hero section replaced by Figma poster block — hidden pending full deletion) --}}
<section style="display:none;">

    {{-- ── Peeking decorative images — left & right edges ── --}}
    <img src="{{ asset('assets/img/home_corndog_01.png') }}"
         alt="Corndog Decoration"
         class="absolute top-10 -left-12 md:-left-24 w-32 md:w-64 object-contain pointer-events-none z-0 hidden sm:block opacity-90 drop-shadow-xl">

    <img src="{{ asset('assets/img/home_bingsoo_01.png') }}"
         alt="Bingsoo Decoration"
         class="absolute top-48 -right-12 md:-right-24 w-32 md:w-64 object-contain pointer-events-none z-0 hidden sm:block opacity-90 drop-shadow-xl">

    {{-- ── Decorative corndogs (absolute, z-0, desktop only) ── --}}
    <div class="absolute bottom-0 left-0 z-0 hidden lg:block pointer-events-none"
         style="width: clamp(180px, 20vw, 300px); opacity: 0.72; transform: rotate(-6deg); transform-origin: bottom left;">
        <img src="{{ asset('assets/img/CA_MOZZA_POTATO.png') }}" alt=""
             class="w-full object-contain object-bottom" draggable="false">
    </div>

    <div class="absolute z-0 hidden lg:block pointer-events-none"
         style="right: 36%; top: 6%; width: 110px; opacity: 0.38; transform: rotate(18deg);">
        <img src="{{ asset('assets/img/CA_DOUBLE_CHEESE.png') }}" alt=""
             class="w-full object-contain" draggable="false">
    </div>

    <div class="absolute z-0 hidden xl:block pointer-events-none"
         style="right: 2%; bottom: 8%; width: 90px; opacity: 0.35; transform: rotate(-14deg);">
        <img src="{{ asset('assets/img/CA_CHEETOS.png') }}" alt=""
             class="w-full object-contain" draggable="false">
    </div>

    {{-- ── Floating review card (z-20, sits on top of corndog) ── --}}
    <div class="absolute z-20 hidden lg:block pointer-events-none"
         style="right: 4%; top: 18%;">
        <div class="bg-white rounded-2xl px-4 py-3"
             style="min-width: 200px; max-width: 240px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center
                            text-white text-xs font-bold flex-none"
                     style="background-color: #F472B6;">S</div>
                <div>
                    <p class="text-xs font-bold leading-none" style="color: #1a1a1a;">Sarah K.</p>
                    <p class="text-[10px] mt-0.5" style="color: #888;">Pelanggan Setia</p>
                </div>
            </div>
            <p class="text-[10px] leading-relaxed" style="color: #444;">
                "Corndognya gak ada tandingannya! Crispy di luar, lumer di dalam. Wajib coba!"
            </p>
            <div class="flex items-center gap-0.5 mt-2">
                @for($i=0;$i<5;$i++)<span style="color: var(--color-accent); font-size: 10px;">★</span>@endfor
            </div>
        </div>
    </div>

    <div class="relative z-10 max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12
                flex flex-col lg:flex-row items-center lg:items-stretch
                gap-8 lg:gap-0 py-16 lg:py-0"
         style="min-height: 680px;">

        {{-- LEFT: text content — mirrors Figma left-[231px] start --}}
        <div class="flex-1 text-white flex flex-col justify-center
                    text-center lg:text-left lg:py-24 lg:pr-8">

            {{-- Top review badge --}}
            <div class="hidden lg:inline-flex items-center gap-2 mb-6 px-4 py-2 rounded-full
                        text-xs text-white/80 self-start"
                 style="background-color: rgba(255,255,255,0.12);">
                <span>⭐</span>
                <span>"Pelayanannya cepat, corndognya fresh, toppingnya gak pelit. Auto order lagi!"</span>
            </div>

            <h1 class="font-helvetica leading-[1.05] mb-5
                        text-4xl sm:text-5xl lg:text-[64px] xl:text-[70px]"
                style="font-weight: 900; color: white;">
                Crispy Corndog,<br>Happy Mood
            </h1>

            <p class="text-base sm:text-lg lg:text-[22px] leading-relaxed mb-8
                      max-w-md mx-auto lg:mx-0 lg:max-w-[580px]"
               style="opacity: 0.82;">
                Nikmati corndog hangat dengan topping melimpah dan mozzarella yang lumer di setiap
                gigitan. Dibuat fresh setiap hari untuk nemenin mood kamu kapan aja.
            </p>

            {{-- CTA + Social proof row --}}
            <div class="flex flex-col sm:flex-row items-center lg:items-start gap-5 mb-6">

                {{-- TRY NOW — white outlined pill --}}
                <a href="#menu"
                   class="inline-flex px-8 py-3.5 rounded-full font-bold text-sm tracking-widest
                          border-2 transition-all duration-200 hover:bg-white font-helvetica"
                   style="border-color: white; color: white;"
                   onmouseover="this.style.color='var(--color-primary)'"
                   onmouseout="this.style.color='white'">
                    TRY NOW
                </a>

                {{-- Social proof --}}
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2 flex-none">
                        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center
                                    text-white text-xs font-bold" style="background-color: #60A5FA;">A</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center
                                    text-white text-xs font-bold" style="background-color: #A855F7;">D</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center
                                    text-white text-xs font-bold" style="background-color: #F472B6;">B</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center
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

        {{-- RIGHT: hero corndog image — anchored to bottom, overflows naturally --}}
        <div class="flex-none flex items-end justify-center
                    w-full lg:w-[42%] xl:w-[40%]
                    lg:self-stretch">
            <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}"
                 alt="Corndog-Ku"
                 class="w-64 sm:w-80 lg:w-full lg:max-w-[520px]
                        object-contain object-bottom drop-shadow-2xl
                        lg:max-h-[720px]"
                 style="margin-bottom: -1px;">
        </div>

    </div>
</section>

{{-- old section 4 promo banners removed --}}
<section class="w-full" style="display:none;">
    <div>
        <div>

            {{-- LEFT COLUMN: two stacked cards --}}
            <div class="flex flex-col gap-5 lg:gap-6 lg:w-[44%] lg:flex-none">

                {{-- Card A: Korean Bingsu (TOP) — amber/yellow bg --}}
                <div class="promo-card relative rounded-[32px] lg:rounded-[40px] overflow-hidden
                            flex-1 min-h-[200px] lg:min-h-0 flex flex-col justify-between p-6 sm:p-8"
                     style="background-color: var(--color-accent);
                            box-shadow: var(--shadow-card);">

                    {{-- Decorative dots pattern --}}
                    <div class="absolute inset-0 opacity-20 pointer-events-none"
                         style="background-image: radial-gradient(circle, #A6171C 1px, transparent 1px);
                                background-size: 24px 24px;"></div>

                    {{-- Food image (absolute right) --}}
                    <img src="{{ asset('assets/img/BS_STRAWBERRY_CREAMY.png') }}"
                         alt="Korean Bingsu"
                         class="absolute right-4 bottom-0 h-40 sm:h-48 object-contain pointer-events-none
                                drop-shadow-lg z-10">

                    {{-- Tag --}}
                    <div class="relative z-10">
                        <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                           style="color: rgba(0,0,0,0.5);">Sweet &amp; Chilly</p>
                        <p class="font-helvetica leading-tight" style="font-size: clamp(26px, 3vw, 36px); font-weight: 900; color: var(--color-black);">
                            KOREAN<br>BINGSU
                        </p>
                    </div>

                    {{-- Bottom: button + discount --}}
                    <div class="relative z-10 flex items-end justify-between mt-4 pr-48 sm:pr-52">
                        <button class="text-xs font-bold px-5 py-2 rounded-full hover:opacity-80 transition-opacity"
                                style="background-color: var(--color-primary); color: white;">
                            Order Now 🔥
                        </button>
                        <div class="text-right">
                            <p class="text-xs font-medium" style="color: rgba(0,0,0,0.5);">Up to</p>
                            <p class="font-helvetica leading-none" style="font-size: clamp(32px, 4vw, 48px); font-weight: 900; color: var(--color-primary);">40%</p>
                        </div>
                    </div>
                </div>

                {{-- Card B: Cheesy Bites (BOTTOM) — red bg --}}
                <div class="promo-card relative rounded-[32px] lg:rounded-[40px] overflow-hidden
                            flex-1 min-h-[200px] lg:min-h-0 flex flex-col justify-between p-6 sm:p-8"
                     style="background-color: var(--color-primary);
                            box-shadow: var(--shadow-card);">

                    {{-- Decorative dots --}}
                    <div class="absolute inset-0 opacity-10 pointer-events-none"
                         style="background-image: radial-gradient(circle, #FFBE54 1px, transparent 1px);
                                background-size: 24px 24px;"></div>

                    {{-- Food image --}}
                    <img src="{{ asset('assets/img/CM_CHOCO_CHRUNCH_CHEESE.png') }}"
                         alt="Cheesy Bites"
                         class="absolute right-4 bottom-0 h-44 sm:h-52 object-contain pointer-events-none
                                drop-shadow-lg z-10">

                    {{-- Tag --}}
                    <div class="relative z-10">
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/60 mb-1">SUPER</p>
                        <p class="font-helvetica text-white leading-tight" style="font-size: clamp(26px, 3vw, 36px); font-weight: 900;">
                            CHEESY BITES
                        </p>
                    </div>

                    {{-- Bottom --}}
                    <div class="relative z-10 flex items-end justify-between mt-4 pr-48 sm:pr-56">
                        <button class="text-xs font-bold px-5 py-2 rounded-full hover:opacity-80 transition-opacity"
                                style="background-color: white; color: var(--color-primary);">
                            Order Now 🔥
                        </button>
                        <div class="text-right">
                            <p class="text-xs font-medium text-white/60">Up to</p>
                            <p class="font-helvetica text-white leading-none" style="font-size: clamp(32px, 4vw, 48px); font-weight: 900;">50%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Korean Corndogs large card --}}
            <div class="promo-card relative rounded-[32px] lg:rounded-[40px] overflow-hidden
                        flex-1 flex flex-col justify-between p-7 sm:p-10
                        min-h-[340px] lg:min-h-0"
                 style="background-color: #ffebc3;
                        box-shadow: var(--shadow-card);">

                {{-- Diagonal CORNDOG-KU watermark text (Londrina Shadow) --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden"
                     style="transform: rotate(-14deg); transform-origin: center;">
                    <div class="font-londrina whitespace-nowrap leading-[1.1] select-none"
                         style="color: rgba(255,190,84,0.45); font-size: clamp(80px, 10vw, 128px);">
                        <div>CORNDOG-KU CORNDOG-KU</div>
                        <div>CORNDOG-KU CORNDOG-KU</div>
                        <div>CORNDOG-KU CORNDOG-KU</div>
                        <div>CORNDOG-KU CORNDOG-KU</div>
                    </div>
                </div>

                {{-- Food image — large, centered --}}
                <img src="{{ asset('assets/img/CA_MOZZA_POTATO.png') }}"
                     alt="Loaded Korean Corndogs"
                     class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                     style="opacity: 0.5; mix-blend-mode: multiply;">

                {{-- Top label --}}
                <div class="relative z-10">
                    <p class="text-xs font-bold tracking-[0.2em] uppercase"
                       style="color: rgba(0,0,0,0.55);">LOADED</p>
                    <p class="font-helvetica leading-tight mt-1"
                       style="font-size: clamp(30px, 3.5vw, 42px); font-weight: 900; color: #1a1a1a;">
                        KOREAN<br>CORNDOGS
                    </p>
                </div>

                {{-- Bottom BUY NOW stamp --}}
                <div class="relative z-10 flex justify-end">
                    <div class="w-28 h-28 lg:w-32 lg:h-32 rounded-full bg-white shadow-xl
                                flex flex-col items-center justify-center
                                transition-transform duration-200 hover:scale-105">
                        <p class="font-helvetica leading-none" style="font-size: 26px; font-weight: 900; color: var(--color-black);">BUY</p>
                        <p class="font-helvetica leading-none" style="font-size: 26px; font-weight: 900; color: var(--color-black);">NOW</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     5. CUSTOMIZE CORNDOG BANNER
══════════════════════════════════════════════════════════════ --}}
<section class="w-full py-6 sm:py-8" style="background-color: var(--color-light);">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12">

        {{-- Wrapper: relative so the button can be layered above the image --}}
        <div class="relative w-full">

            {{-- Layer 1: Figma-exported composite banner image (natural intrinsic size) --}}
            <img src="{{ asset('assets/img/custom_corndog_banner_bg.png') }}"
                 alt="Custom Corndog – Buat corndog favoritmu sesuai seleramu!"
                 class="w-full h-auto block rounded-[2rem] select-none"
                 draggable="false"
                 style="box-shadow:0 8px 40px rgba(0,0,0,0.13);">

            {{-- Layer 2: Native interactive CTA button --}}
            <a href="{{ route('customize') }}"
               id="btn-custom-cta"
               class="absolute bottom-[8%] left-[5%] md:bottom-[15%] md:left-[8%] z-10
                      inline-flex items-center gap-1 bg-[#7A0000] text-white text-[10px] sm:text-xs font-bold
                      px-3 py-1.5 sm:px-4 sm:py-2 rounded-full whitespace-nowrap shadow-md
                      hover:bg-red-800 transition-colors w-auto max-w-fit">
                Yuk, Buat Corndog Kamu!
                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

        </div>{{-- /.banner --}}

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     6. MENU CATEGORIES + PER-CATEGORY PRODUCT MARQUEES (id="menu")
══════════════════════════════════════════════════════════════ --}}
<section id="menu" class="w-full" style="background-color: var(--color-light);">

    {{-- Section heading --}}
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12 pt-12 pb-0">
        <h2 class="font-helvetica text-center mb-8"
            style="font-size: clamp(28px, 3vw, 40px); font-weight: 900; color: var(--color-black);">
            Menu Categories
        </h2>
    </div>

    {{-- ── Category tab buttons (static, DB-driven) ──────────────── --}}
    <div class="flex flex-row overflow-x-auto justify-center gap-4 w-full hide-scrollbar px-4 pb-6">
        @foreach ($categories as $category)
            @if (strtolower($category->name) === 'custom') @continue @endif
            <button type="button"
                    class="category-btn relative flex-none px-8 py-2.5 font-bold whitespace-nowrap
                           transition-all hover:opacity-90
                           {{ $loop->first ? 'cat-btn-active' : 'cat-btn-inactive' }}"
                    data-target="marquee-{{ $category->id }}"
                    style="{{ $loop->first
                        ? 'background-color: white; color: var(--color-primary); border-radius: 9999px; box-shadow: 0 4px 20px rgba(0,0,0,0.14);'
                        : 'background-color: var(--color-primary); color: white; border-radius: 9999px;' }}">
                {{ $category->name }}
                {{-- Speech-bubble triangle tail (visible only on active) --}}
                <span class="active-tail pointer-events-none absolute left-1/2 {{ $loop->first ? '' : 'hidden' }}"
                      style="bottom: -9px; width: 16px; height: 16px;
                             background-color: white;
                             transform: translateX(-50%) rotate(45deg);
                             clip-path: polygon(100% 0%, 100% 100%, 0% 100%);
                             border-radius: 0 0 3px 0;"></span>
            </button>
        @endforeach
    </div>

    {{-- ── One marquee container per category ──────────────────────── --}}
    @foreach ($categories as $category)
        @if (strtolower($category->name) === 'custom') @continue @endif
        @php
            $catProducts = $products->where('category_id', $category->id);

            // Guarantee track width ≥ 2 × max-viewport (1440 px) so the -50%
            // animation never reveals a blank gap on the right side.
            // Card approx px: w-[260px] + mx-3 (24 px) + flex-gap (12 px) = 296 px.
            // Copies must be even so translateX(-50%) resets on an identical frame.
            $count = $catProducts->count();
            if ($count > 0) {
                $needed = (int) ceil((2 * 1440) / ($count * 296));
                if ($needed % 2 !== 0) { $needed++; }
                $totalCopies = max(2, $needed);
            } else {
                $totalCopies = 2;
            }
        @endphp

        <div id="marquee-{{ $category->id }}"
             class="category-marquee-container overflow-hidden w-full py-10 {{ $loop->first ? '' : 'hidden' }}">

            @if ($catProducts->isEmpty())
                <div class="py-16 text-center">
                    <div class="text-4xl mb-3">🌽</div>
                    <p class="font-bold text-base" style="color: var(--color-black);">Belum ada produk di kategori ini.</p>
                </div>
            @else
                <div class="marquee-track hover:[animation-play-state:paused]">
                    @for ($r = 0; $r < $totalCopies; $r++)
                        @foreach ($catProducts as $product)
                            <div class="product-card relative bg-white rounded-3xl shadow-sm p-4 pt-14
                                         w-[240px] md:w-[260px] shrink-0 mx-3 flex flex-col justify-between
                                         cursor-pointer hover:-translate-y-1 transition-all duration-200"
                                 @if($r > 0) aria-hidden="true" @endif
                                 data-category="{{ $product->category->name }}"
                                 data-price="{{ $product->price }}"
                                 data-name="{{ strtolower($product->name) }}"
                                 style="box-shadow: var(--shadow-card);">

                                {{-- Image pops out of card top --}}
                                <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 rounded-full
                                            flex items-center justify-center"
                                     style="background-color: #FDECD8;">
                                    <img src="{{ asset($product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="w-16 h-16 object-contain drop-shadow-md"
                                         onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                                </div>

                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-center mb-1"
                                       style="color: var(--color-accent);">{{ $product->category->name }}</p>
                                    <p class="font-bold text-sm text-center leading-snug mb-1"
                                       style="color: var(--color-primary);">{{ $product->name }}</p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed line-clamp-2 mb-3">
                                        {{ $product->description }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between gap-1 mt-auto">
                                    <p class="text-sm font-black" style="color: var(--color-primary);">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                    @if ($product->is_available && $product->stock > 0)
                                        <button type="button"
                                                class="btn-pesan flex-none px-3 py-1.5 rounded-full text-xs font-bold
                                                       transition-opacity hover:opacity-80"
                                                style="background-color: var(--color-accent); color: var(--color-black);"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-description="{{ $product->description }}"
                                                data-image="{{ asset($product->image) }}">
                                            Pesan
                                        </button>
                                    @else
                                        <button type="button" disabled
                                                class="flex-none px-3 py-1.5 rounded-full text-xs font-bold cursor-not-allowed"
                                                style="background-color: #d1d5db; color: #9ca3af;">
                                            Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endfor
                </div>
            @endif

        </div>
    @endforeach

</section>

{{-- ══════════════════════════════════════════════════════════════
     7. LOCATION & HOURS — full-width section
══════════════════════════════════════════════════════════════ --}}
<section class="w-full py-16" style="background-color: var(--color-white);">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12">
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
     8. TESTIMONIALS — full-width "Your trust in us"
══════════════════════════════════════════════════════════════ --}}
<section class="w-full py-16" style="background-color: #FFF9E6;">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12">

        <div class="mb-8">
            <span class="inline-block bg-white font-bold text-lg px-5 py-2 rounded-full shadow-sm"
                  style="color: var(--color-black);">Your trust in us</span>
        </div>

        @if (!empty($googleReviews))
            <div class="relative w-full px-4 sm:px-10">

                {{-- Left arrow — bound to Swiper via JS --}}
                <button id="swiperPrevBtn"
                        class="absolute left-0 top-[40%] -translate-y-1/2 z-20
                               bg-white rounded-full p-2 md:p-3 shadow-lg hover:shadow-xl hover:bg-gray-50
                               focus:outline-none transition-all hover:scale-110 hidden sm:flex
                               border border-gray-100" aria-label="Slide sebelumnya">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div class="swiper reviews-swiper w-full pb-10">
                    <div class="swiper-wrapper">
                        @foreach ($googleReviews as $review)
                            <div class="swiper-slide !w-[280px] sm:!w-[320px] h-auto">
                                <div class="bg-white rounded-2xl p-6 h-full flex flex-col justify-between
                                            hover:shadow-md transition-shadow"
                                     style="box-shadow: 0 2px 12px rgba(0,0,0,0.07); border: 1px solid #f3f4f6;">

                                    <div>
                                        {{-- Author row + Google logo --}}
                                        <div class="flex items-start justify-between gap-2 mb-4">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if (!empty($review['profile_photo_url']))
                                                    <img src="{{ $review['profile_photo_url'] }}"
                                                         alt="{{ $review['author_name'] }}"
                                                         class="w-10 h-10 rounded-full object-cover flex-none border border-gray-100">
                                                @else
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white
                                                                font-bold text-sm flex-none"
                                                         style="background-color: var(--color-primary);">
                                                        {{ strtoupper(mb_substr($review['author_name'] ?? '?', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <h4 class="font-bold text-sm text-gray-900 truncate">{{ $review['author_name'] ?? '' }}</h4>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $review['formatted_time'] ?? '' }}</p>
                                                </div>
                                            </div>
                                            {{-- Google logo (inline SVG — no external dependency) --}}
                                            <svg class="w-5 h-5 flex-none mt-0.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                            </svg>
                                        </div>

                                        {{-- Star rating (filled + unfilled) --}}
                                        <div class="flex gap-0.5 mb-3">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= ($review['rating'] ?? 5))
                                                    <span class="text-base" style="color: var(--color-accent);">★</span>
                                                @else
                                                    <span class="text-base text-gray-200">★</span>
                                                @endif
                                            @endfor
                                        </div>

                                        {{-- Review text --}}
                                        <p class="text-sm leading-relaxed text-gray-600 line-clamp-4">"{{ $review['text'] ?? '' }}"</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right arrow — bound to Swiper via JS --}}
                <button id="swiperNextBtn"
                        class="absolute right-0 top-[40%] -translate-y-1/2 z-20
                               bg-white rounded-full p-2 md:p-3 shadow-lg hover:shadow-xl hover:bg-gray-50
                               focus:outline-none transition-all hover:scale-110 hidden sm:flex
                               border border-gray-100" aria-label="Slide berikutnya">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

            </div>
        @else
            <p class="text-sm text-gray-400 italic">Belum ada ulasan yang tersedia.</p>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     9. FOOTER — full-width
══════════════════════════════════════════════════════════════ --}}
<footer class="w-full" style="background-color: var(--color-primary);">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-16 2xl:px-12 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            {{-- Brand column --}}
            <div>
                <img src="{{ asset('assets/img/logo.png') }}"
                     alt="Corndog-Ku"
                     class="w-14 h-14 rounded-full object-cover border-2 border-white/30 mb-4">

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
                    <a href="https://api.whatsapp.com/send/?phone=6282325110652&text&type=phone_number&app_absent=0"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center
                              hover:border-white transition-colors"
                       aria-label="WhatsApp">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/corndogku_id?igsh=cDN6b2w0dGwydjI3"
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
<div id="product-modal"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden"
     style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">

    <div id="product-modal-box"
         class="bg-white rounded-3xl overflow-hidden shadow-2xl w-full max-w-3xl flex flex-col md:flex-row relative">

        {{-- Close button --}}
        <button type="button" id="modal-close"
                class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-gray-100 transition-colors"
                aria-label="Tutup">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- LEFT: Product image on warm peach background --}}
        <div class="w-full md:w-1/2 flex justify-center items-center p-6 md:p-10"
             style="background-color: #FDECD8; min-height: 280px;">
            <img id="modal-image" src="" alt=""
                 class="w-full max-w-[240px] md:max-w-none h-56 md:h-72 object-contain drop-shadow-xl">
        </div>

        {{-- RIGHT: Details --}}
        <div class="w-full md:w-1/2 p-6 md:p-8 flex flex-col justify-between gap-5">

            {{-- Name + price --}}
            <div>
                <h3 id="modal-title"
                    class="text-xl font-bold leading-snug mb-2"
                    style="color: var(--color-black);"></h3>
                <p id="modal-price"
                   class="text-xl font-black"
                   style="color: var(--color-primary);"></p>
            </div>

            {{-- Description --}}
            <p id="modal-description"
               class="text-sm leading-relaxed"
               style="color: #525252;"></p>

            {{-- Quantity selector --}}
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold" style="color: var(--color-black);">Quantity</span>
                <div class="flex items-center gap-2 px-2 py-1.5 rounded-[8px]"
                     style="background-color: rgba(255,203,99,0.24); border: 1px solid #ffcb63;">
                    <button type="button" id="modal-qty-minus"
                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-base leading-none hover:opacity-70"
                            style="background-color: #ffcb63; color: #525252;">&#8722;</button>
                    <span id="modal-qty" class="w-7 text-center font-bold text-sm" style="color: #3d3d3d;">1</span>
                    <button type="button" id="modal-qty-plus"
                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-base leading-none hover:opacity-70"
                            style="background-color: var(--color-primary); color: white;">+</button>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-col gap-3">
                {{-- Outline: add to cart, stay on page --}}
                <button type="button"
                        class="btn-add-only w-full py-3 rounded-xl text-sm font-bold
                               flex items-center justify-center gap-2 border-2 transition-opacity hover:opacity-80"
                        style="border-color: var(--color-primary); color: var(--color-primary); background-color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-none" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Masukan Ke Keranjang
                </button>

                {{-- Solid: add then redirect to cart --}}
                <button type="button"
                        class="btn-order-now w-full py-3 rounded-xl text-sm font-bold
                               text-white transition-opacity hover:opacity-85"
                        style="background-color: var(--color-primary);">
                    Pesan Sekarang
                </button>
            </div>
        </div>

    </div>{{-- /#product-modal-box --}}
</div>{{-- /#product-modal --}}

</div>{{-- /.relative.overflow-x-hidden wrapper --}}

<script>
$(function () {

    /* ── CSRF header for all AJAX requests ───────────────── */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /* ── Current modal product data ─────────────────────── */
    var currentProductId    = null;
    var currentProductPrice = 0;
    var currentProductImage = '';
    var currentProductDesc  = '';

    /* ── Category tab → marquee switcher ───────────────────── */
    $('.category-btn').on('click', function () {
        var target = $(this).data('target');

        // Reset all buttons to inactive
        $('.category-btn')
            .removeClass('cat-btn-active')
            .addClass('cat-btn-inactive')
            .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'box-shadow': 'none' });
        $('.category-btn .active-tail').addClass('hidden');

        // Activate the clicked button
        $(this)
            .removeClass('cat-btn-inactive')
            .addClass('cat-btn-active')
            .css({ 'background-color': 'white', 'color': 'var(--color-primary)', 'box-shadow': '0 4px 20px rgba(0,0,0,0.14)' });
        $(this).find('.active-tail').removeClass('hidden');

        // Swap marquee containers
        $('.category-marquee-container').addClass('hidden');
        $('#' + target).removeClass('hidden');
    });

    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    var isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    /* ── Custom Corndog CTA — require login ─────────────── */
    $('#btn-custom-cta').on('click', function (e) {
        if (!isLoggedIn) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Silakan login atau daftar untuk membuat custom corndog.',
                showCancelButton: true,
                confirmButtonText: 'Login / Daftar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a81d1d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent("{{ route('customize') }}");
                }
            });
        }
    });

    $(document).on('click', '.btn-pesan', function () {
        if (!isLoggedIn) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Silakan login atau daftar untuk memesan.',
                showCancelButton: true,
                confirmButtonText: 'Login / Daftar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a81d1d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent(window.location.href);
                }
            });
            return;
        }

        var $b = $(this);
        currentProductId    = $b.data('id');
        currentProductPrice = parseInt($b.data('price'), 10) || 0;
        currentProductImage = $b.data('image');
        currentProductDesc  = $b.data('description');

        $('#modal-title').text($b.data('name'));
        $('#modal-price').text(fmtRp(currentProductPrice) + ' / pcs');
        $('#modal-description').text(currentProductDesc);
        $('#modal-image').attr({ src: currentProductImage, alt: $b.data('name') });
        $('#modal-qty').text('1');
        $('#product-modal').removeClass('hidden').addClass('flex');
        $('body').css('overflow', 'hidden');
    });

    /* ── Add to cart (outline button) — stay on page ────── */
    $(document).on('click', '.btn-add-only', function () {
        if (!isLoggedIn) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Anda belum login. Silakan login atau daftar untuk memesan.',
                showCancelButton: true,
                confirmButtonText: 'Login / Daftar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a81d1d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent(window.location.href);
                }
            });
            return;
        }

        var $btn     = $(this);
        var origHtml = $btn.html();
        var qty      = parseInt($('#modal-qty').text(), 10) || 1;

        $btn.prop('disabled', true).text('Menambahkan...');

        $.ajax({
            url:    '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id:  currentProductId,
                name:        $('#modal-title').text(),
                price:       currentProductPrice,
                qty:         qty,
                image:       currentProductImage,
                description: currentProductDesc,
            },
            success: function (response) {
                if (response.success) {
                    closeModal();
                    $('#cart-badge').text(response.count);
                    showCartToast('Ditambahkan ke keranjang!');
                }
            },
            error: function () {
                showCartToast('Gagal menambahkan ke keranjang.', true);
            },
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            }
        });
    });

    /* ── Pesan Sekarang (solid button) — add then redirect ─ */
    $(document).on('click', '.btn-order-now', function () {
        if (!isLoggedIn) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Anda belum login. Silakan login atau daftar untuk memesan.',
                showCancelButton: true,
                confirmButtonText: 'Login / Daftar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a81d1d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent(window.location.href);
                }
            });
            return;
        }

        var $btn = $(this);
        var qty  = parseInt($('#modal-qty').text(), 10) || 1;

        $btn.prop('disabled', true).text('Memproses...');

        $.ajax({
            url:    '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id:  currentProductId,
                name:        $('#modal-title').text(),
                price:       currentProductPrice,
                qty:         qty,
                image:       currentProductImage,
                description: currentProductDesc,
            },
            success: function (response) {
                if (response.success) {
                    window.location.href = '{{ route("cart") }}';
                }
            },
            error: function () {
                showCartToast('Gagal menambahkan ke keranjang.', true);
                $btn.prop('disabled', false).text('Pesan Sekarang');
            }
        });
    });

    function showCartToast(msg, isError) {
        var bg = isError ? '#c00f0c' : '#A6171C';
        var $t = $('<div>').text(msg).css({
            position: 'fixed', bottom: '28px', left: '50%',
            transform: 'translateX(-50%)',
            background: bg, color: '#fff',
            padding: '11px 28px', borderRadius: '999px',
            fontWeight: '700', fontSize: '14px',
            zIndex: 99999, boxShadow: '0 4px 24px rgba(0,0,0,0.2)',
            whiteSpace: 'nowrap'
        }).appendTo('body');
        setTimeout(function () { $t.fadeOut(300, function () { $(this).remove(); }); }, 2500);
    }

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

    /* Reviews navigation is handled by Swiper (see script block below jQuery). */

    /* ── Normalize all marquee speeds to match Corndog Manis pace ─── */
    (function normalizeMarqueeSpeeds() {
        var BASE_DURATION = 24; // seconds — the CSS default

        var $manisBtn = $('.category-btn').filter(function () {
            return $.trim($(this).text()) === 'Corndog Manis';
        });
        if (!$manisBtn.length) { return; }
        var manisId = $manisBtn.data('target');

        // Temporarily show hidden containers off-screen so the browser lays them out
        var hidden = [];
        $('.category-marquee-container').each(function () {
            if ($(this).hasClass('hidden')) {
                hidden.push(this);
                $(this).removeClass('hidden')
                       .css({ visibility: 'hidden', position: 'absolute', pointerEvents: 'none' });
            }
        });

        var $manisTrack = $('#' + manisId + ' .marquee-track').first();
        var manisW = $manisTrack.length ? $manisTrack[0].scrollWidth : 0;

        if (manisW > 0) {
            // Speed (px/s) that Corndog Manis scrolls: it moves -50% (half its width) in BASE_DURATION s
            var pxPerSec = (manisW / 2) / BASE_DURATION;
            $('.marquee-track').each(function () {
                var w = this.scrollWidth;
                if (w > 0) {
                    this.style.animationDuration = ((w / 2) / pxPerSec).toFixed(2) + 's';
                }
            });
        }

        // Restore hidden containers
        $.each(hidden, function (_, el) {
            $(el).css({ visibility: '', position: '', pointerEvents: '' }).addClass('hidden');
        });
    })();

});
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
(function () {
    if (typeof Swiper === 'undefined' || !document.querySelector('.reviews-swiper')) { return; }

    var reviewsSwiper = new Swiper('.reviews-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 16,
        loop: true,
        grabCursor: true,
        breakpoints: {
            640: { spaceBetween: 24 },
        },
    });

    var prevBtn = document.getElementById('swiperPrevBtn');
    var nextBtn = document.getElementById('swiperNextBtn');
    if (prevBtn) { prevBtn.addEventListener('click', function () { reviewsSwiper.slidePrev(); }); }
    if (nextBtn) { nextBtn.addEventListener('click', function () { reviewsSwiper.slideNext(); }); }
})();
</script>

{{-- ════════════════════════════════════════════════════════════
     CHATBOT WIDGET — floating bottom-right on all customer pages
════════════════════════════════════════════════════════════ --}}

{{-- Floating trigger button — #FFBE54 pill, 15px radius, same-color badge with white ring (Figma 347:4278) --}}
<button id="chatbot-trigger"
        aria-label="Buka chat asisten"
        class="fixed bottom-6 right-6 z-[9999] flex items-center gap-2 px-5 py-3 shadow-lg transition-transform hover:scale-105 active:scale-95"
        style="background-color: #FFBE54; border-radius: 15px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white flex-shrink-0" fill="currentColor"
         viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
    <span class="text-white text-base font-semibold tracking-tight">Chat</span>
    {{-- Badge: same yellow bg + white border ring, matches Figma 347:4290 --}}
    <span class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center text-white border-2 border-white"
          style="background-color: #FFBE54;">1</span>
</button>

{{-- Chat window --}}
<div id="chatbot-window"
     class="hidden fixed bottom-20 right-4 md:bottom-24 md:right-6 z-50 flex flex-col rounded-2xl overflow-hidden shadow-2xl
            w-[calc(100vw-2rem)] max-w-[340px] md:w-96 h-[70vh] max-h-[480px] md:h-[480px]"
     style="border: 1px solid #e5e7eb;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 flex-shrink-0"
         style="background-color: #8B0000;">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-semibold text-sm leading-none">Corndog-Ku Assistant</p>
                <p class="text-white/70 text-[10px] mt-0.5">Online · Biasanya membalas segera</p>
            </div>
        </div>
        <button id="chatbot-close"
                class="text-white/80 hover:text-white transition-colors text-sm font-medium">
            × Tutup
        </button>
    </div>

    {{-- Messages area --}}
    <div id="chatbot-messages"
         class="flex-1 overflow-y-auto px-4 py-3 space-y-3"
         style="background-color: #FAFAFA;">

        {{-- Greeting bubble --}}
        <div class="flex items-end gap-2">
            <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center"
                 style="background-color: #8B0000;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
            </div>
            <div class="max-w-[75%] px-3 py-2 rounded-2xl rounded-bl-sm text-sm"
                 style="background-color: #F0F0F0; color: #1a1a1a;">
                Halo! 👋 Aku asisten Corndog-Ku. Ada yang bisa aku bantu?
            </div>
        </div>
    </div>

    {{-- Input area --}}
    <div class="flex items-center gap-2 px-3 py-3 flex-shrink-0 bg-white border-t border-gray-100">
        <input id="chatbot-input"
               type="text"
               placeholder="Ketik pesan..."
               autocomplete="off"
               class="flex-1 px-4 py-2 text-sm rounded-full border border-gray-200 outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100 transition-all"
               style="background-color: #F9F9F9;">
        <button id="chatbot-send"
                class="w-9 h-9 flex-shrink-0 rounded-full flex items-center justify-center transition-colors hover:opacity-80"
                style="background-color: #8B0000;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function () {
    const trigger   = document.getElementById('chatbot-trigger');
    const window_   = document.getElementById('chatbot-window');
    const closeBtn  = document.getElementById('chatbot-close');
    const messages  = document.getElementById('chatbot-messages');
    const input     = document.getElementById('chatbot-input');
    const sendBtn   = document.getElementById('chatbot-send');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const sendUrl   = '{{ route("chatbot.send") }}';

    let isSending = false;

    trigger.addEventListener('click', () => {
        window_.classList.toggle('hidden');
        if (!window_.classList.contains('hidden')) {
            input.focus();
            scrollToBottom();
        }
    });

    closeBtn.addEventListener('click', () => window_.classList.add('hidden'));

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    sendBtn.addEventListener('click', sendMessage);

    function sendMessage() {
        const text = input.value.trim();
        if (!text || isSending) return;

        isSending = true;
        input.value = '';
        sendBtn.disabled = true;

        appendBubble(text, 'user');
        const typingId = appendTyping();

        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text }),
        })
        .then(r => r.json())
        .then(data => {
            removeTyping(typingId);
            appendBubble(data.reply || 'Maaf, terjadi kesalahan.', 'bot');
        })
        .catch(() => {
            removeTyping(typingId);
            appendBubble('Maaf, koneksi bermasalah. Coba lagi ya!', 'bot');
        })
        .finally(() => {
            isSending = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    function appendBubble(text, role) {
        const isUser = role === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-end gap-2' + (isUser ? ' justify-end' : '');

        if (!isUser) {
            wrapper.innerHTML = `
                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color:#8B0000;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>`;
        }

        const bubble = document.createElement('div');
        bubble.className = 'max-w-[75%] px-3 py-2 rounded-2xl text-sm whitespace-pre-wrap';
        if (isUser) {
            bubble.style.cssText = 'background-color:#8B0000; color:#fff; border-radius:16px 16px 4px 16px;';
        } else {
            bubble.style.cssText = 'background-color:#F0F0F0; color:#1a1a1a; border-radius:16px 16px 16px 4px;';
        }
        bubble.textContent = text;
        wrapper.appendChild(bubble);

        messages.appendChild(wrapper);
        scrollToBottom();
    }

    function appendTyping() {
        const id = 'typing-' + Date.now();
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = 'flex items-end gap-2';
        wrapper.innerHTML = `
            <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color:#8B0000;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
            </div>
            <div class="px-4 py-2.5 rounded-2xl" style="background-color:#F0F0F0; border-radius:16px 16px 16px 4px;">
                <span class="flex gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms"></span>
                </span>
            </div>`;
        messages.appendChild(wrapper);
        scrollToBottom();
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }
})();
</script>

</body>
</html>
