<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Custom Corndog — Corndog-Ku</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --custom-bg: #FFFDEB;
            --custom-red: #A6171C;
            --custom-yellow: #FFC95E;
            --custom-yellow-soft: #FFDEA0;
            --custom-text: #111111;
        }

        body {
            background-color: var(--custom-bg);
        }

        .custom-page {
            min-height: calc(100vh - 64px);
            background: var(--custom-bg);
            position: relative;
            overflow: hidden;
        }

        /* Decorative background */
        .deco-circle-left,
        .deco-circle-right {
            position: absolute;
            border-radius: 999px;
            background: var(--custom-yellow-soft);
            z-index: 1;
            pointer-events: none;
        }

        .deco-circle-left {
            left: -125px;
            top: 35%;
            width: 270px;
            height: 270px;
        }

        .deco-circle-right {
            right: -100px;
            top: -24px;
            width: 270px;
            height: 270px;
        }

        .deco-dot {
            position: absolute;
            border-radius: 999px;
            background: #FFC14D;
            z-index: 2;
            pointer-events: none;
        }

        .dot-1 { left: 95px; top: 270px; width: 18px; height: 18px; }
        .dot-2 { left: 58px; bottom: 270px; width: 27px; height: 27px; }
        .dot-3 { right: 108px; top: 260px; width: 28px; height: 28px; }
        .dot-4 { right: 155px; bottom: 250px; width: 17px; height: 17px; }
        .dot-5 { right: 145px; top: 65px; width: 18px; height: 18px; }

        /* Poster title */
        .custom-title-wrap {
            position: absolute;
            top: 38px;
            left: 90px;
            z-index: 20;
        }

        .custom-title {
    position: relative;
    display: inline-block;
    font-weight: 900;
    line-height: 0.9;
    letter-spacing: 1px;
    transform: rotate(-3deg);
}

        .custom-title .black {
            display: block;
            font-size: clamp(46px, 5vw, 78px);
            color: #050505;
        }

        .custom-title .red {
            display: block;
            font-size: clamp(46px, 4.9vw, 76px);
            color: var(--custom-red);
            margin-left: 88px;
        }

      .title-brush {
    width: 360px;
    height: 28px;
    margin-top: -2px;
    margin-left: 34px;
    opacity: 0.9;
}

.title-brush img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

    .subtitle-blob {
    margin-top: 10px;
    margin-left: 8px;
    position: relative;

    width: 330px;
    height: 82px;

    display: flex;
    align-items: center;

    padding: 12px 60px 12px 32px;

    background-image: url('{{ asset('assets/img/Yellow_Custom.png') }}');
    background-repeat: no-repeat;
    background-position: center;
    background-size: 100% 100%;

    color: #050505;
    transform: none;
    overflow: visible;
}

.subtitle-text {
    display: block;
    max-width: 280px;
    font-weight: 900;
    font-size: 17px;
    line-height: 1.08;
    color: #000000;
    text-align: left;
}

.subtitle-heart-img {
    position: absolute;
    right: -12px;
    top: -20px;
    width: 58px;
    height: auto;
    object-fit: contain;
    pointer-events: none;
}

      .title-accent-lines {
    position: absolute;
    right: 80px;
    top: -18px;
    width: 62px;
    height: 48px;
    pointer-events: none;
    z-index: 25;
}

        .custom-layout {
            position: relative;
            min-height: calc(100vh - 64px);
            width: 100%;
            z-index: 10;
        }

        /* Stepper */
        #stepper {
            position: absolute;
            top: 82px;
            right: 140px;
            z-index: 30;
            display: flex;
            align-items: flex-start;
            gap: 0;
        }

        .step-line {
            flex: 1;
            border-top: 1.8px solid #FFBE54 !important;
            min-width: 70px !important;
            margin-top: 17px;
        }

        .step-line.done {
            border-color: #FFBE54 !important;
        }

        #stepper > div > div:first-child {
            width: 34px !important;
            height: 34px !important;
            font-size: 16px !important;
            border-width: 1.8px !important;
        }

        #stepper > div > span {
            display: block !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #3b3b3b !important;
            margin-top: 8px;
        }

        /* Preview center */
        .custom-preview-area {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(44vw, 560px);
            height: min(62vh, 670px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 12;
        }

        .corndog-blob {
            position: absolute;
            width: 430px;
            height: 350px;
            background: var(--custom-yellow-soft);
            border-radius: 46% 54% 48% 52% / 52% 45% 55% 48%;
            transform: rotate(-24deg);
            z-index: 0;
            pointer-events: none;
        }

        #base-corndog {
            height: 50vh !important;
            max-height: 560px !important;
            width: auto;
            object-fit: contain;
            z-index: 5 !important;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        #base-corndog.fading {
            opacity: 0;
        }

        #middle-varian {
            height: 50vh !important;
            max-height: 560px !important;
            width: auto;
            object-fit: contain;
        }

        #overlay-sauce {
            height: 52vh !important;
            max-height: 575px !important;
            width: auto;
            object-fit: contain;
            transition: none;
        }
        #overlay-sauce {
    border: none !important;
    outline: none !important;
}

#overlay-sauce:not([src]),
#overlay-sauce[src=""] {
    display: none !important;
}

        .selection-pill {
    position: absolute;
    left: 110px;
    bottom: 135px;
    min-width: 290px;
    height: 86px;
    background: #FFFFFF !important;   /* ini yang bikin putih */
    border: 2.5px dashed #F0A62B !important;
    border-radius: 999px !important;
    padding: 0 30px !important;
    transform: none !important;
    z-index: 22;
    text-align: center;

    display: flex;
    align-items: center;
    justify-content: center;
}

        #carousel-label-text {
    font-size: 24px !important;
    font-weight: 900 !important;
    letter-spacing: 0.5px !important;
    color: #A62A24 !important;
    line-height: 1 !important;
    white-space: nowrap;
}

        #carousel-dots {
            position: absolute;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            z-index: 30;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        #carousel-dots > div {
            width: 18px !important;
            height: 18px !important;
        }

        #sauce-chips {
            display:none !important;
        }

        /* Arrows */
        #btn-prev,
        #btn-next {
            position: absolute;
            top: 45%;
            z-index: 40;
            width: 62px !important;
            height: 62px !important;
            border-radius: 14px !important;
            background: white !important;
            color: #B94D52 !important;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
            font-size: 48px !important;
            line-height: 1 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 5px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        #btn-prev:hover,
        #btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(0,0,0,0.18) !important;
        }

        #btn-prev { left: 112px; }
        #btn-next { right: 112px; }

        /* Step instruction */
        #step-card {
    position: absolute;
    right: 95px;
    bottom: 120px;
    width: 390px;
    background: white !important;
    border: 1.8px solid #FFBE54 !important;
    border-radius: 10px !important;
    box-shadow: none !important;
    padding: 24px 30px !important;
    z-index: 25;
}

        #step-card-num {
            width: 46px !important;
            height: 46px !important;
            font-size: 24px !important;
            background-color: var(--custom-red) !important;
        }

        #step-card-title {
            font-size: 24px !important;
            line-height: 1.15 !important;
            color: #333 !important;
        }

        #step-card-desc {
    font-size: 16px !important;
    line-height: 1.22 !important;
    color: #333 !important;
    font-weight: 600;
    margin-top: 12px !important;
}

        /* Hidden selection cards, but keep DOM for JS logic */
        #ingredient-grid {
            display: none !important;
        }

        #add-sauce-wrap {
    margin-top: 14px !important;
    padding-top: 14px !important;
}

#add-sauce-wrap p {
    margin-bottom: 10px !important;
}

#add-sauce-btn {
    padding-top: 14px !important;
    padding-bottom: 14px !important;
    border-radius: 14px !important;
}

        /* Review */
        #review-panel {
    position: absolute;
    right: 35px;
    bottom: 120px;
    width: 430px;
    max-height: 48vh;
    overflow: auto;
    z-index: 45;
    background: white;
    padding: 22px !important;
    border-radius: 18px !important;
}

        #review-items {
            gap: 12px !important;
            margin-bottom: 16px !important;
        }

        /* Bottom buttons */
        .button-area-custom {
            position: absolute;
    left: 50%;
    bottom: 38px;
    transform: translateX(-50%);
    width: min(70vw, 740px);
    z-index: 40;
        }

        #btn-next-step {
             width: 100% !important;
    max-width: none !important;
    border-radius: 14px !important;
    padding: 14px 28px !important;
    font-size: 24px !important;
            line-height: 1.1;
            background-color: var(--custom-red) !important;
            color: white !important;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        #btn-next-step:hover {
            opacity: 0.9;
        }

        #btn-back {
            position: absolute;
            left: -178px;
            top: 0;
            height: 100%;
            border-radius: 14px !important;
            background: var(--custom-bg) !important;
            border: 2px solid var(--custom-red) !important;
            color: var(--custom-red) !important;
            padding-left: 28px;
            padding-right: 28px;
        }

        #btn-back:not(.hidden) {
            display: flex !important;
        }


        .corndog-red-accent {
            position: absolute;
            right: 12%;
            top: 17%;
            z-index: 20;
        }

        /* Keep elements stable after JS display changes */
        #carousel-center[style*="display: none"] {
            display: none !important;
        }

       #carousel-center.oos-active #base-corndog,
#carousel-center.oos-active #middle-varian,
#carousel-center.oos-active #overlay-sauce {
    filter: grayscale(80%) opacity(0.5);
}

@media (max-width: 1180px) {
    .custom-title-wrap {
        top: 30px;
        left: 34px;
        transform: scale(0.88);
        transform-origin: top left;
    }

    #stepper {
        top: 210px;
        left: 50%;
        right: auto;
        transform: translateX(-50%) scale(0.88);
        transform-origin: top center;
    }

    .custom-preview-area {
        top: 60%;
        width: 460px;
        height: 520px;
    }

    .corndog-blob {
        width: 340px;
        height: 285px;
    }

    #base-corndog {
        height: 390px !important;
        max-height: none !important;
    }

    #overlay-sauce {
        height: 400px !important;
        max-height: none !important;
    }

    #btn-prev {
        left: 38px;
        top: 58%;
    }

    #btn-next {
        right: 38px;
        top: 58%;
    }

    .selection-pill {
        left: 34px;
        bottom: 170px;
        min-width: 250px;
        height: 74px;
    }

    #carousel-label-text {
        font-size: 20px !important;
    }

    #carousel-dots {
        bottom: 42px;
    }

    #step-card {
        right: 34px;
        bottom: 165px;
        width: 300px;
        padding: 20px 22px !important;
    }

    #step-card-title {
        font-size: 20px !important;
    }

    #step-card-desc {
        font-size: 14px !important;
        line-height: 1.2 !important;
    }

    #review-panel {
        right: 34px;
        bottom: 155px;
        width: 360px;
    }

    .button-area-custom {
        width: 78vw;
        bottom: 36px;
    }

    #btn-next-step {
        font-size: 22px !important;
        padding: 14px 24px !important;
    }

    #btn-back {
        left: -150px;
        padding-left: 22px;
        padding-right: 22px;
    }

    .corndog-red-accent {
        right: 9%;
        top: 25%;
    }
}

@media (max-width: 900px) {
    body {
        overflow: auto !important;
    }

    .custom-page {
        min-height: 1080px;
        overflow: hidden;
    }

    .custom-title-wrap {
        top: 24px;
        left: 22px;
        transform: scale(0.78);
        transform-origin: top left;
    }

    #stepper {
        top: 190px;
        left: 50%;
        right: auto;
        transform: translateX(-50%) scale(0.78);
        transform-origin: top center;
    }

    .custom-preview-area {
        top: 57%;
        width: 90vw;
        height: 470px;
    }

    .corndog-blob {
        width: 300px;
        height: 250px;
    }

    #base-corndog {
        height: 350px !important;
    }

    #overlay-sauce {
        height: 360px !important;
    }

    #btn-prev {
        left: 18px;
        top: 58%;
        width: 56px !important;
        height: 56px !important;
    }

    #btn-next {
        right: 18px;
        top: 58%;
        width: 56px !important;
        height: 56px !important;
    }

    .selection-pill {
        left: 22px;
        bottom: 165px;
        min-width: 220px;
        height: 68px;
        padding: 0 20px !important;
    }

    #carousel-label-text {
        font-size: 18px !important;
    }

    #carousel-dots {
        bottom: 36px;
    }

    #step-card {
        right: 22px;
        bottom: 155px;
        width: 260px;
        padding: 18px 18px !important;
    }

    #step-card-num {
        width: 40px !important;
        height: 40px !important;
        font-size: 20px !important;
    }

    #step-card-title {
        font-size: 18px !important;
    }

    #step-card-desc {
        font-size: 13px !important;
        line-height: 1.18 !important;
        margin-top: 8px !important;
    }

    #review-panel {
        left: 22px;
        right: 22px;
        bottom: 145px;
        width: auto;
    }

    .button-area-custom {
        width: 86vw;
        bottom: 28px;
    }

    #btn-back {
        position: static;
        width: 100%;
        height: auto;
        margin-bottom: 10px;
        justify-content: center;
        padding: 12px 20px;
    }

    #btn-next-step {
        font-size: 20px !important;
        padding: 13px 20px !important;
    }

    .corndog-red-accent {
        right: 8%;
        top: 26%;
    }
}
    </style>
</head>
<body class="font-sans antialiased h-screen overflow-hidden flex flex-col">

{{-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">
    <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-16 h-16 flex items-center justify-between gap-6">

        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku"
                 class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-lg tracking-tight" style="color: var(--color-black);">Corndog-Ku</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 flex-1 justify-center">
            <a href="{{ route('welcome') }}"
               class="text-sm font-medium transition-colors hover:opacity-70"
               style="color: var(--color-black);">Beranda</a>
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
                <span id="cart-badge"
                      class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold
                             flex items-center justify-center"
                      style="background-color: var(--color-accent); color: var(--color-black);">{{ count(session()->get('cart', [])) }}</span>
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
     MAIN — CUSTOM CORNDOG WIZARD
══════════════════════════════════════════════════════════════ --}}
<section class="custom-page flex-1">

    {{-- Decorative background --}}
    <div class="deco-circle-left"></div>
    <div class="deco-circle-right"></div>
    <div class="deco-dot dot-1"></div>
    <div class="deco-dot dot-2"></div>
    <div class="deco-dot dot-3"></div>
    <div class="deco-dot dot-4"></div>
    <div class="deco-dot dot-5"></div>

    {{-- Title --}}
    <div class="custom-title-wrap">
        <div class="custom-title">
            <span class="black">CUSTOM</span>
            <span class="red">CORNDOG</span>
        </div>

        <svg class="title-accent-lines" viewBox="0 0 90 70" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L25 42" stroke="#FFBE54" stroke-width="13" stroke-linecap="round"/>
            <path d="M62 23L37 49" stroke="#FFBE54" stroke-width="10" stroke-linecap="round"/>
            <path d="M80 56L45 58" stroke="#FFBE54" stroke-width="8" stroke-linecap="round"/>
        </svg>

        <div class="title-brush">
    <img src="{{ asset('assets/img/line_custome.png') }}" alt="Line decoration">
</div>

      <div class="subtitle-blob">
    <span class="subtitle-text">
        Buat Corndog Favoritemu<br>
        Sesuai Seleramu
    </span>

    <img src="{{ asset('assets/img/heart_custom.png') }}"
         alt="Heart decoration"
         class="subtitle-heart-img">
</div>

</div> {{-- TUTUP .custom-title-wrap --}}

{{-- Stepper --}}
<div id="stepper">
    {{-- Rendered by JS --}}
</div>

    {{-- Stepper --}}
    <div id="stepper">
        {{-- Rendered by JS --}}
    </div>

    <div class="custom-layout">

        {{-- Left arrow --}}
        <button id="btn-prev"
                type="button"
                aria-label="Previous item">
            &#8249;
        </button>

        {{-- Main corndog preview --}}
        <div id="carousel-center" class="custom-preview-area">

            <div class="corndog-blob"></div>

            {{-- Base corndog --}}
            <img id="base-corndog"
                 src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
                 alt="Corndog preview"
                 style="transform: scale(1.15);"
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                        select-none pointer-events-none transition-transform duration-300">

            {{-- Varian layer --}}
            <div id="middle-varian-wrap"
                 class="absolute inset-0 flex items-center justify-center pointer-events-none"
                 style="display:none; z-index:6;">
                <img id="middle-varian"
                     src=""
                     alt=""
                     style="transform: scale(1.0);"
                     class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                            select-none pointer-events-none transition-transform duration-300">
            </div>

            {{-- Sauce overlay --}}
            <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
                <img id="overlay-sauce"
                     alt=""
                     style="transform: scale(0.9) translateY(-1rem);"
                     class="select-none pointer-events-none transition-transform duration-300">
            </div>

            {{-- Red accent --}}
            <div class="corndog-red-accent pointer-events-none">
                <svg width="54" height="54" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 4L7 35" stroke="#A6171C" stroke-width="8" stroke-linecap="round"/>
                    <path d="M36 17L18 39" stroke="#A6171C" stroke-width="8" stroke-linecap="round"/>
                    <path d="M50 38L27 43" stroke="#A6171C" stroke-width="8" stroke-linecap="round"/>
                </svg>
            </div>

            {{-- Dot indicators --}}
            <div id="carousel-dots"></div>

            {{-- Step 3 sauce chips --}}
            <div id="sauce-chips" class="hidden flex-wrap gap-2 justify-center max-w-xs"></div>

            {{-- OOS overlay badge --}}
            <div id="oos-badge"
                 class="hidden absolute pointer-events-none flex items-center justify-center"
                 style="top: 42%; left: 50%; transform: translate(-50%, -50%); z-index: 22;">
                <div class="text-white font-black text-xl px-6 py-2 rounded-full shadow-lg"
                     style="background-color: #dc2626; transform: rotate(-12deg); letter-spacing: 3px;">
                    HABIS
                </div>
            </div>
        </div>

        {{-- Selection name pill --}}
        <div class="selection-pill" id="carousel-label">
            <span class="font-black tracking-widest"
                  id="carousel-label-text"
                  style="color: var(--color-primary);">SOSIS &amp; MOZZA</span>
            <div id="carousel-label-price" class="text-xs font-semibold mt-0.5 hidden"
                 style="color: var(--color-primary);"></div>
        </div>

        {{-- Right arrow --}}
        <button id="btn-next"
                type="button"
                aria-label="Next item">
            &#8250;
        </button>

        {{-- Step instruction card --}}
        <div id="step-card">
            <div class="flex items-start gap-4">
                <div id="step-card-num"
                     class="rounded-full flex items-center justify-center text-white font-bold flex-none"
                     style="background-color: var(--color-primary);">
                    1
                </div>
                <div>
                    <p id="step-card-title" class="font-bold">
                        Pilih Isi Corndog
                    </p>
                    <p id="step-card-desc">
                        Geser atau gunakan tombol untuk memilih isi favoritemu
                    </p>
                </div>
            </div>

            {{-- Tetap ada agar logic JS tidak rusak, tapi disembunyikan oleh CSS --}}
            <div id="ingredient-grid" class="grid gap-2 mt-4"></div>

            {{-- Sauce add button, tetap dipakai di step 3 --}}
            <div id="add-sauce-wrap" class="hidden mt-5 pt-5 border-t border-gray-100">
                <p class="text-sm text-right mb-3 font-semibold" style="color: var(--color-primary);">
                    Max 2 sauce*
                </p>
                <button id="add-sauce-btn"
                        type="button"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl
                               text-base font-bold border-2 transition-colors hover:opacity-80"
                        style="border-color: var(--color-primary); color: var(--color-primary);">
                    <span>+</span> Add Sauce
                </button>
            </div>
        </div>

        {{-- Review panel --}}
        <div id="review-panel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-lg mb-5" style="color: var(--color-black);">
                Ringkasan Pesananmu
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" id="review-items"></div>
            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Harga Dasar</span>
                    <span id="review-base" class="font-medium text-gray-800">Rp 16.000</span>
                </div>
                <div class="flex justify-between text-gray-500" id="review-extra-row">
                    <span>Varian Tambahan</span>
                    <span id="review-extra" class="font-medium text-gray-800">Rp 0</span>
                </div>
                <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-100">
                    <span style="color: var(--color-black);">Total</span>
                    <span id="review-total" style="color: var(--color-primary);">Rp 16.000</span>
                </div>
            </div>
        </div>

        {{-- Next / Back buttons --}}
        <div class="button-area-custom">
            <button id="btn-back"
                    type="button"
                    class="hidden items-center justify-center font-bold text-base transition-opacity hover:opacity-70">
                &#8592; Kembali
            </button>

            <button id="btn-next-step"
                    type="button"
                    class="font-bold tracking-wide transition-opacity hover:opacity-85 active:scale-[0.99]">
                Next Pilih Varian
            </button>
        </div>

    </div>
</section>
{{-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer class="hidden" style="background-color: var(--color-primary);">
    <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-16 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            <div>
                <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku"
                     class="w-14 h-14 rounded-full object-cover border-2 border-white/30 mb-4">
                <p class="text-white font-bold text-2xl leading-snug mb-1">
                    Beli dimana saja,<br>pesan kapan saja
                </p>
                <p class="text-white/70 text-sm font-semibold mt-3 mb-3">Tersedia Order Online</p>
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

            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Contact Us</h4>
                <ul class="space-y-2 text-sm text-white/70">
                    <li>@corndogku_id</li>
                    <li>+62 823-2511-0652</li>
                    <li class="pt-1">Jl. Rungkut Mejoyo Utara No.61, Surabaya</li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4 text-sm">Follow Us</h4>
                <div class="flex items-center gap-3">
                    <a href="#"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center hover:border-white transition-colors"
                       aria-label="WhatsApp">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center hover:border-white transition-colors"
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

@php
    // Maps a product name to the original local 3D render asset.
    // The central visualizer always uses these static images — NOT the DB product photo.
    $staticImageFor = function (string $name): string {
        $n = strtolower($name);
        if (str_contains($n, 'sosis') && str_contains($n, 'mozza')) return asset('assets/img/custom_sosis_mozza.png');
        if (str_contains($n, 'full mozza') || (str_contains($n, 'mozza') && !str_contains($n, 'sosis'))) return asset('assets/img/custom_mozza.png');
        if (str_contains($n, 'full sosis') || (str_contains($n, 'sosis') && !str_contains($n, 'mozza')))  return asset('assets/img/custom_sosis.png');
        if (str_contains($n, 'original'))    return asset('assets/img/custom_original.png');
        if (str_contains($n, 'potato'))      return asset('assets/img/custom_potato.png');
        if (str_contains($n, 'ramen'))       return asset('assets/img/custom_ramen.png');
        if (str_contains($n, 'ketchup'))     return asset('assets/img/custom_ketchup.png');
        if (str_contains($n, 'mayo'))        return asset('assets/img/custom_mayonnaise.png');
        if (str_contains($n, 'hot sauce') || str_contains($n, 'hot saos')) return asset('assets/img/custom_hotsauce.png');
        if (str_contains($n, 'cheese sauce') || str_contains($n, 'cheese saos')) return asset('assets/img/custom_cheesesauce.png');
        return asset('assets/img/custom_sosis_mozza.png'); // safe fallback
    };

    $mapProductsToJs = function ($col, bool $showPrice = true) use ($staticImageFor) {
        return ($col ?? collect())->values()->map(function ($p) use ($staticImageFor, $showPrice) {
            return [
                'id'         => $p->id,
                'name'       => strtoupper($p->name),
                'display'    => strtoupper($p->name),
                'image'      => $staticImageFor($p->name),
                'extra'      => (int) $p->price,
                'showPrice'  => $showPrice,
                'outOfStock' => $p->stock <= 0,
                'stock'      => (int) $p->stock,
            ];
        })->values()->all();
    };

    $jsIsian  = $mapProductsToJs($isianProducts  ?? collect(), false); // price hidden on Step 1
    $jsVarian = $mapProductsToJs($varianProducts ?? collect(), true);
    $jsSauce  = $mapProductsToJs($sauceProducts  ?? collect(), true);
@endphp
<script>
var ISIAN_DATA  = @json($jsIsian);
var VARIAN_DATA = @json($jsVarian);
var SAUCE_DATA  = @json($jsSauce);
</script>

<script>
$(function () {
    /* ─── Data ───────────────────────────────────────────────── */
    var BASE_PRICE = 16000;

    var STEPS = [
        {
            num: 1,
            title: 'Pilih Isi',
            instruction: 'Pilih Isi Corndog',
            description: 'Geser atau gunakan tombol untuk memilih isi favoritemu',
            nextLabel: 'Next Pilih Varian',
            items: ISIAN_DATA
        },
        {
            num: 2,
            title: 'Pilih Varian',
            instruction: 'Pilih Varian Corndog',
            description: 'Geser atau gunakan tombol untuk memilih varian favoritemu',
            nextLabel: 'Next Pilih Saos',
            items: VARIAN_DATA
        },
        {
            num: 3,
            title: 'Pilih Saos',
            instruction: 'Pilih Saos Corndog',
            description: 'Geser atau gunakan tombol untuk memilih saos favoritemu',
            nextLabel: 'Next Review &amp; Order',
            multiSelect: true,
            maxSelect: 2,
            items: SAUCE_DATA
        },
        {
            num: 4,
            title: 'Review &amp; Order',
            nextLabel: 'Tambah ke Keranjang 🛒'
        }
    ];

    /* ─── State ─────────────────────────────────────────────── */
    var state = {
        step: 0,          // 0-indexed (0=step1, 1=step2, 2=step3, 3=step4)
        idx: [0, 0, 0],   // selected index per step 1-3
        sauces: []         // for step 3 multi-select
    };

    /* ─── Helpers ───────────────────────────────────────────── */
    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function currentItems() {
        return state.step < 3 ? STEPS[state.step].items : [];
    }

    function currentIdx() {
        return state.idx[state.step] || 0;
    }

    /* ─── Render stepper ────────────────────────────────────── */
    function renderStepper() {
        var el = document.getElementById('stepper');
        var html = '';
        STEPS.forEach(function (s, i) {
            var active = i <= state.step;
            var current = i === state.step;
            // Connector
            if (i > 0) {
                html += '<div class="step-line' + (i <= state.step ? ' done' : '') + '"></div>';
            }
            // Circle
            html += '<div class="flex flex-col items-center gap-1">'
                  + '<div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm border-2 transition-all"'
                  + ' style="'
                  + (current
                      ? 'background-color: var(--color-primary); border-color: var(--color-primary); color: white;'
                      : active
                          ? 'background-color: var(--color-primary); border-color: var(--color-primary); color: white; opacity:0.6;'
                          : 'background-color: white; border-color: #d1d5db; color: #9ca3af;')
                  + '">' + s.num + '</div>'
                  + '<span class="text-[10px] font-medium whitespace-nowrap hidden sm:block"'
                  + ' style="color:' + (active ? 'var(--color-primary)' : '#9ca3af') + ';">'
                  + s.title + '</span>'
                  + '</div>';
        });
        el.innerHTML = html;
    }

    function sauceTransform(display) {
    var map = {
        'KETCHUP'      : 'scale(1.15) translate(0px, -16px)',
        'MAYONNAISE'   : 'scale(1.15) translate(0px, -16px)',
        'HOT SAUCE'    : 'scale(1.15) translate(-3px, -16px)',
        'CHEESE SAUCE' : 'scale(1.15) translate(-3px, -16px)'
    };

    return map[display] || 'scale(1.15) translate(0px, -16px)';
}

function getBaseTransform(display) {
    var map = {
        'SOSIS & MOZZA' : 'scale(1.15) translateY(-8px)',
        'FULL MOZZA'    : 'scale(1.10) translateY(-8px)',
        'FULL SOSIS'    : 'scale(1.10) translateY(-8px)',

        'ORIGINAL'      : 'scale(1.30) translateY(-8px)',
        'POTATO'        : 'scale(1.30) translateY(-8px)',
        'RAMEN'         : 'scale(1.30) translateY(-8px)'
    };

    return map[display] || 'scale(1.0)';
}

function getSauceTransform(sauceDisplay, variantDisplay) {
    var defaultMap = {
        'KETCHUP'      : 'scale(1.15) translate(0px, -16px)',
        'MAYONNAISE'   : 'scale(1.10) translate(0px, -16px)',
        'HOT SAUCE'    : 'scale(1.15) translate(0px, -16px)',
        'CHEESE SAUCE' : 'scale(1.15) translate(0px, -16px)'
    };

    var ramenMap = {
        'KETCHUP'      : 'scale(1.08) translate(6px, -18px)',
        'MAYONNAISE'   : 'scale(1.08) translate(6px, -8px)',
        'HOT SAUCE'    : 'scale(1.08) translate(6px, -18px)',
        'CHEESE SAUCE' : 'scale(1.08) translate(6px, -18px)'
    };

    if (variantDisplay === 'RAMEN') {
        return ramenMap[sauceDisplay] || 'scale(1.08) translate(6px, -18px)';
    }

    return defaultMap[sauceDisplay] || 'scale(1.15) translate(0px, -16px)';
}

    /* ─── Render carousel ───────────────────────────────────── */
    function renderCarousel(animate) {
        var step = STEPS[state.step];
        if (state.step >= 3) return; // review step has no carousel

        var items = step.items;
        if (!items || items.length === 0) return;
        var idx = currentIdx();
        var item = items[idx];
        if (!item) return;

        var isOOS = !!(item.outOfStock);

        // OOS badge and greyscale on preview
        var oosBadge = document.getElementById('oos-badge');
        var carouselCenter = document.getElementById('carousel-center');
        if (oosBadge) {
            if (isOOS) {
                oosBadge.classList.remove('hidden');
                carouselCenter.classList.add('oos-active');
            } else {
                oosBadge.classList.add('hidden');
                carouselCenter.classList.remove('oos-active');
            }
        }

        // Update base image only on steps 1 & 2 — step 3 keeps the selected variant underneath
        if (!step.multiSelect) {
            // Inline scale values per asset — bypasses Tailwind JIT so dynamic strings are always applied
           var baseTransform = getBaseTransform(item.display);

if (animate) {
    $('#base-corndog').addClass('fading');
    setTimeout(function () {
        $('#base-corndog')
            .attr('src', item.image)
            .css('transform', baseTransform)
            .removeClass('fading');
    }, 180);
} else {
    $('#base-corndog')
        .attr('src', item.image)
        .css('transform', baseTransform);
}
        }

        // Update sauce overlay — preview current carousel item on step 3, clear otherwise
        if (step.multiSelect) {
            var selectedVariant = STEPS[1].items && STEPS[1].items[state.idx[1]];
            var sauceTransformVal = selectedVariant
                ? getSauceTransform(item.display, selectedVariant.display)
                : 'scale(1.15) translate(0px, -16px)';
            $('#overlay-sauce')
                .attr('src', item.image)
                .show()
                .css('transform', sauceTransformVal);

        } else {
            $('#overlay-sauce')
                .removeAttr('src')
                .hide();
        }

        // Label
        document.getElementById('carousel-label-text').innerHTML = item.name;
        var priceEl = document.getElementById('carousel-label-price');
        if (isOOS) {
            priceEl.textContent = '— Habis';
            priceEl.style.color = '#dc2626';
            priceEl.classList.remove('hidden');
        } else if (item.showPrice && item.extra && item.extra > 0) {
            priceEl.textContent = '+ ' + rupiah(item.extra);
            priceEl.style.color = '';
            priceEl.classList.remove('hidden');
        } else {
            priceEl.style.color = '';
            priceEl.classList.add('hidden');
        }

        // Dots
        var dotsEl = document.getElementById('carousel-dots');
        var dotsHtml = '';
        items.forEach(function (_, i) {
            dotsHtml += '<div class="w-2.5 h-2.5 rounded-full transition-all"'
                      + ' style="background-color:' + (i === idx ? 'var(--color-primary)' : '#d1d5db') + ';'
                      + (i === idx ? ' transform:scale(1.2);' : '') + '">'
                      + '</div>';
        });
        dotsEl.innerHTML = dotsHtml;

        // Sauce chips (step 3)
        var chipsWrap = document.getElementById('sauce-chips');
        if (step.multiSelect) {
            chipsWrap.style.display = 'flex';
            var chipsHtml = '';
            state.sauces.forEach(function (s) {
                chipsHtml += '<span class="text-xs font-bold px-3 py-1 rounded-full"'
                           + ' style="background-color: var(--color-primary); color: white;">'
                           + STEPS[2].items[s].display + '</span>';
            });
            if (state.sauces.length === 0) {
                chipsHtml = '<span class="text-xs text-gray-400">Belum ada saos dipilih</span>';
            }
            chipsWrap.innerHTML = chipsHtml;
        } else {
            chipsWrap.style.display = 'none';
        }

        // Sauce add button
        var addWrap = document.getElementById('add-sauce-wrap');
        var addBtn = document.getElementById('add-sauce-btn');
        if (step.multiSelect) {
            addWrap.classList.remove('hidden');
            if (isOOS) {
                addBtn.innerHTML = 'Habis';
                addBtn.style.backgroundColor = '#f3f4f6';
                addBtn.style.color = '#9ca3af';
                addBtn.style.borderColor = '#d1d5db';
            } else {
                var alreadySelected = state.sauces.indexOf(idx) !== -1;
                var maxReached = state.sauces.length >= step.maxSelect && !alreadySelected;
                if (alreadySelected) {
                    addBtn.innerHTML = '&#10003; Ditambahkan';
                    addBtn.style.backgroundColor = 'var(--color-primary)';
                    addBtn.style.color = 'white';
                    addBtn.style.borderColor = 'var(--color-primary)';
                } else if (maxReached) {
                    addBtn.innerHTML = 'Max ' + step.maxSelect + ' saos';
                    addBtn.style.backgroundColor = '#f3f4f6';
                    addBtn.style.color = '#9ca3af';
                    addBtn.style.borderColor = '#d1d5db';
                } else {
                    addBtn.innerHTML = '+ Add Sauce';
                    addBtn.style.backgroundColor = 'white';
                    addBtn.style.color = 'var(--color-primary)';
                    addBtn.style.borderColor = 'var(--color-primary)';
                }
            }
        } else {
            addWrap.classList.add('hidden');
        }
    }

    /* ─── Render instruction card ──────────────────────────── */
    function renderStepCard() {
        var step = STEPS[state.step];
        if (state.step >= 3) {
            document.getElementById('step-card').classList.add('hidden');
            return;
        }
        document.getElementById('step-card').classList.remove('hidden');
        document.getElementById('step-card-num').textContent = step.num;
        document.getElementById('step-card-title').textContent = step.instruction;
        document.getElementById('step-card-desc').textContent = step.description;
    }

    /* ─── Render ingredient selection cards (steps 1 & 2) ──── */
    function renderIngredientCards() {
        var grid = document.getElementById('ingredient-grid');
        if (!grid) return;
        // Steps 3+ use carousel + add-button; clear the grid
        if (state.step >= 2) { grid.innerHTML = ''; return; }

        var step = STEPS[state.step];
        var items = step.items;
        var selectedIdx = state.idx[state.step];
        var colClass = items.length === 3 ? 'grid-cols-3' : 'grid-cols-2';
        grid.className = 'grid gap-2 mt-4 ' + colClass;

        var html = '';
        items.forEach(function (item, i) {
            var sel = i === selectedIdx;
            var isItemOOS = !!(item && item.outOfStock);
            var thumbStyle = item.display === 'SOSIS & MOZZA' ? 'transform:scale(1.15);' : '';
            var thumbHoverClass = (!isItemOOS && item.display !== 'SOSIS & MOZZA') ? 'group-hover:scale-110' : '';
            var oosClasses = isItemOOS
                ? ' opacity-50 grayscale cursor-not-allowed pointer-events-none'
                : ' cursor-pointer';
            html += '<div class="ingredient-card group rounded-xl p-3 text-center border-2 transition-all select-none' + oosClasses + '"'
                  + ' data-idx="' + i + '"'
                  + ' style="border-color:' + (sel && !isItemOOS ? 'var(--color-primary)' : '#e5e7eb') + ';'
                  + 'background-color:' + (sel && !isItemOOS ? '#fff8f5' : 'white') + ';">'
                  + '<div class="w-20 h-20 md:w-28 md:h-28 flex-shrink-0 flex items-center justify-center p-2 mx-auto mb-3 bg-white rounded-xl shadow-sm border-2 border-transparent overflow-hidden">'
                  + '<img src="' + item.image + '" class="w-full h-full object-contain object-center drop-shadow-md transition-transform duration-300 ' + thumbHoverClass + '" style="' + thumbStyle + '" alt="' + item.display + '">'
                  + '</div>'
                  + '<p class="text-xs font-bold leading-tight truncate"'
                  + ' style="color:' + (sel && !isItemOOS ? 'var(--color-primary)' : '#374151') + ';">'
                  + item.display + '</p>';
            if (isItemOOS) {
                html += '<p class="text-[10px] font-bold mt-0.5" style="color:#dc2626;">Habis</p>';
            } else if (item.showPrice && item.extra > 0) {
                html += '<p class="text-[10px] font-semibold mt-0.5" style="color:var(--color-primary);">+'
                      + rupiah(item.extra) + '</p>';
            }
            html += '</div>';
        });
        grid.innerHTML = html;

        $(grid).find('.ingredient-card').on('click', function () {
            state.idx[state.step] = parseInt($(this).data('idx'));
            renderAll(true);
        });
    }

    /* ─── Render Next button ────────────────────────────────── */
    function renderNextBtn() {
        var step = STEPS[state.step];
        document.getElementById('btn-next-step').innerHTML = step.nextLabel || 'Next';
        var backBtn = document.getElementById('btn-back');
        if (state.step === 0) {
            backBtn.classList.add('hidden');
        } else {
            backBtn.classList.remove('hidden');
        }
    }

    /* ─── Render review panel ──────────────────────────────── */
    function renderReview() {
        var panel = document.getElementById('review-panel');
        if (state.step !== 3) {
            panel.classList.add('hidden');
            return;
        }
        panel.classList.remove('hidden');

        var isi    = STEPS[0].items && STEPS[0].items[state.idx[0]];
        var varian = STEPS[1].items && STEPS[1].items[state.idx[1]];
        if (!isi || !varian) { panel.classList.add('hidden'); return; }
        var saosList = state.sauces.map(function (i) { return STEPS[2].items && STEPS[2].items[i]; }).filter(Boolean);

        var html = '';

        // Isi card
        html += reviewCard('Isian', isi.image, isi.display, null);
        // Varian card
        html += reviewCard('Varian', varian.image, varian.display, varian.extra);
        // Saos card
        var saosDisplay = saosList.length ? saosList.map(function (s) { return s.display; }).join(' + ') : 'Tidak ada';
        var lastSaos = saosList.length ? saosList[saosList.length - 1] : null;
        var saosImg = lastSaos ? lastSaos.image : '';
        html += reviewCard('Saos', saosImg, saosDisplay, null);

        document.getElementById('review-items').innerHTML = html;

        var extra = varian.extra || 0;
        document.getElementById('review-base').textContent = rupiah(BASE_PRICE);
        document.getElementById('review-extra').textContent = extra > 0 ? '+ ' + rupiah(extra) : 'Gratis';
        document.getElementById('review-extra-row').style.display = extra > 0 ? '' : 'none';
        document.getElementById('review-total').textContent = rupiah(BASE_PRICE + extra);
    }

    function reviewCard(label, img, name, extra) {
        var imgHtml = img
            ? '<img src="' + img + '" alt="' + name + '" class="w-full h-full object-contain object-center drop-shadow-md">'
            : '<span class="text-xs font-bold text-gray-400">-</span>';

        return '<div class="flex flex-col items-center bg-gray-50 rounded-xl p-3 gap-2">'
             + '<p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">' + label + '</p>'
             + '<div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 flex items-center justify-center p-2 mx-auto rounded-2xl overflow-hidden"'
             + '     style="background-color: #FDECD8;">'
             + imgHtml + '</div>'
             + '<p class="font-bold text-xs text-center leading-tight" style="color:var(--color-primary);">' + name + '</p>'
             + (extra > 0 ? '<p class="text-[11px] font-semibold" style="color:var(--color-primary);">+ ' + rupiah(extra) + '</p>' : '')
             + '</div>';
    }

    /* ─── Full re-render ────────────────────────────────────── */
    function renderAll(animate) {
        renderStepper();
        if (state.step < 3) renderCarousel(animate);
        renderStepCard();
        renderIngredientCards();
        renderNextBtn();
        renderReview();

        var isReview = state.step === 3;

        // Always keep carousel-center visible — step 4 shows assembled preview
        document.getElementById('carousel-center').style.display = '';
        document.getElementById('btn-prev').style.display = isReview ? 'none' : '';
        document.getElementById('btn-next').style.display = isReview ? 'none' : '';

        if (isReview) {
            var isiItem    = STEPS[0].items && STEPS[0].items[state.idx[0]];
            var varianItem = STEPS[1].items && STEPS[1].items[state.idx[1]];
            if (!isiItem || !varianItem) {
                // Required selections missing — drop back to step 0
                state.step = 0;
                renderAll(false);
                return;
            }
            var lastSauce  = state.sauces.length ? STEPS[2].items && STEPS[2].items[state.sauces[state.sauces.length - 1]] : null;

            // Review preview must use the exact same size/position as the final selected variant
            $('#base-corndog')
                .attr('src', varianItem.image)
                .css('transform', getBaseTransform(varianItem.display));

            $('#middle-varian-wrap').hide();

            // Review preview uses the last selected sauce, not the first one
            if (lastSauce) {
                $('#overlay-sauce')
                    .attr('src', lastSauce.image)
                    .show()
                    .css('transform', getSauceTransform(lastSauce.display, varianItem.display));
            } else {
                $('#overlay-sauce')
                    .removeAttr('src')
                    .hide();
            }

            // Hide carousel UI chrome — only the preview blob is shown
            document.getElementById('carousel-dots').style.display  = 'none';
            document.getElementById('carousel-label').style.display = 'none';
            document.getElementById('sauce-chips').style.display    = 'none';
        } else {
            document.getElementById('middle-varian-wrap').style.display = 'none';
            document.getElementById('carousel-dots').style.display      = '';
            document.getElementById('carousel-label').style.display     = '';
            document.getElementById('sauce-chips').style.display        = state.step === 2 ? 'flex' : 'none';
        }
    }

    /* ─── Navigation ────────────────────────────────────────── */
    function prevItem() {
        var items = currentItems();
        if (!items.length) return;
        state.idx[state.step] = (state.idx[state.step] - 1 + items.length) % items.length;
        renderAll(true);
    };

    function nextItem() {
        var items = currentItems();
        if (!items.length) return;
        state.idx[state.step] = (state.idx[state.step] + 1) % items.length;
        renderAll(true);
    };

    function toggleSauce() {
        var idx = currentIdx();
        var currentSauce = STEPS[2].items && STEPS[2].items[idx];
        if (currentSauce && currentSauce.outOfStock) return;
        var sauceIdx = state.sauces.indexOf(idx);
        if (sauceIdx !== -1) {
            state.sauces.splice(sauceIdx, 1);
        } else {
            if (state.sauces.length < STEPS[2].maxSelect) {
                state.sauces.push(idx);
            }
        }
        renderAll(false);
    };

    function showToast(msg, isError) {
        var bg = isError ? '#c00f0c' : '#A6171C';
        var $t = $('<div>').text(msg).css({
            position: 'fixed', bottom: '28px', left: '50%',
            transform: 'translateX(-50%)',
            background: bg, color: '#fff',
            padding: '11px 28px', borderRadius: '999px',
            fontWeight: '700', fontSize: '14px',
            zIndex: 99999, boxShadow: '0 4px 24px rgba(0,0,0,0.22)',
            whiteSpace: 'nowrap'
        }).appendTo('body');
        setTimeout(function () { $t.fadeOut(300, function () { $(this).remove(); }); }, 2800);
    }

    function nextStep() {
        if (state.step < 3) {
            var stepItems = STEPS[state.step].items;
            // Steps 0 (isian) and 1 (varian) are required — block if DB returned nothing
            if (state.step < 2 && (!stepItems || stepItems.length === 0)) {
                showToast('Tidak ada pilihan tersedia untuk langkah ini.', true);
                return;
            }
            if (stepItems && stepItems.length) {
                var picked = stepItems[state.idx[state.step]];
                if (picked && picked.outOfStock) {
                    showToast('Item ini sedang habis. Silakan pilih item lain.', true);
                    return;
                }
            }
            state.step++;
            renderAll(false);
            return;
        }

        // ── Step 4: Add custom corndog to cart ──────────────────
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

        var isiItem    = STEPS[0].items[state.idx[0]];
        var varianItem = STEPS[1].items[state.idx[1]];
        var sauceNames = state.sauces.map(function (i) { return STEPS[2].items[i].display; });
        var totalPrice = BASE_PRICE + (varianItem.extra || 0);
        var description = 'Isi: ' + isiItem.display
                        + ' | Varian: ' + varianItem.display
                        + (sauceNames.length ? ' | Saos: ' + sauceNames.join(', ') : '');

        var $btn        = $('#btn-next-step');
        var origLabel   = STEPS[3].nextLabel || 'Tambah ke Keranjang 🛒';
        $btn.prop('disabled', true).text('Menambahkan...');

        $.ajax({
            url:    '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                is_custom:    true,
                name:         'Custom Corndog',
                price:        totalPrice,
                qty:          1,
                image:        varianItem.image,
                varian_image: varianItem.image,
                sauce_image:  state.sauces.length ? STEPS[2].items[state.sauces[0]].image : '',
                description:  description,
                isi:          isiItem.display,
                varian:       varianItem.display,
                sauces:       sauceNames.join(', '),
            },
            success: function (response) {
                if (response && response.success) {
                    // Update cart badge counter
                    $('#cart-badge').text(response.count);

                    // Show success state on button
                    $btn.css({ 'background-color': '#16a34a', 'color': 'white' })
                        .html('&#10003; Berhasil Ditambahkan!');

                    // Redirect to cart after short delay
                    setTimeout(function () {
                        window.location.href = '{{ route("cart") }}';
                    }, 1500);
                } else {
                    // Server said success:false — restore and notify
                    $btn.prop('disabled', false)
                        .css({ 'background-color': '', 'color': '' })
                        .html(origLabel);
                    showToast('Gagal menambahkan ke keranjang.', true);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false)
                    .css({ 'background-color': '', 'color': '' })
                    .html(origLabel);
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Gagal menambahkan ke keranjang. Silakan coba lagi.';
                showToast(msg, true);
            }
        });
    };

    function prevStep() {
        if (state.step <= 0) return;
        state.step--;
        renderAll(false);
    };

    /* ─── jQuery event bindings ─────────────────────────────── */
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    $('#btn-prev').on('click', prevItem);
    $('#btn-next').on('click', nextItem);
    $('#btn-back').on('click', prevStep);
    $('#btn-next-step').on('click', nextStep);
    $('#add-sauce-btn').on('click', toggleSauce);

    /* ─── Touch/swipe support ───────────────────────────────── */
    var touchStartX = 0;
    $('#carousel-center').on('touchstart', function (e) {
        touchStartX = e.originalEvent.touches[0].clientX;
    }).on('touchend', function (e) {
        var dx = e.originalEvent.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) > 40) {
            dx < 0 ? nextItem() : prevItem();
        }
    });

    /* ─── Init ──────────────────────────────────────────────── */
    renderAll(false);

});
</script>

</body>
</html>
