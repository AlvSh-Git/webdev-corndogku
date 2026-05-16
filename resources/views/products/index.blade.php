@extends('layouts.app')

@section('title', 'Product Management')

@section('content')

@php
    $products = [
        ['id' => 1, 'name' => 'Corndog Original',        'category' => 'Corndog Asin',  'price' => 'Rp 16.000', 'stock' => 42, 'image' => 'assets/img/CA_ORIGINAL.png',                  'desc' => 'Perpaduan sosis dan mozzarella lumer dengan balutan tepung crispy khas Korean corndog.'],
        ['id' => 2, 'name' => 'Corndog Full Mozza',       'category' => 'Corndog Asin',  'price' => 'Rp 17.000', 'stock' => 35, 'image' => 'assets/img/CA_FULL_MOZZA.png',                  'desc' => 'Mozzarella full lumer dengan tekstur crispy di luar dan cheesy di dalam.'],
        ['id' => 3, 'name' => 'Corndog Squid Mozza',      'category' => 'Corndog Asin',  'price' => 'Rp 16.000', 'stock' => 0,  'image' => 'assets/img/CA_SQUID_ORI.png',                   'desc' => 'Corndog sosis mozzarella dengan model sosis seperti squid crispy khas yang gurih dan unik.'],
        ['id' => 4, 'name' => 'Corndog Mozza Potato',     'category' => 'Corndog Asin',  'price' => 'Rp 20.000', 'stock' => 20, 'image' => 'assets/img/CA_MOZZA_POTATO.png',               'desc' => 'Corndog mozzarella dengan balutan potongan kentang crispy yang gurih dan crunchy.'],
        ['id' => 5, 'name' => 'Corndog Ramen Mix',        'category' => 'Corndog Asin',  'price' => 'Rp 18.000', 'stock' => 18, 'image' => 'assets/img/CA_RAMEN_MIX.png',                  'desc' => 'Kombinasi sosis dan mozzarella dengan topping ramen crispy yang unik dan gurih.'],
        ['id' => 6, 'name' => 'Corndog Choco Kacang',     'category' => 'Corndog Manis', 'price' => 'Rp 20.000', 'stock' => 25, 'image' => 'assets/img/CM_CHOCO_CHRUNCH_CHEESE.png',       'desc' => 'Corndog mozzarella dengan glaze coklat premium dan taburan keju parut melimpah.'],
        ['id' => 7, 'name' => 'Corndog Matcha Kacang',    'category' => 'Corndog Manis', 'price' => 'Rp 20.000', 'stock' => 22, 'image' => 'assets/img/CM_GREENTEA_CHRUNCHY_BISKUIT.png',  'desc' => 'Corndog mozzarella dengan glaze greentea dan taburan biskuit matcha crunchy yang manis gurih.'],
        ['id' => 8, 'name' => 'Corndog Tiramisu Biskuit', 'category' => 'Corndog Manis', 'price' => 'Rp 20.000', 'stock' => 15, 'image' => 'assets/img/CM_TIRAMISU_BISKUIT.png',           'desc' => 'Corndog mozzarella dengan glaze tiramisu creamy dan topping biskuit crunchy ala dessert.'],
    ];
@endphp

{{-- ── Page Header ─────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="text-4xl font-black tracking-tight" style="color: var(--color-black);">PRODUCT</h1>

    {{-- Search + Filter --}}
    <div class="flex items-center gap-3 flex-1 max-w-xl">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input type="search"
                   id="search-products"
                   placeholder="Search products..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm border border-gray-200
                          bg-white focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <select id="filter-category"
                class="px-4 py-2.5 rounded-xl text-sm border border-gray-200 bg-white
                       focus:outline-none focus:ring-2 focus:ring-red-200 min-w-[160px]">
            <option value="">Select one Category</option>
            <option value="Corndog Asin">Corndog Asin</option>
            <option value="Corndog Manis">Corndog Manis</option>
            <option value="Mie Bakar">Mie Bakar</option>
            <option value="Loaded Fries">Loaded Fries</option>
        </select>
    </div>
</div>

{{-- ── Product Grid ─────────────────────────────────────── --}}
<div id="product-grid"
     class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">

    @foreach ($products as $product)
        <div class="product-card bg-white rounded-2xl relative overflow-visible"
             data-name="{{ strtolower($product['name']) }}"
             data-category="{{ $product['category'] }}"
             style="border: 1px solid var(--color-border); box-shadow: var(--shadow-card);">

            {{-- Edit / Delete icons —top-right --}}
            <div class="absolute -top-2 -right-2 flex items-center gap-1.5 z-10">
                <button type="button"
                        class="btn-edit w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm
                               border border-gray-100 hover:bg-gray-50 transition-colors"
                        title="Edit product">
                    {{-- Pencil --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="color: var(--color-primary);">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <button type="button"
                        class="btn-delete w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm
                               border border-gray-100 hover:bg-red-50 transition-colors"
                        title="Delete product">
                    {{-- Trash --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="color: var(--color-primary);">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                </button>
            </div>

            {{-- Circle image on peach --}}
            <div class="flex items-center justify-center pt-7 pb-2 px-4">
                <div class="w-28 h-28 rounded-full overflow-hidden flex items-center justify-center"
                     style="background-color: #FDECD8;">
                    <img src="{{ asset($product['image']) }}"
                         alt="{{ $product['name'] }}"
                         class="w-24 h-24 object-contain">
                </div>
            </div>

            {{-- Content --}}
            <div class="px-4 pb-4">
                <h3 class="font-bold text-sm leading-snug" style="color: var(--color-primary);">
                    {{ $product['name'] }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed"
                   style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $product['desc'] }}
                </p>
                <div class="flex items-center justify-between mt-3">
                    @if ($product['stock'] > 0)
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                              style="background-color: var(--color-status-active-bg);
                                     color: var(--color-status-active-text);">
                            Stok: {{ $product['stock'] }}
                        </span>
                    @else
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                              style="background-color: var(--color-status-inactive-bg);
                                     color: var(--color-status-inactive-text);">
                            Habis
                        </span>
                    @endif
                    <span class="font-bold text-sm" style="color: var(--color-primary);">
                        {{ $product['price'] }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Menu card --}}
    <button id="btn-add-product"
            type="button"
            class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed
                   min-h-[220px] transition-opacity hover:opacity-70 bg-white"
            style="border-color: var(--color-border);">
        <span class="flex items-center justify-center w-12 h-12 rounded-full"
              style="background-color: rgba(166,23,28,0.10);">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                 style="color: var(--color-primary);">
                <path d="M12 4v16M4 12h16"/>
            </svg>
        </span>
        <span class="text-sm font-bold" style="color: var(--color-black);">Add Menu</span>
    </button>

    {{-- No results --}}
    <p id="no-results" class="hidden col-span-full text-center text-sm text-gray-400 py-10">
        No products match your search.
    </p>
</div>


{{-- ══════════════════════════════════════════════════════════
     ADD PRODUCT MODAL
══════════════════════════════════════════════════════════ --}}
<div id="modal-add"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background-color: rgba(0,0,0,0.45);">
    <div class="relative w-full max-w-lg rounded-[24px] p-8 overflow-y-auto"
         style="background-color: var(--color-white); max-height: 90vh;">

        <button id="modal-close-btn"
                type="button"
                class="absolute top-4 right-4 w-9 h-9 flex items-center justify-center
                       rounded-full hover:opacity-70"
                style="background-color: #F0F0F0;">
            <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h2 class="text-2xl font-bold text-center mb-6" style="color: var(--color-black);">
            Add New Product
        </h2>

        <form class="space-y-4" id="form-add-product" onsubmit="return false;">
            <div>
                <label class="block text-sm mb-1 font-medium" style="color: #292929;">Product Name</label>
                <input type="text" placeholder="Enter product name..."
                       class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none border border-gray-200
                              focus:ring-2 focus:ring-red-200">
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium" style="color: #292929;">Category</label>
                <select class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none border border-gray-200
                               focus:ring-2 focus:ring-red-200">
                    <option value="">Select category...</option>
                    @foreach (['Corndog Asin', 'Corndog Manis', 'Mie Bakar', 'Loaded Fries', 'Es Teler Kwentel', 'Bingsoo', 'Camilan'] as $cat)
                        <option>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1 font-medium" style="color: #292929;">Harga Modal</label>
                    <input type="text" placeholder="Rp 0"
                           class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none border border-gray-200
                                  focus:ring-2 focus:ring-red-200">
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium" style="color: #292929;">Harga Jual</label>
                    <input type="text" placeholder="Rp 0"
                           class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none border border-gray-200
                                  focus:ring-2 focus:ring-red-200">
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium" style="color: #292929;">Stock</label>
                <input type="number" placeholder="0" min="0"
                       class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none border border-gray-200
                              focus:ring-2 focus:ring-red-200">
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium" style="color: #292929;">Description</label>
                <textarea rows="3" placeholder="Enter a description..."
                          class="w-full px-4 py-2.5 rounded-xl text-sm resize-none focus:outline-none
                                 border border-gray-200 focus:ring-2 focus:ring-red-200"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" id="modal-cancel-btn"
                        class="flex-1 py-2.5 rounded-full text-sm font-semibold hover:opacity-80"
                        style="border: 1.5px solid var(--color-black); color: var(--color-black);">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-full text-sm font-semibold hover:opacity-80"
                        style="background-color: var(--color-primary); color: var(--color-white);">
                    Add
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    /* ── Modal helpers ─────────────────────────────────────── */
    function openModal() {
        $('#modal-add').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }
    function closeModal() {
        $('#modal-add').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $('#btn-add-product').on('click', openModal);
    $('#modal-close-btn, #modal-cancel-btn').on('click', closeModal);

    /* Close on backdrop click */
    $('#modal-add').on('click', function (e) {
        if ($(e.target).is('#modal-add')) closeModal();
    });

    /* Esc key closes modal */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    /* ── Search + Category filter ──────────────────────────── */
    function applyFilter() {
        var q   = $('#search-products').val().toLowerCase().trim();
        var cat = $('#filter-category').val();
        var vis = 0;

        $('.product-card').each(function () {
            var name    = $(this).data('name') || '';
            var cardCat = $(this).data('category') || '';
            var show    = (!q || name.includes(q)) && (!cat || cardCat === cat);
            $(this).toggle(show);
            if (show) vis++;
        });

        $('#no-results').toggle(vis === 0);
    }

    $('#search-products').on('input', applyFilter);
    $('#filter-category').on('change', applyFilter);

    /* ── Edit / Delete button stubs ───────────────────────── */
    $(document).on('click', '.btn-edit', function () {
        var name = $(this).closest('.product-card').find('h3').text().trim();
        alert('Edit: ' + name + '\n(Backend not wired yet)');
    });

    $(document).on('click', '.btn-delete', function () {
        var card = $(this).closest('.product-card');
        var name = card.find('h3').text().trim();
        if (confirm('Delete "' + name + '"?')) {
            card.fadeOut(200, function () { $(this).remove(); });
        }
    });

});
</script>
@endpush
