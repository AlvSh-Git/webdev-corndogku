@extends('layouts.customer')

@section('title', 'Wishlist Saya - Corndog-Ku')

@push('styles')
<style>
    /* Product card hover — mirrors the menu catalog cards */
    .product-card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .product-card:hover {
        box-shadow: 0 12px 32px rgba(0,0,0,0.13);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-16 py-12">
    <h1 class="text-3xl font-bold mb-8">Wishlist Saya ❤️</h1>

    @if($wishlistItems->isEmpty())
        <div id="wishlist-empty" class="text-center py-16 bg-white rounded-2xl border" style="border-color: var(--color-border);">
            <p class="text-gray-500 mb-4">Kamu belum menambahkan produk apa pun ke wishlist.</p>
            <a href="{{ route('menu') }}" class="px-6 py-2.5 rounded-full text-white font-semibold transition-opacity hover:opacity-90" style="background-color: var(--color-primary);">Lihat Menu</a>
        </div>
    @else
        <div id="wishlist-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $product)
                @php
                    $imgUrl = $product->image ? asset($product->image) : asset('assets/img/CA_ORIGINAL.png');
                @endphp
                <div class="product-card bg-white rounded-2xl p-4 border relative flex flex-col justify-between cursor-pointer"
                     style="border-color: var(--color-border); box-shadow: 0 4px 12px rgba(0,0,0,0.03);"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-price="{{ $product->price }}"
                     data-image="{{ $imgUrl }}"
                     data-description="{{ $product->description }}">

                    {{-- Tombol love melayang di pojok kanan atas gambar (default: aktif/merah) --}}
                    <button type="button"
                            class="btn-wishlist absolute top-6 right-6 z-10 w-9 h-9 rounded-full bg-white/80 backdrop-blur-sm flex items-center justify-center shadow-sm hover:bg-white transition-colors"
                            data-id="{{ $product->id }}"
                            aria-label="Hapus dari wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                    </button>

                    <div>
                        <div class="overflow-hidden rounded-xl mb-3 h-44 bg-gray-50 flex items-center justify-center">
                            <img src="{{ $imgUrl }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-bold text-lg mb-1" style="color: var(--color-black);">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($product->description, 60) }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="font-extrabold text-red-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <button type="button"
                                class="btn-wishlist-order inline-flex items-center text-xs font-bold px-4 min-h-[40px] rounded-full text-white transition-opacity hover:opacity-90 disabled:opacity-60"
                                style="background-color: var(--color-primary);"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ $imgUrl }}"
                                data-description="{{ $product->description }}">
                            Pesan
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty state shown when the last item gets removed without a reload --}}
        <div id="wishlist-empty" class="hidden text-center py-16 bg-white rounded-2xl border" style="border-color: var(--color-border);">
            <p class="text-gray-500 mb-4">Kamu belum menambahkan produk apa pun ke wishlist.</p>
            <a href="{{ route('menu') }}" class="px-6 py-2.5 rounded-full text-white font-semibold transition-opacity hover:opacity-90" style="background-color: var(--color-primary);">Lihat Menu</a>
        </div>
    @endif
</div>

{{-- PRODUCT DETAIL MODAL (mirrors the menu view popup) --}}
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

                    {{-- Tombol love di dalam modal --}}
                    <button id="modal-btn-wishlist"
                            class="flex-none w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center transition-colors hover:bg-gray-200"
                            data-id="">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 text-red-500"
                             fill="currentColor"
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
@endsection

@push('scripts')
<script>
$(function () {

    /*  CSRF header for all AJAX requests  */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /*  Current modal product data  */
    var currentProductId    = null;
    var currentProductPrice = 0;
    var currentProductImage = '';
    var currentProductDesc  = '';

    /*  Helpers  */
    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function closeModal() {
        $('#product-modal').addClass('hidden').removeClass('flex');
        $('body').css('overflow', '');
    }

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

    /* 
       OPEN PRODUCT DETAIL MODAL (clicking a wishlist card)
        */
    $(document).on('click', '.product-card', function (e) {
        // Jangan buka modal kalau yang diklik adalah tombol love atau tombol Pesan
        if ($(e.target).closest('.btn-wishlist, .btn-wishlist-order').length) return;

        var $card = $(this);

        currentProductId    = $card.data('id');
        currentProductPrice = parseInt($card.data('price'), 10) || 0;
        currentProductImage = $card.data('image');
        currentProductDesc  = $card.data('description');
        var currentProductName = $card.data('name');

        $('#modal-title').text(currentProductName);
        $('#modal-price').text(fmtRp(currentProductPrice) + ' / pcs');
        $('#modal-description').text(currentProductDesc ? currentProductDesc : 'Tidak ada deskripsi produk.');
        $('#modal-image').attr({ src: currentProductImage ? currentProductImage : '{{ asset("assets/img/CA_ORIGINAL.png") }}', alt: currentProductName });

        // Semua produk di halaman ini sudah ada di wishlist → hati merah penuh
        $('#modal-btn-wishlist').data('id', currentProductId);
        $('#modal-btn-wishlist').find('svg').removeClass('text-gray-400').addClass('text-red-500').attr('fill', 'currentColor');

        $('#modal-qty').text(1);
        $('#product-modal').removeClass('hidden').addClass('flex');
        $('body').css('overflow', 'hidden');
    });

    /*  Close modal helpers  */
    $('#modal-close').on('click', closeModal);
    $('#product-modal').on('click', function (e) {
        if (!$(e.target).closest('#product-modal-box').length) closeModal();
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    /*  Modal Qty Plus & Minus  */
    $('#modal-qty-plus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        $('#modal-qty').text(q + 1);
    });
    $('#modal-qty-minus').on('click', function () {
        var q = parseInt($('#modal-qty').text(), 10);
        if (q > 1) $('#modal-qty').text(q - 1);
    });

    /* 
       WISHLIST LOVE TOGGLE (card heart + modal heart)
        */
    $(document).on('click', '.btn-wishlist, #modal-btn-wishlist', function (e) {
        e.stopPropagation();

        var $btn      = $(this);
        var productId = $btn.data('id');

        $btn.prop('disabled', true);

        $.ajax({
            url:  "{{ route('wishlist.toggle') }}",
            type: "POST",
            data: { product_id: productId },
            success: function (response) {
                if (!response.success) return;

                $('#wishlist-badge').text(response.count);

                if (response.status === 'removed') {
                    // Item dihapus dari wishlist → buang kartunya dari halaman
                    closeModal();
                    showCartToast(response.message);

                    var $card = $('.product-card[data-id="' + productId + '"]');
                    $card.fadeOut(250, function () {
                        $(this).remove();
                        if ($('#wishlist-grid .product-card').length === 0) {
                            $('#wishlist-grid').remove();
                            $('#wishlist-empty').removeClass('hidden');
                        }
                    });
                } else {
                    // Ditambahkan kembali (mis. ditoggle dua kali) → hati merah
                    $('.btn-wishlist[data-id="' + productId + '"], #modal-btn-wishlist')
                        .find('svg').removeClass('text-gray-400').addClass('text-red-500').attr('fill', 'currentColor');
                    showCartToast(response.message);
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal memproses wishlist.' });
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    /* 
       ADD TO CART — outline button (stay on page)
        */
    $(document).on('click', '.btn-add-only', function () {
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

    /* 
       ORDER NOW — solid button (add then go to cart)
        */
    $(document).on('click', '.btn-order-now', function () {
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

    /* 
       WISHLIST "Pesan" button — add to cart then go to cart
        */
    $(document).on('click', '.btn-wishlist-order', function () {
        var $btn = $(this);

        $btn.prop('disabled', true).text('Memproses...');

        $.ajax({
            url:    '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id:  $btn.data('id'),
                name:        $btn.data('name'),
                price:       $btn.data('price'),
                qty:         1,
                image:       $btn.data('image'),
                description: $btn.data('description'),
            },
            success: function (response) {
                if (response.success) {
                    window.location.href = '{{ route("cart") }}';
                } else {
                    $btn.prop('disabled', false).text('Pesan');
                }
            },
            error: function () {
                $btn.prop('disabled', false).text('Pesan');
            }
        });
    });

});
</script>
@endpush
