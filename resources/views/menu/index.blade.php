<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu &amp; Varian Rasa — Corndog-Ku</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #FFFDDB; }

        /* Category tab active state */
        .cat-tab.active {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }

        /* Sort dropdown */
        #sort-dropdown {
            display: none;
        }
        #sort-dropdown.open {
            display: block;
        }

        /* Product card hover */
        .product-card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .product-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.13);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="font-sans antialiased" style="color: var(--color-black);">

{{-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

        {{-- Brand --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}"
                 alt="Corndog-Ku"
                 class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-base tracking-tight hidden sm:inline"
                  style="color: var(--color-black);">Corndog-Ku</span>
        </a>

        {{-- Search bar (center) --}}
        <div class="flex-1 max-w-md relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input id="navbar-search"
                   type="search"
                   placeholder="Cari produk…"
                   class="w-full pl-9 pr-4 py-2 rounded-full text-sm border focus:outline-none focus:ring-2 focus:ring-red-200"
                   style="border-color: var(--color-border); background-color: #f9f9f9;">
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2 flex-none">

            {{-- Sort trigger --}}
            <button id="btn-sort-toggle" type="button"
                    class="relative w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
                    title="Urutkan Produk">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                </svg>
            </button>

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
                   class="hidden sm:inline-flex px-4 py-2 rounded-full text-sm font-semibold border transition-colors hover:opacity-80"
                   style="border-color: var(--color-border); color: var(--color-black);">Daftar</a>
                <a href="{{ route('login') }}"
                   class="inline-flex px-4 py-2 rounded-full text-sm font-semibold transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary); color: var(--color-white);">Log In</a>
            @endauth
        </div>
    </div>

    {{-- Sort dropdown (slides below navbar) --}}
    <div id="sort-dropdown"
         class="absolute top-full left-0 right-0 bg-white border-t z-20 shadow-lg"
         style="border-color: var(--color-border);">
        <div class="max-w-md mx-auto px-4 py-4">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Urutkan Produk</p>
            <div class="space-y-2 mb-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" name="sort-option" value="price-asc" class="accent-red-700">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition-colors">
                        Harga: Rendah ke Tinggi
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" name="sort-option" value="price-desc" class="accent-red-700">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition-colors">
                        Harga: Tinggi ke Rendah
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="radio" name="sort-option" value="default" checked class="accent-red-700">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition-colors">
                        Default
                    </span>
                </label>
            </div>
            <div class="flex gap-2">
                <button id="btn-sort-reset" type="button"
                        class="flex-1 py-2 rounded-xl text-sm font-semibold border transition-colors hover:bg-gray-50"
                        style="border-color: var(--color-border); color: #666;">
                    Reset
                </button>
                <button id="btn-sort-apply" type="button"
                        class="flex-1 py-2 rounded-xl text-sm font-bold transition-opacity hover:opacity-80"
                        style="background-color: var(--color-primary); color: white;">
                    Terapkan Urutan
                </button>
            </div>
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════
     CUSTOMIZE CORNDOG BANNER
══════════════════════════════════════════════════════════════ --}}
<section class="pt-6 pb-4 sm:pt-8 sm:pb-6" style="background-color: var(--color-light);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ url('/customize') }}"
           class="flex flex-col sm:flex-row items-center justify-between overflow-hidden rounded-2xl no-underline
                  transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
           style="background-color: var(--color-accent);
                  box-shadow: 0 4px 24px rgba(166,23,28,0.12);">
            <div class="flex-1 px-6 sm:px-10 py-6 text-center sm:text-left">
                <p class="text-xs font-bold tracking-widest mb-1" style="color: rgba(0,0,0,0.45);">HANYA DI CORNDOG-KU</p>
                <p class="text-2xl sm:text-3xl font-black leading-tight" style="color: var(--color-black);">
                    Buat Custom<br>
                    <span style="color: var(--color-primary);">Corndog-mu!</span>
                </p>
                <p class="text-sm font-semibold mt-2 mb-4" style="color: rgba(0,0,0,0.55);">
                    Pilih isi, varian, dan saos sesuai seleramu
                </p>
                <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm text-white"
                      style="background-color: var(--color-primary);">
                    Mulai Sekarang
                    <svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12"/>
                    </svg>
                </span>
            </div>
            <div class="hidden sm:flex flex-none items-end justify-center pr-6 h-36">
                <img src="{{ asset('assets/img/custom_sosis_mozza.png') }}"
                     alt="Custom Corndog"
                     class="h-full w-auto object-contain drop-shadow-lg">
            </div>
        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     CATEGORY FILTER + PRODUCT GRID
══════════════════════════════════════════════════════════════ --}}
@php
    $menuProducts = \App\Models\Product::with('category')->orderBy('category_id')->get();
    $categories   = ['Semua', 'Corndog Asin', 'Corndog Manis', 'Toppoki', 'Combo', 'Es Teler Kwentel', 'Bingsoo'];
@endphp

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Category tabs — horizontally scrollable on mobile --}}
    <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
            @foreach ($categories as $i => $cat)
                <button type="button"
                        class="cat-tab px-5 py-2 rounded-full text-sm font-semibold border whitespace-nowrap transition-all duration-150 hover:opacity-85 {{ $i === 0 ? 'active' : '' }}"
                        data-cat="{{ $cat }}"
                        style="{{ $i !== 0 ? 'border-color: var(--color-border); color: var(--color-black); background-color: var(--color-white);' : 'border-color: var(--color-primary);' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Active category label + result count --}}
    <div class="flex items-center justify-between mt-5 mb-4">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Menampilkan</span>
            <span id="active-cat-label" class="ml-1 text-xs font-bold" style="color: var(--color-primary);">Semua</span>
        </div>
        <span id="result-count" class="text-xs text-gray-400 font-medium">
            {{ $menuProducts->count() }} produk
        </span>
    </div>

    {{-- Product grid --}}
    <div id="product-grid"
         class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">

        @foreach ($menuProducts as $p)
            <div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden cursor-pointer"
                 style="box-shadow: 0 2px 12px rgba(0,0,0,0.08);"
                 data-category="{{ $p->category->name }}"
                 data-price="{{ $p->price }}"
                 data-name="{{ strtolower($p->name) }}"
                 data-id="{{ $p->id }}">

                {{-- Image: uniform crop locked into card top --}}
                <div class="overflow-hidden rounded-t-2xl">
                    <img src="{{ asset($p->image) }}"
                         alt="{{ $p->name }}"
                         class="w-full h-48 object-cover rounded-t-2xl transition-transform duration-300 hover:scale-105"
                         onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                </div>

                {{-- Text area --}}
                <div class="px-4 pt-3 pb-4 flex flex-col flex-1">
                    <p class="font-bold text-sm leading-snug" style="color: var(--color-primary);">
                        {{ $p->name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">
                        {{ $p->description }}
                    </p>
                    <div class="flex items-center justify-between mt-3 gap-2">
                        <p class="text-sm font-black" style="color: var(--color-primary);">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </p>
                        <button type="button"
                                class="btn-pesan flex-none px-3 py-1 rounded-full text-xs font-bold transition-opacity hover:opacity-80"
                                style="background-color: var(--color-accent); color: var(--color-black);"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                data-price="{{ $p->price }}"
                                data-description="{{ $p->description }}"
                                data-image="{{ asset($p->image) }}">
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
        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau pilih kategori berbeda.</p>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════════ --}}
<footer style="background-color: var(--color-primary);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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

    /* ── State ──────────────────────────────────────────── */
    var activeCat  = 'Semua';
    var searchTerm = '';
    var sortMode   = 'default';

    /* Store original order --*/
    var $grid    = $('#product-grid');
    var $origCards = $grid.children('.product-card').clone(true);

    /* ── Helpers ──────────────────────────────────────────── */
    function applyFilters() {
        var visible = 0;

        $grid.children('.product-card').each(function () {
            var $card   = $(this);
            var cat     = $card.data('category');
            var name    = $card.data('name');

            var catMatch  = (activeCat === 'Semua') || (cat === activeCat);
            var nameMatch = (searchTerm === '') || name.includes(searchTerm);

            if (catMatch && nameMatch) {
                $card.removeClass('hidden');
                visible++;
            } else {
                $card.addClass('hidden');
            }
        });

        $('#result-count').text(visible + ' produk');
        $('#empty-state').toggleClass('hidden', visible > 0);
    }

    function applySort() {
        var $cards = $grid.children('.product-card').detach();

        if (sortMode === 'price-asc') {
            $cards.sort(function (a, b) {
                return parseInt($(a).data('price'), 10) - parseInt($(b).data('price'), 10);
            });
        } else if (sortMode === 'price-desc') {
            $cards.sort(function (a, b) {
                return parseInt($(b).data('price'), 10) - parseInt($(a).data('price'), 10);
            });
        } else {
            $cards = $origCards.clone(true);
        }

        $grid.append($cards);
        applyFilters();
    }

    /* ── Category tabs ────────────────────────────────────── */
    $(document).on('click', '.cat-tab', function () {
        activeCat = $(this).data('cat');

        $('.cat-tab').each(function () {
            $(this).removeClass('active')
                   .css({ 'background-color': 'var(--color-white)', 'color': 'var(--color-black)', 'border-color': 'var(--color-border)' });
        });
        $(this).addClass('active')
               .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'border-color': 'var(--color-primary)' });

        $('#active-cat-label').text(activeCat);
        applyFilters();
    });

    /* ── Navbar search ─────────────────────────────────────── */
    $('#navbar-search').on('input', function () {
        searchTerm = $.trim($(this).val()).toLowerCase();
        applyFilters();
    });

    /* ── Sort dropdown toggle ──────────────────────────────── */
    $('#btn-sort-toggle').on('click', function (e) {
        e.stopPropagation();
        $('#sort-dropdown').toggleClass('open');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#sort-dropdown, #btn-sort-toggle').length) {
            $('#sort-dropdown').removeClass('open');
        }
    });

    /* ── Sort apply ────────────────────────────────────────── */
    $('#btn-sort-apply').on('click', function () {
        sortMode = $('input[name="sort-option"]:checked').val() || 'default';
        $('#sort-dropdown').removeClass('open');
        applySort();
    });

    /* ── Sort reset ────────────────────────────────────────── */
    $('#btn-sort-reset').on('click', function () {
        $('input[name="sort-option"][value="default"]').prop('checked', true);
        sortMode = 'default';
        $('#sort-dropdown').removeClass('open');
        applySort();
    });

    /* ── Init ──────────────────────────────────────────────── */
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
