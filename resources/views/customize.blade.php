<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Corndog — Corndog-Ku</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #FFFDDB; }

        /* Organic blob shape behind corndog */
        .corndog-blob {
            border-radius: 58% 42% 46% 54% / 52% 44% 56% 48%;
            background-color: #FDECD8;
        }

        /* Dashed pill for selection label */
        .selection-pill {
            border: 2.5px dashed #FFBE54;
            background-color: #fff;
        }

        /* Stepper dot connector */
        .step-line {
            flex: 1;
            border-top: 2px dashed #d1d5db;
            min-width: 24px;
        }
        .step-line.done {
            border-color: #A6171C;
        }

        /* Carousel dot size */
        #carousel-dots > div { width: 0.875rem; height: 0.875rem; }

        /* Corndog base transition */
        #base-corndog { transition: opacity 0.2s ease, transform 0.2s ease; }
        #base-corndog.fading { opacity: 0; transform: scale(0.95); }

        /* Overlay: instant sauce swap */
        #overlay-sauce { transition: none; }

        /* Stepper override — compact for viewport fit */
        #stepper > div > div:first-child {
            width: 2rem !important;
            height: 2rem !important;
            font-size: 0.875rem !important;
        }
        #stepper > div > span {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
        }
        .step-line { min-width: 36px; }

        /* Hide scrollbar cross-browser */
        .hide-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* Sauce chip badges */
        .sauce-chip {
            border: 2px solid #A6171C;
            background: white;
            color: #A6171C;
        }
        .sauce-chip.added {
            background: #A6171C;
            color: white;
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
                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold
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
<section class="relative overflow-hidden flex-1 flex flex-col">

    {{-- Decorative large circles --}}
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
         style="background-color: rgba(253,236,216,0.85);"></div>
    <div class="absolute top-48 -right-8 w-24 h-24 rounded-full pointer-events-none"
         style="background-color: rgba(253,236,216,0.6);"></div>
    <div class="absolute bottom-32 -left-16 w-56 h-56 rounded-full pointer-events-none"
         style="background-color: rgba(253,236,216,0.5);"></div>

    {{-- Small amber dots --}}
    <div class="absolute top-16 left-[12%] w-3 h-3 rounded-full pointer-events-none"
         style="background-color: #FFBE54;"></div>
    <div class="absolute top-40 left-[8%] w-2.5 h-2.5 rounded-full pointer-events-none"
         style="background-color: #FFBE54; opacity:0.7;"></div>
    <div class="absolute top-72 right-[14%] w-2.5 h-2.5 rounded-full pointer-events-none"
         style="background-color: #FFBE54;"></div>
    <div class="absolute top-24 right-[38%] w-2 h-2 rounded-full pointer-events-none"
         style="background-color: #FFBE54; opacity:0.6;"></div>
    <div class="absolute bottom-48 right-[10%] w-3 h-3 rounded-full pointer-events-none"
         style="background-color: #FFBE54; opacity:0.5;"></div>

    <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-12 pt-5 pb-0 relative z-10 flex-1 flex flex-col overflow-hidden">

        {{-- ── Top row: Title + Stepper ──────────────────────── --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-4 mb-4 lg:mb-5 flex-none">

            {{-- CUSTOM CORNDOG title --}}
            <div class="flex-none">
                {{-- Squiggly underline decoration (SVG) --}}
                <div class="relative leading-none">
                    <div class="text-4xl sm:text-5xl font-black leading-none tracking-tight">
                        <span style="color: #1a1a1a;">CUSTOM</span>
                        <span class="inline-block ml-1 text-xl sm:text-2xl" style="color: #FFBE54;">✦</span>
                    </div>
                    <div class="text-4xl sm:text-5xl font-black leading-none tracking-tight"
                         style="color: var(--color-primary);">CORNDOG</div>
                    <svg class="mt-1" width="200" height="10" viewBox="0 0 200 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 5 Q18 1 34 5 Q50 9 66 5 Q82 1 98 5 Q114 9 130 5 Q146 1 162 5 Q178 9 194 5"
                              stroke="#A6171C" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
                {{-- Subtitle pill --}}
                <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm"
                     style="background-color: #FFBE54; color: #1a1a1a;">
                    Buat Corndog Favoritemu Sesuai Seleramu
                    <svg class="w-4 h-4 flex-none" viewBox="0 0 24 24" fill="#A6171C">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
            </div>

            {{-- Step stepper --}}
            <div class="flex items-center gap-0 flex-none" id="stepper">
                {{-- Rendered by JS --}}
            </div>
        </div>

        {{-- ── Carousel + Instruction layout ─────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch flex-1 overflow-hidden">

            {{-- LEFT: Carousel --}}
            <div class="flex items-center gap-4 justify-center overflow-hidden">

            {{-- Left arrow --}}
            <button id="btn-prev"
                    type="button"
                    class="flex-none w-16 h-16 md:w-20 md:h-20 rounded-full bg-white shadow-md flex items-center justify-center
                           font-bold text-2xl md:text-4xl hover:shadow-lg transition-shadow active:scale-95"
                    style="color: var(--color-primary);">
                &#8249;
            </button>

            {{-- Center: blob + image + label + dots --}}
            <div id="carousel-center" class="flex-1 flex flex-col items-center justify-center relative">

                {{-- Peach blob --}}
                <div class="corndog-blob w-80 h-80 sm:w-96 sm:h-96 lg:w-[500px] lg:h-[500px]
                            flex items-center justify-center relative">
                    <img id="carousel-img"
                         src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
                         alt="Corndog preview"
                         class="h-[280px] sm:h-[380px] lg:h-[560px] w-auto object-contain drop-shadow-xl">
                    {{-- Spark accent near image --}}
                    <div class="absolute top-3 right-3 text-3xl pointer-events-none"
                         style="color: #A6171C;">✦</div>
                </div>

                {{-- Selection name pill --}}
                <div class="selection-pill mt-5 px-8 py-3 rounded-full text-center min-w-[220px]"
                     id="carousel-label">
                    <span class="font-black text-xl tracking-widest"
                          id="carousel-label-text"
                          style="color: var(--color-primary);">SOSIS &amp; MOZZA</span>
                    <div id="carousel-label-price" class="text-sm font-semibold mt-1 hidden"
                         style="color: var(--color-primary);"></div>
                </div>

                {{-- Dot indicators --}}
                <div id="carousel-dots" class="flex items-center gap-3 mt-5"></div>

                {{-- Step 3 sauce chips (hidden except step 3) --}}
                <div id="sauce-chips" class="hidden flex-wrap gap-2 justify-center mt-4 max-w-sm"></div>
            </div>

            {{-- Right arrow --}}
            <button id="btn-next"
                    type="button"
                    class="flex-none w-16 h-16 md:w-20 md:h-20 rounded-full bg-white shadow-md flex items-center justify-center
                           font-bold text-2xl md:text-4xl hover:shadow-lg transition-shadow active:scale-95"
                    style="color: var(--color-primary);">
                &#8250;
            </button>

            </div>{{-- /.carousel left column --}}

            {{-- RIGHT: scrollable column containing step card + review + buttons --}}
            <div class="h-full overflow-y-auto hide-scrollbar flex flex-col gap-4 py-2 pb-24">

                {{-- Step instruction card --}}
                <div id="step-card"
                     class="bg-white rounded-2xl p-8 md:p-10 shadow-lg">
                    <div class="flex items-start gap-4">
                        <div id="step-card-num"
                             class="w-12 h-12 rounded-full flex items-center justify-center
                                    text-white font-bold text-xl flex-none"
                             style="background-color: var(--color-primary);">1</div>
                        <div>
                            <p id="step-card-title" class="font-bold text-xl lg:text-2xl" style="color: var(--color-black);">
                                Pilih Isi Corndog
                            </p>
                            <p id="step-card-desc" class="text-base text-gray-500 mt-2 leading-relaxed">
                                Geser atau gunakan tombol untuk memilih isi favoritemu
                            </p>
                        </div>
                    </div>
                    {{-- Sauce add button (step 3 only) --}}
                    <div id="add-sauce-wrap" class="hidden mt-5 pt-5 border-t border-gray-100">
                        <p class="text-sm text-right mb-3 font-semibold" style="color: var(--color-primary);">Max 2 sauce*</p>
                        <button id="add-sauce-btn"
                                type="button"
                                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl
                                       text-base font-bold border-2 transition-colors hover:opacity-80"
                                style="border-color: var(--color-primary); color: var(--color-primary);">
                            <span>+</span> Add Sauce
                        </button>
                    </div>
                </div>

                {{-- Review panel (step 4 only) --}}
                <div id="review-panel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg mb-5" style="color: var(--color-black);">Ringkasan Pesananmu</h3>
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
                <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                    <button id="btn-back"
                            type="button"
                            class="hidden sm:inline-flex items-center justify-center px-10 py-5 rounded-2xl
                                   font-bold text-base border-2 transition-opacity hover:opacity-70"
                            style="border-color: var(--color-primary); color: var(--color-primary);">
                        &#8592; Kembali
                    </button>
                    <button id="btn-next-step"
                            type="button"
                            class="flex-1 max-w-4xl mx-auto w-full py-5 rounded-2xl font-bold
                                   text-xl md:text-2xl tracking-wide
                                   transition-opacity hover:opacity-85 active:scale-[0.99]"
                            style="background-color: var(--color-primary); color: var(--color-white);">
                        Next Pilih Varian
                    </button>
                </div>

            </div>{{-- /.right scrollable column --}}
        </div>{{-- /.grid --}}
    </div>{{-- /.container --}}
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
            items: [
                { name: 'SOSIS &amp; MOZZA', display: 'SOSIS & MOZZA', image: '{{ asset("assets/img/custom_sosis_mozza.png") }}', extra: 0 },
                { name: 'FULL MOZZA',         display: 'FULL MOZZA',    image: '{{ asset("assets/img/custom_mozza.png") }}',       extra: 0 },
                { name: 'FULL SOSIS',         display: 'FULL SOSIS',   image: '{{ asset("assets/img/custom_sosis.png") }}',       extra: 0 }
            ]
        },
        {
            num: 2,
            title: 'Pilih Varian',
            instruction: 'Pilih Varian Corndog',
            description: 'Geser atau gunakan tombol untuk memilih varian favoritemu',
            nextLabel: 'Next Pilih Saos',
            items: [
                { name: 'ORIGINAL', display: 'ORIGINAL', image: '{{ asset("assets/img/custom_original.png") }}', extra: 0 },
                { name: 'POTATO',   display: 'POTATO',   image: '{{ asset("assets/img/custom_potato.png") }}',   extra: 4000 },
                { name: 'RAMEN',    display: 'RAMEN',    image: '{{ asset("assets/img/custom_ramen.png") }}',    extra: 3000 }
            ]
        },
        {
            num: 3,
            title: 'Pilih Saos',
            instruction: 'Pilih Saos Corndog',
            description: 'Geser atau gunakan tombol untuk memilih saos favoritemu',
            nextLabel: 'Next Review &amp; Order',
            multiSelect: true,
            maxSelect: 2,
            items: [
                { name: 'KETCHUP',      display: 'KETCHUP',      image: '{{ asset("assets/img/custom_ketchup.png") }}' },
                { name: 'MAYONNAISE',   display: 'MAYONNAISE',   image: '{{ asset("assets/img/custom_mayonnaise.png") }}' },
                { name: 'HOT SAUCE',    display: 'HOT SAUCE',    image: '{{ asset("assets/img/custom_hotsauce.png") }}' },
                { name: 'CHEESE SAUCE', display: 'CHEESE SAUCE', image: '{{ asset("assets/img/custom_cheesesauce.png") }}' }
            ]
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

    /* ─── Render carousel ───────────────────────────────────── */
    function renderCarousel(animate) {
        var step = STEPS[state.step];
        if (state.step >= 3) return; // review step has no carousel

        var items = step.items;
        var idx = currentIdx();
        var item = items[idx];

        // Image with fade transition
        var imgEl = document.getElementById('carousel-img');
        if (animate) {
            imgEl.classList.add('fading');
            setTimeout(function () {
                imgEl.src = item.image;
                imgEl.classList.remove('fading');
            }, 180);
        } else {
            imgEl.src = item.image;
        }

        // Label
        document.getElementById('carousel-label-text').innerHTML = item.name;
        var priceEl = document.getElementById('carousel-label-price');
        if (item.extra && item.extra > 0) {
            priceEl.textContent = '+ ' + rupiah(item.extra);
            priceEl.classList.remove('hidden');
        } else {
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

        var isi    = STEPS[0].items[state.idx[0]];
        var varian = STEPS[1].items[state.idx[1]];
        var saosList = state.sauces.map(function (i) { return STEPS[2].items[i]; });

        var html = '';

        // Isi card
        html += reviewCard('Isian', isi.image, isi.display, null);
        // Varian card
        html += reviewCard('Varian', varian.image, varian.display, varian.extra);
        // Saos card
        var saosDisplay = saosList.length ? saosList.map(function (s) { return s.display; }).join(' + ') : 'Tidak ada';
        var saosImg = saosList.length ? saosList[0].image : '{{ asset("assets/img/logo.png") }}';
        html += reviewCard('Saos', saosImg, saosDisplay, null);

        document.getElementById('review-items').innerHTML = html;

        var extra = varian.extra || 0;
        document.getElementById('review-base').textContent = rupiah(BASE_PRICE);
        document.getElementById('review-extra').textContent = extra > 0 ? '+ ' + rupiah(extra) : 'Gratis';
        document.getElementById('review-extra-row').style.display = extra > 0 ? '' : 'none';
        document.getElementById('review-total').textContent = rupiah(BASE_PRICE + extra);
    }

    function reviewCard(label, img, name, extra) {
        return '<div class="flex flex-col items-center bg-gray-50 rounded-xl p-4 gap-2">'
             + '<p class="text-xs font-bold text-gray-400 uppercase tracking-widest">' + label + '</p>'
             + '<div class="w-20 h-20 rounded-full flex items-center justify-center overflow-hidden"'
             + '     style="background-color: #FDECD8;">'
             + '<img src="' + img + '" alt="' + name + '" class="w-16 h-16 object-contain"></div>'
             + '<p class="font-bold text-sm text-center" style="color:var(--color-primary);">' + name + '</p>'
             + (extra > 0 ? '<p class="text-xs font-semibold" style="color:var(--color-primary);">+ ' + rupiah(extra) + '</p>' : '')
             + '</div>';
    }

    /* ─── Full re-render ────────────────────────────────────── */
    function renderAll(animate) {
        renderStepper();
        if (state.step < 3) renderCarousel(animate);
        renderStepCard();
        renderNextBtn();
        renderReview();

        // Show/hide carousel elements
        var isReview = state.step === 3;
        document.getElementById('carousel-center').style.display = isReview ? 'none' : '';
        document.getElementById('btn-prev').style.display          = isReview ? 'none' : '';
        document.getElementById('btn-next').style.display          = isReview ? 'none' : '';
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

    function nextStep() {
        if (state.step >= 3) {
            alert('Pesananmu sudah ditambahkan ke keranjang!');
            return;
        }
        state.step++;
        renderAll(false);
    };

    function prevStep() {
        if (state.step <= 0) return;
        state.step--;
        renderAll(false);
    };

    /* ─── jQuery event bindings ─────────────────────────────── */
    $('#btn-prev').on('click', prevItem);
    $('#btn-next').on('click', nextItem);
    $('#btn-back').on('click', prevStep);
    $('#btn-next-step').on('click', nextStep);
    $('#add-sauce-btn').on('click', toggleSauce);

    /* ─── Touch/swipe support ───────────────────────────────── */
    var touchStartX = 0;
    $('#carousel-img').on('touchstart', function (e) {
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
