@extends('layouts.customer')

@section('title', 'Menu & Varian Rasa — Corndog-Ku')

@push('styles')
<style>
        body { background-color: #FFFDDB; }

        /* Category tab active state */
        .cat-tab.active {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }

        /* Sort/Filter dropdowns — toggled via Tailwind 'hidden' class */
        .sort-option-card:hover { background-color: #fafafa; }

        /* Hide scrollbar on category strip */
        .hide-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* Product card hover */
        .product-card {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .product-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.13);
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     CUSTOMIZE CORNDOG BANNER
══════════════════════════════════════════════════════════════ --}}
<section class="pt-6 pb-4 sm:pt-8 sm:pb-6" style="background-color: var(--color-light);">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-12 2xl:px-12">

        {{-- Wrapper: relative so the button can be layered above the image --}}
        <div class="relative w-full">

            {{-- Layer 1: Figma-exported composite banner image (natural intrinsic size) --}}
            <img src="{{ asset('assets/img/custom_corndog_banner_bg.png') }}"
                 alt="Custom Corndog – Buat corndog favoritmu sesuai seleramu!"
                 class="w-full h-auto block select-none"
                 draggable="false"
                 style="border-radius: 2rem; box-shadow:0 8px 40px rgba(0,0,0,0.13);">

            {{-- Layer 2: Native interactive CTA button --}}
            <a href="{{ route('customize') }}"
               id="btn-custom-cta"
               class="absolute bottom-[8%] left-[5%] md:bottom-[15%] md:left-[8%] z-10
                      inline-flex items-center bg-[#ba0d0d] text-white font-bold
                      rounded-full whitespace-nowrap shadow-md
                      hover:bg-red-800 transition-colors w-auto max-w-fit"
               style="font-size: clamp(7px, 1.7vw, 14px);
                      gap: clamp(2px, 0.5vw, 4px);
                      padding: clamp(3px, 0.85vw, 9px) clamp(7px, 2vw, 18px);">
                Yuk, Buat Corndog Kamu!
                <svg class="flex-shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"
                     style="width: clamp(7px, 1.7vw, 14px); height: clamp(7px, 1.7vw, 14px);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

        </div>{{-- /.banner --}}

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     CATEGORY FILTER + PRODUCT GRID
══════════════════════════════════════════════════════════════ --}}
@php
    $categories = ['Semua', 'Corndog Asin', 'Corndog Manis', 'Toppoki', 'Combo', 'Es Teler Kwentel', 'Bingsoo'];
@endphp

<section class="w-full">
    <div class="max-w-[1440px] 2xl:max-w-[1600px] w-full mx-auto px-4 sm:px-8 lg:px-12 2xl:px-12 py-8">

    {{-- Category tabs + Filter/Sort controls --}}
    <div class="flex justify-between items-center mb-8 gap-3">

        {{-- Scrollable category pills --}}
        <div class="flex flex-row overflow-x-auto hide-scrollbar gap-3 pb-1 flex-1 min-w-0">
            @foreach ($categories as $i => $cat)
                <button type="button"
                        class="cat-tab shrink-0 px-5 py-2 rounded-full text-sm font-semibold border whitespace-nowrap transition-all duration-150 hover:opacity-85 {{ $i === 0 ? 'active' : '' }}"
                        data-cat="{{ $cat }}"
                        style="{{ $i !== 0 ? 'border-color: var(--color-border); color: var(--color-black); background-color: var(--color-white);' : 'border-color: var(--color-primary);' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        {{-- Filter + Sort triggers (moved from navbar) --}}
        <div class="flex items-center gap-2 flex-none">

            {{-- Filter trigger + floating dropdown --}}
            <div class="relative inline-block">
                <button id="btn-filter-toggle" type="button"
                        class="inline-flex items-center justify-center gap-1.5 px-3 min-h-[40px] min-w-[40px] rounded-full text-xs font-semibold border hover:bg-gray-100 transition-colors"
                        style="border-color: var(--color-border); color: var(--color-black);"
                        title="Filter Produk">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-none" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="hidden sm:inline">Filter</span>
                </button>

                {{-- Filter dropdown card --}}
                <div id="dropdown-filter"
                     class="absolute top-full right-0 mt-3 w-80 bg-white rounded-2xl z-50 hidden"
                     style="box-shadow: 0 8px 40px rgba(0,0,0,0.14); border: 1px solid #f0f0f0;">
                    <div class="absolute -top-2 right-3 w-4 h-4 bg-white rotate-45"
                         style="border-left: 1px solid #f0f0f0; border-top: 1px solid #f0f0f0;"></div>

                    <div class="p-5" style="max-height: 80vh; overflow-y: auto;">
                        <div class="mb-4">
                            <p class="font-bold text-base" style="color: var(--color-black);">Filter Produk</p>
                            <p class="text-xs text-gray-400 mt-0.5">Sesuaikan produk yang ingin ditampilkan</p>
                        </div>

                        {{-- Batas Harga --}}
                        <div class="mb-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Batas Harga</p>
                            <div class="flex items-end gap-2 mb-3">
                                <div class="flex-1">
                                    <label class="text-[10px] font-semibold text-gray-400 block mb-1">Min</label>
                                    <input id="filter-price-min" type="number" placeholder="0"
                                           class="w-full px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-red-100"
                                           style="border-color: var(--color-border);">
                                </div>
                                <span class="pb-2 text-gray-300 text-base flex-none">—</span>
                                <div class="flex-1">
                                    <label class="text-[10px] font-semibold text-gray-400 block mb-1">Max</label>
                                    <input id="filter-price-max" type="number" placeholder="100000"
                                           class="w-full px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-red-100"
                                           style="border-color: var(--color-border);">
                                </div>
                            </div>
                            <div class="relative mb-3" style="height: 6px;">
                                <div class="absolute inset-0 rounded-full" style="background-color: #e5e7eb;"></div>
                                <div id="filter-range-fill" class="absolute inset-y-0 left-0 rounded-full"
                                     style="background-color: var(--color-primary); width: 100%;"></div>
                                <input id="filter-range" type="range" min="0" max="100000" value="100000" step="1000"
                                       class="absolute inset-0 w-full opacity-0 cursor-pointer"
                                       style="height: 6px;">
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" class="price-pill px-3 py-1 rounded-full text-xs font-semibold border transition-all"
                                        style="border-color: var(--color-border); color: #555;"
                                        data-min="0" data-max="10000">&lt; Rp 10.000</button>
                                <button type="button" class="price-pill px-3 py-1 rounded-full text-xs font-semibold border transition-all"
                                        style="border-color: var(--color-border); color: #555;"
                                        data-min="10000" data-max="25000">Rp 10.000 – 25.000</button>
                                <button type="button" class="price-pill px-3 py-1 rounded-full text-xs font-semibold border transition-all"
                                        style="border-color: var(--color-border); color: #555;"
                                        data-min="25000" data-max="">&gt; Rp 25.000</button>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Kategori</p>
                            <div class="space-y-2.5">
                                @foreach (['Corndog Asin', 'Corndog Manis', 'Toppoki', 'Combo', 'Es Teler Kwentel', 'Bingsoo'] as $filterCat)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="filter-cat" value="{{ $filterCat }}"
                                               class="filter-cat-check w-4 h-4 rounded cursor-pointer accent-red-700">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition-colors">
                                            {{ $filterCat }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <button id="btn-filter-reset" type="button"
                                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold border-2 transition-colors hover:bg-gray-50"
                                    style="border-color: var(--color-border); color: #555;">
                                Reset
                            </button>
                            <button id="btn-filter-apply" type="button"
                                    class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-opacity hover:opacity-85"
                                    style="background-color: var(--color-primary); color: white;">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sort trigger + floating dropdown --}}
            <div class="relative inline-block">
                <button id="btn-sort-toggle" type="button"
                        class="inline-flex items-center justify-center gap-1.5 px-3 min-h-[40px] min-w-[40px] rounded-full text-xs font-semibold border hover:bg-gray-100 transition-colors"
                        style="border-color: var(--color-border); color: var(--color-black);"
                        title="Urutkan Produk">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-none" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    <span class="hidden sm:inline">Urutkan</span>
                </button>

                {{-- Sort dropdown card --}}
                <div id="dropdown-sort"
                     class="absolute top-full right-0 mt-3 w-80 bg-white rounded-2xl z-50 hidden"
                     style="box-shadow: 0 8px 40px rgba(0,0,0,0.14); border: 1px solid #f0f0f0;">
                    <div class="absolute -top-2 right-3 w-4 h-4 bg-white rotate-45"
                         style="border-left: 1px solid #f0f0f0; border-top: 1px solid #f0f0f0;"></div>

                    <div class="p-5">
                        <div class="mb-4">
                            <p class="font-bold text-base" style="color: var(--color-black);">Urutkan Produk</p>
                            <p class="text-xs text-gray-400 mt-0.5">Pilih urutan tampilan produk</p>
                        </div>

                        <div class="space-y-2 mb-5">
                            <label class="sort-option-card flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                                   style="border-color: #e5e7eb;">
                                <input type="radio" name="sort-option" value="price-asc" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-none sort-radio-ring"
                                     style="border-color: #d1d5db;">
                                    <div class="w-2.5 h-2.5 rounded-full sort-radio-dot hidden"
                                         style="background-color: var(--color-primary);"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm" style="color: var(--color-black);">Harga: Rendah ke Tinggi</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Tampilkan dari harga terendah</p>
                                </div>
                            </label>

                            <label class="sort-option-card flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                                   style="border-color: #e5e7eb;">
                                <input type="radio" name="sort-option" value="price-desc" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-none sort-radio-ring"
                                     style="border-color: #d1d5db;">
                                    <div class="w-2.5 h-2.5 rounded-full sort-radio-dot hidden"
                                         style="background-color: var(--color-primary);"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm" style="color: var(--color-black);">Harga: Tinggi ke Rendah</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Tampilkan dari harga tertinggi</p>
                                </div>
                            </label>
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <button id="btn-sort-reset" type="button"
                                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold border-2 transition-colors hover:bg-gray-50"
                                    style="border-color: var(--color-border); color: #555;">
                                Reset
                            </button>
                            <button id="btn-sort-apply" type="button"
                                    class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-opacity hover:opacity-85"
                                    style="background-color: var(--color-primary); color: white;">
                                Terapkan Urutan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.filter-sort controls --}}
    </div>{{-- /.category row --}}

    {{-- Active category label + result count --}}
    <div class="flex items-center justify-between mt-5 mb-4">
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Menampilkan</span>
            <span id="active-cat-label" class="ml-1 text-xs font-bold" style="color: var(--color-primary);">Semua</span>
        </div>
        <span id="result-count" class="text-xs text-gray-400 font-medium">
            — produk
        </span>
    </div>

    {{-- Product grid — populated by AJAX --}}
    <div id="product-grid"
         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 sm:gap-6">
    </div>

    {{-- Empty state --}}
    <div id="empty-state" class="hidden py-20 text-center">
        <div class="text-5xl mb-4">🌽</div>
        <p class="font-bold text-lg" style="color: var(--color-black);">Produk tidak ditemukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau pilih kategori berbeda.</p>
    </div>

    {{-- Pagination nav — rendered by JS --}}
    <div id="pagination-nav"></div>

    </div>{{-- /.inner container --}}
</section>

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

            {{-- Name + price + wishlist icon --}}
            <div>
                <div class="flex items-start justify-between gap-4 mb-2">
                    <h3 id="modal-title"
                        class="text-xl font-bold leading-snug"
                        style="color: var(--color-black);"></h3>
                    
                    {{-- TOMBOL LOVE DI DALAM MODAL (Sesuai Screenshot) --}}
                    <button id="modal-btn-wishlist" 
                            class="flex-none w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center transition-colors hover:bg-gray-200" 
                            data-id="">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             class="w-5 h-5 text-gray-400" 
                             fill="none" 
                             viewBox="0 0 24 24" 
                             stroke="currentColor" 
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                    </button>
                </div>
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

@push('scripts')
<script>
$(function () {

    /* ── CSRF header for all AJAX requests ───────────────── */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    /* ── State ──────────────────────────────────────────── */
    var activeCat      = 'Semua';
    var searchTerm     = '';
    var sortMode       = 'default';
    var filterMinPrice = 0;
    var filterMaxPrice = null;
    var filterCats     = [];
    var currentPage    = 1;

    var $grid = $('#product-grid');

    

    /* ── Current modal product data ─────────────────────── */
    var currentProductId    = null;
    var currentProductPrice = 0;
    var currentProductImage = '';
    var currentProductDesc  = '';

    /* ── Helpers ──────────────────────────────────────────── */
    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function htmlEscape(s) {
        return (s || '').replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
    }

    function buildProductCard(p) {
        var fallback = '{{ asset('assets/img/CA_ORIGINAL.png') }}';
        var safeName = htmlEscape(p.name);
        var safeDesc = htmlEscape(p.description);
        var safeImageUrl = p.image_url ? p.image_url.replace(/"/g, '&quot;') : '';
        var inStock = p.is_available !== false && (p.stock === undefined || p.stock > 0);

        // Cek status kepemilikan wishlist produk dari database array
        // Menggunakan p.is_wishlisted (sesuaikan dengan properti object dari Backend/Controller Anda)
        var isWishlisted = p.is_wishlisted === true || p.is_wishlist === true;
        var loveSvgClass = isWishlisted ? 'text-red-500' : 'text-gray-400';
        var loveSvgFill = isWishlisted ? 'currentColor' : 'none';

        // Tombol wishlist asli diposisikan melayang (absolute) di atas gambar h-48 tanpa merusak tata letak
        var wishlistBtn = '<button type="button" class="btn-wishlist absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/80 backdrop-blur-sm flex items-center justify-center shadow-sm hover:bg-white transition-colors" data-id="' + p.id + '">' +
            '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ' + loveSvgClass + '" fill="' + loveSvgFill + '" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' +
            '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>' +
            '</svg>' +
            '</button>';

        // Mengembalikan tombol Pesan dengan style warna asli CSS Variable bawaan aplikasi Anda (--color-accent)
        var orderBtn = inStock
            ? '<button type="button"' +
            ' class="btn-pesan flex-none px-4 min-h-[36px] rounded-full text-xs font-bold transition-opacity hover:opacity-80"' +
            ' style="background-color:var(--color-accent);color:var(--color-black);"' +
            ' data-id="' + p.id + '"' +
            ' data-name="' + safeName + '"' +
            ' data-price="' + p.price + '"' +
            ' data-description="' + safeDesc + '"' +
            ' data-image="' + safeImageUrl + '">Pesan</button>'
            : '<button type="button" disabled' +
            ' class="flex-none px-4 min-h-[36px] rounded-full text-xs font-bold cursor-not-allowed"' +
            ' style="background-color:#d1d5db;color:#9ca3af;">Habis</button>';

        // Return HTML dengan layout h-48 asli agar gambar corndog memanjang utuh dan tidak terpotong
        return '<div class="product-card bg-white rounded-2xl flex flex-col overflow-hidden relative' + (inStock ? ' cursor-pointer' : '') + '"' +
            ' style="box-shadow:0 2px 12px rgba(0,0,0,0.08);"' +
            ' data-category="' + htmlEscape(p.category ? p.category.name : '') + '"' +
            ' data-price="' + p.price + '"' +
            ' data-name="' + safeName.toLowerCase() + '"' +
            ' data-id="' + p.id + '">' +
            wishlistBtn +
            '<div class="overflow-hidden rounded-t-2xl relative h-48">' + // Tetap h-48 asli
            '<img src="' + safeImageUrl + '"' +
            ' alt="' + safeName + '"' +
            ' class="w-full h-full object-cover rounded-t-2xl transition-transform duration-300' + (inStock ? ' hover:scale-105' : ' opacity-60') + '"' +
            ' onerror="this.src=\'' + fallback + '\'">' +
            (!inStock ? '<div class="absolute inset-0 flex items-center justify-center bg-black/30 rounded-t-2xl"><span class="text-white text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(0,0,0,0.55);">Habis</span></div>' : '') +
            '</div>' +
            '<div class="px-4 pt-3 pb-4 flex flex-col flex-1">' +
            '<p class="font-bold text-sm leading-snug" style="color:var(--color-primary);">' + htmlEscape(p.name) + '</p>' +
            '<p class="text-xs text-gray-500 mt-1 leading-relaxed flex-1 line-clamp-2">' + htmlEscape(p.description) + '</p>' +
            '<div class="flex items-center justify-between mt-3 gap-2">' +
            '<p class="text-sm font-black" style="color:var(--color-primary);">' + fmtRp(p.price) + '</p>' +
            orderBtn +
            '</div></div></div>';
    }
    

    function renderCards(data) {
        $grid.empty();
        $.each(data, function (_, p) {
            $grid.append(buildProductCard(p));
        });
    }

    function renderPagination(res) {
        var $nav = $('#pagination-nav').empty();
        if (res.last_page <= 1) { return; }

        var cur  = res.current_page;
        var last = res.last_page;
        var html = '<div class="flex items-center justify-center gap-1 mt-8 flex-wrap">';

        // Prev button
        var prevDisabled = (cur === 1) ? 'disabled' : '';
        var prevClass    = (cur === 1) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50';
        html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ' + prevClass + '"' +
                 ' data-page="' + (cur - 1) + '" ' + prevDisabled +
                 ' style="border-color:var(--color-border);" aria-label="Sebelumnya">&#8592;</button>';

        // Page number window
        var pages = [];
        for (var i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= cur - 2 && i <= cur + 2)) {
                pages.push(i);
            }
        }
        var prev = 0;
        $.each(pages, function (_, p) {
            if (prev && p - prev > 1) {
                html += '<span class="px-2 py-1.5 text-sm text-gray-400">&hellip;</span>';
            }
            var isActive = (p === cur);
            var btnStyle = isActive
                ? 'background-color:var(--color-primary);color:white;border-color:var(--color-primary);'
                : 'border-color:var(--color-border);';
            html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors' +
                    (isActive ? '' : ' hover:bg-gray-50') + '"' +
                    ' data-page="' + p + '" style="' + btnStyle + '">' + p + '</button>';
            prev = p;
        });

        // Next button
        var nextDisabled = (cur === last) ? 'disabled' : '';
        var nextClass    = (cur === last) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50';
        html += '<button class="pg-btn px-3 py-1.5 rounded-lg text-sm font-semibold border transition-colors ' + nextClass + '"' +
                 ' data-page="' + (cur + 1) + '" ' + nextDisabled +
                 ' style="border-color:var(--color-border);" aria-label="Selanjutnya">&#8594;</button>';

        html += '</div>';
        $nav.html(html);
    }

    function loadProducts(page) {
        currentPage = page || 1;
        var params = {
            page:     currentPage,
            category: activeCat,
            search:   searchTerm,
            sort:     sortMode,
            min:      filterMinPrice || 0,
        };
        if (filterMaxPrice) { params.max = filterMaxPrice; }
        if (filterCats.length) { params['cats[]'] = filterCats; }

        $grid.css({ opacity: '0.5', 'pointer-events': 'none' });

        $.get('/api/products', params)
            .done(function (res) {
                renderCards(res.data);
                renderPagination(res);
                $('#result-count').text(res.total + ' produk');
                $('#active-cat-label').text(activeCat);
                $('#empty-state').toggleClass('hidden', res.data.length > 0);
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat produk. Silakan coba lagi.' });
            })
            .always(function () {
                $grid.css({ opacity: '1', 'pointer-events': '' });
            });
    }

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

    /* ── Category tabs ────────────────────────────────────── */
    $(document).on('click', '.cat-tab', function () {
        activeCat = $(this).data('cat');

        $('.cat-tab').each(function () {
            $(this).removeClass('active')
                   .css({ 'background-color': 'var(--color-white)', 'color': 'var(--color-black)', 'border-color': 'var(--color-border)' });
        });
        $(this).addClass('active')
               .css({ 'background-color': 'var(--color-primary)', 'color': 'white', 'border-color': 'var(--color-primary)' });

        loadProducts(1);
    });

    /* ── Navbar search ─────────────────────────────────────── */
    var searchDebounce = null;
    $('#navbar-search').on('input', function () {
        searchTerm = $.trim($(this).val()).toLowerCase();
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            loadProducts(1);
        }, 300);
    });

    /* ── Dropdown helpers ──────────────────────────────────── */
    function closeAllDropdowns() {
        $('#dropdown-sort, #dropdown-filter').addClass('hidden');
    }

    function updateSortUI() {
        var selected = $('input[name="sort-option"]:checked').val();
        $('.sort-option-card').each(function () {
            var val    = $(this).find('input[type=radio]').val();
            var active = (val === selected);
            $(this).css({
                'border-color':     active ? 'var(--color-primary)' : '#e5e7eb',
                'background-color': active ? '#fff5f5' : ''
            });
            $(this).find('.sort-radio-ring').css('border-color', active ? 'var(--color-primary)' : '#d1d5db');
            $(this).find('.sort-radio-dot').toggleClass('hidden', !active);
        });
    }

    /* ── Sort dropdown toggle ──────────────────────────────── */
    $('#btn-sort-toggle').on('click', function (e) {
        e.stopPropagation();
        var wasHidden = $('#dropdown-sort').hasClass('hidden');
        closeAllDropdowns();
        if (wasHidden) $('#dropdown-sort').removeClass('hidden');
    });

    /* ── Filter dropdown toggle ────────────────────────────── */
    $('#btn-filter-toggle').on('click', function (e) {
        e.stopPropagation();
        var wasHidden = $('#dropdown-filter').hasClass('hidden');
        closeAllDropdowns();
        if (wasHidden) $('#dropdown-filter').removeClass('hidden');
    });

    /* ── Close both on outside click ──────────────────────── */
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#dropdown-sort, #btn-sort-toggle, #dropdown-filter, #btn-filter-toggle').length) {
            closeAllDropdowns();
        }
    });

    /* ── Sort option card click (visual feedback) ──────────── */
    $(document).on('click', '.sort-option-card', function () {
        $(this).find('input[type=radio]').prop('checked', true);
        updateSortUI();
    });

    /* ── Sort apply ────────────────────────────────────────── */
    $('#btn-sort-apply').on('click', function () {
        sortMode = $('input[name="sort-option"]:checked').val() || 'default';
        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Sort reset ────────────────────────────────────────── */
    $('#btn-sort-reset').on('click', function () {
        $('input[name="sort-option"]').prop('checked', false);
        sortMode = 'default';
        updateSortUI();
        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Filter apply ──────────────────────────────────────── */
    $('#btn-filter-apply').on('click', function () {
        var minVal     = $('#filter-price-min').val();
        var maxVal     = $('#filter-price-max').val();
        filterMinPrice = minVal ? parseInt(minVal, 10) : 0;
        filterMaxPrice = maxVal ? parseInt(maxVal, 10) : null;

        filterCats = [];
        $('.filter-cat-check:checked').each(function () {
            filterCats.push($(this).val());
        });

        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Filter reset ──────────────────────────────────────── */
    $('#btn-filter-reset').on('click', function () {
        filterMinPrice = 0;
        filterMaxPrice = null;
        filterCats     = [];
        $('#filter-price-min, #filter-price-max').val('');
        $('.filter-cat-check').prop('checked', false);
        $('.price-pill').css({ 'border-color': 'var(--color-border)', 'background-color': '', 'color': '#555' });
        closeAllDropdowns();
        loadProducts(1);
    });

    /* ── Price quick-select pills ──────────────────────────── */
    $(document).on('click', '.price-pill', function () {
        $('.price-pill').css({ 'border-color': 'var(--color-border)', 'background-color': '', 'color': '#555' });
        $(this).css({ 'border-color': 'var(--color-primary)', 'background-color': '#fff5f5', 'color': 'var(--color-primary)' });
        $('#filter-price-min').val($(this).data('min') || '');
        $('#filter-price-max').val($(this).data('max') || '');
    });

    /* ── Pagination click ─────────────────────────────────── */
    $(document).on('click', '.pg-btn:not([disabled])', function () {
        var page = parseInt($(this).data('page'), 10);
        if (!page) { return; }
        loadProducts(page);
        $('html, body').animate({ scrollTop: $grid.offset().top - 80 }, 200);
    });

    /* ── Init Awal Halaman ─────────────────────────────────── */
    loadProducts(1);

    /* ══════════════════════════════════════════════════════════
       PRODUCT DETAIL MODAL
       ══════════════════════════════════════════════════════════ */
    $(document).on('click', '.product-card, .btn-pesan', function (e) {
        // Intersepsi: Jika yang dklik adalah tombol wishlist bunder, hentikan logic modalbox
        if ($(e.target).closest('.btn-wishlist').length) return;

        var $targetData = $(this).hasClass('btn-pesan') ? $(this) : $(this).find('.btn-pesan');
        
        if (!$targetData.data('id')) return;

        currentProductId    = $targetData.data('id');
        currentProductPrice = parseInt($targetData.data('price'), 10) || 0;
        currentProductImage = $targetData.data('image');
        currentProductDesc  = $targetData.data('description');
        var currentProductName = $targetData.data('name');

        $('#modal-title').text(currentProductName);
        $('#modal-price').text(fmtRp(currentProductPrice) + ' / pcs');
        $('#modal-description').text(currentProductDesc ? currentProductDesc : 'Tidak ada deskripsi produk.');
        $('#modal-image').attr({ src: currentProductImage ? currentProductImage : '{{ asset("assets/img/CA_ORIGINAL.png") }}', alt: currentProductName });
        
        // Simpan id produk aktif saat ini ke DOM data-id wishlist modal
        $('#modal-btn-wishlist').data('id', currentProductId);

        // Ambil info status warna love dari card katalog pembungkus utama
        var $cardHeart = $('.product-card[data-id="' + currentProductId + '"]').find('.btn-wishlist svg');
        var isWishlisted = $cardHeart.hasClass('text-red-500');

        // Sinkronisasi status warna hati instant di dalam modal
        var $modalSvg = $('#modal-btn-wishlist').find('svg');
        if (isWishlisted) {
            $modalSvg.removeClass('text-gray-400').addClass('text-red-500').attr('fill', 'currentColor');
        } else {
            $modalSvg.removeClass('text-red-500').addClass('text-gray-400').attr('fill', 'none');
        }

        modalQty = 1;
        $('#modal-qty').text(modalQty);
        $('#product-modal').removeClass('hidden').addClass('flex');
        $('body').css('overflow', 'hidden');
    });

    /* ── Auto-open a product popup when arriving from a home promo card
          (e.g. /menu?product=12). Fetches that single product and opens the
          detail modal directly, independent of the current grid/pagination. ── */
    function openProductModalById(productId) {
        $.get('/api/products', { id: productId }).done(function (res) {
            var p = (res.data || [])[0];
            if (!p) return;

            // Don't open the order modal for an unavailable product (same
            // in-stock rule as the catalog cards). Otherwise the ?product= link
            // would bypass the disabled "Habis" state and let it be ordered.
            var inStock = p.is_available !== false && (p.stock === undefined || p.stock > 0);
            if (!inStock) {
                Swal.fire({
                    icon: 'info',
                    title: 'Stok Habis',
                    text: 'Produk ini sedang tidak tersedia.',
                    confirmButtonColor: 'var(--color-primary)',
                });
                return;
            }

            currentProductId    = p.id;
            currentProductPrice = parseInt(p.price, 10) || 0;
            currentProductImage = p.image_url || '';
            currentProductDesc  = p.description || '';
            var name = p.name || '';

            $('#modal-title').text(name);
            $('#modal-price').text(fmtRp(currentProductPrice) + ' / pcs');
            $('#modal-description').text(currentProductDesc ? currentProductDesc : 'Tidak ada deskripsi produk.');
            $('#modal-image').attr({ src: currentProductImage ? currentProductImage : '{{ asset("assets/img/CA_ORIGINAL.png") }}', alt: name });

            $('#modal-btn-wishlist').data('id', currentProductId);
            var $modalSvg = $('#modal-btn-wishlist').find('svg');
            if (p.is_wishlisted === true) {
                $modalSvg.removeClass('text-gray-400').addClass('text-red-500').attr('fill', 'currentColor');
            } else {
                $modalSvg.removeClass('text-red-500').addClass('text-gray-400').attr('fill', 'none');
            }

            modalQty = 1;
            $('#modal-qty').text(modalQty);
            $('#product-modal').removeClass('hidden').addClass('flex');
            $('body').css('overflow', 'hidden');
        });
    }

    (function () {
        var pid = new URLSearchParams(window.location.search).get('product');
        if (pid) openProductModalById(pid);
    })();



    /* ── Core Logic Operasional Wishlist Love (Global Event Listener) ── */
    $(document).on('click', '.btn-wishlist, #modal-btn-wishlist', function (e) {
        
        e.stopPropagation(); // Amankan agar modal luar tidak memicu bubbling click

        if (!isLoggedIn) {
            closeModal();
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Silakan login terlebih dahulu untuk menyukai produk.',
                confirmButtonColor: '#EF4444'
            });
            return;
        }

        let $clickedBtn = $(this);
        let productId = $clickedBtn.data('id');

        // Seleksi kedua tombol secara kolektif (tombol di katalog dan tombol di modal detail)
        let $bothButtons = $('.btn-wishlist[data-id="' + productId + '"], #modal-btn-wishlist[data-id="' + productId + '"]');
        let $bothSvgs = $bothButtons.find('svg');

        $bothButtons.prop('disabled', true);

        $.ajax({
            url: "{{ route('wishlist.toggle') }}",
            type: "POST",
            data: { product_id: productId },
            success: function(response) {
                if(response.success) {
                    // TAMBAHKAN BARIS INI: Untuk memperbarui angka badge di navbar secara realtime
                    $('#wishlist-badge').text(response.count);

                    if(response.status === 'added') {
                        $bothSvgs.removeClass('text-gray-400').addClass('text-red-500').attr('fill', 'currentColor');
                        showCartToast(response.message);
                    } else {
                        $bothSvgs.removeClass('text-red-500').addClass('text-gray-400').attr('fill', 'none');
                        showCartToast(response.message);
                    }
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal memproses wishlist.' });
            },
            complete: function() {
                $bothButtons.prop('disabled', false);
            }
        });
    });

    

    /* ── Add to cart (outline button) — stay on page ────── */
    $(document).on('click', '.btn-add-only', function () {
        if (!isLoggedIn) {
            closeModal();
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Kamu harus login atau daftar dulu untuk memasukkan menu ini ke keranjang.',
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
            closeModal();
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login!',
                text: 'Kamu harus login atau daftar dulu untuk memasukkan menu ini ke keranjang.',
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

    /* ── Toast Notification ────────────────────────────────── */
    style="z-index: 999999;"
    function showCartToast(msg, isError) {
        var bg = isError ? '#c00f0c' : '#A6171C';
        var $t = $('<div>').text(msg).css({
            position: 'fixed', bottom: '28px', left: '50%',
            transform: 'translateX(-50%)',
            background: bg, color: '#fff',
            padding: '11px 28px', borderRadius: '999px',
            fontWeight: '700', fontSize: '14px',
            zIndex: 999999, boxShadow: '0 4px 24px rgba(0,0,0,0.2)',
            whiteSpace: 'nowrap'
        }).appendTo('body');
        setTimeout(function () { $t.fadeOut(300, function () { $(this).remove(); }); }, 2500);
    }

    /* ── Modal Qty Plus & Minus ────────────────────────────── */
    $('#modal-qty-plus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        $('#modal-qty').text(q + 1);
    });

    $('#modal-qty-minus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        if (q > 1) $('#modal-qty').text(q - 1);
    });

    /* ── Close Modal Helpers ───────────────────────────────── */
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

    /* Mencegah default navbar search submit form */
    $('#navbar-search-form').on('submit', function (e) {
        e.preventDefault();
    });

});
</script>
@endpush
@endsection