@extends('layouts.customer')

@section('title', 'Keranjang Saya — Corndog-Ku')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HERO HEADER
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden" style="background-color: var(--color-light);">
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-6 flex items-center justify-between">
        <div>
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight" style="color: var(--color-black);">
                Keranjang Saya
            </h1>
            <p class="text-base sm:text-xl font-medium mt-2" style="color: var(--color-black);">
                Yuk, periksa pesananmu sebelum checkout
                <span style="color: var(--color-primary);">&#9829;</span>
            </p>
        </div>
        <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}"
             alt=""
             class="hidden lg:block w-44 h-44 object-contain pointer-events-none select-none drop-shadow-xl">
    </div>
</section>


{{-- ══════════════════════════════════════════════════════════════
     MAIN CONTENT  (two-column on desktop)
══════════════════════════════════════════════════════════════ --}}
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-8">

    {{-- ════════════════════
         LEFT — Cart Items
    ════════════════════ --}}
    <div class="w-full lg:w-2/3 flex flex-col gap-6">

        {{-- ── Cart card ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-[10px] p-5 sm:p-6"
             style="box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);">

            {{-- Card header --}}
            <div class="flex items-center justify-between mb-5">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all"
                           class="w-5 h-5 rounded cursor-pointer"
                           style="accent-color: var(--color-primary);"
                           checked>
                    <span class="text-sm font-bold" style="color: var(--color-black);">
                        Pilih Semua (<span id="total-items">3</span>)
                    </span>
                </label>
                <button type="button" id="btn-delete-all"
                        class="flex items-center gap-1.5 text-sm font-bold
                               hover:opacity-60 transition-opacity"
                        style="color: #898989;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Semua
                </button>
            </div>

            {{-- Item list — rendered from session cart --}}
            <div id="cart-items" class="flex flex-col gap-4">

                @foreach ($cartItems as $productId => $item)
                <div class="cart-item flex items-start gap-3 p-4 rounded-[15px] border"
                     style="border-color: #d2d2d2;"
                     data-id="{{ $productId }}"
                     data-price="{{ $item['price'] }}">

                    <div class="flex items-center pt-1 flex-none">
                        <input type="checkbox" class="item-check w-5 h-5 rounded cursor-pointer"
                               style="accent-color: var(--color-primary);" checked>
                    </div>

                    @if (!empty($item['is_custom']))
                        <div class="relative w-20 h-20 md:w-24 md:h-24 rounded-2xl overflow-hidden flex-none border"
                             style="background-color:#FDECD8; border-color:#d2d2d2; flex-shrink:0;">
                            <img src="{{ $item['varian_image'] ?? $item['image'] ?? '' }}"
                                 alt="base"
                                 class="absolute inset-0 w-full h-full object-contain">
                            @if (!empty($item['sauce_image'] ?? null))
                                <img src="{{ $item['sauce_image'] }}"
                                     alt="sauce"
                                     class="absolute inset-0 w-full h-full object-contain"
                                     style="z-index:10;">
                            @endif
                        </div>
                    @else
                        <img src="{{ $item['image'] }}"
                             alt="{{ $item['name'] }}"
                             class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-2xl flex-none border"
                             style="border-color:#d2d2d2;"
                             onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-base sm:text-lg leading-tight"
                                        style="color: var(--color-black);">{{ $item['name'] }}</h3>
                                    @if (!empty($item['is_custom']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                              style="background-color: #FFF3CD; color: #A6171C; border: 1px solid #FFBE54;">
                                            Custom
                                        </span>
                                    @endif
                                </div>

                                @if (!empty($item['is_custom']))
                                    <div class="mt-1 space-y-0.5">
                                        @if (!empty($item['isi']))
                                        <p class="text-xs font-medium" style="color: #9c9c9c;">
                                            <span class="font-semibold" style="color: var(--color-black);">Isi:</span>
                                            {{ $item['isi'] }}
                                        </p>
                                        @endif
                                        @if (!empty($item['varian']))
                                        <p class="text-xs font-medium" style="color: #9c9c9c;">
                                            <span class="font-semibold" style="color: var(--color-black);">Varian:</span>
                                            {{ $item['varian'] }}
                                        </p>
                                        @endif
                                        @if (!empty($item['sauces']))
                                        <p class="text-xs font-medium" style="color: #9c9c9c;">
                                            <span class="font-semibold" style="color: var(--color-black);">Saos:</span>
                                            {{ $item['sauces'] }}
                                        </p>
                                        @endif
                                    </div>
                                @elseif (!empty($item['description']))
                                    <p class="text-xs sm:text-sm mt-1 leading-snug"
                                       style="color: #9c9c9c;">{{ $item['description'] }}</p>
                                @endif

                                <p class="text-xs mt-1 font-medium" style="color: #9c9c9c;">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }} / pcs
                                </p>
                            </div>
                            <button type="button"
                                    class="btn-remove-item flex-none hover:opacity-60 transition-opacity"
                                    style="color: #9c9c9c;" aria-label="Hapus item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                             01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                             00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2 px-2 py-1.5 rounded-[10px]"
                                 style="background-color: rgba(255,203,99,0.24); border: 1px solid #ffcb63;">
                                <button type="button"
                                        class="btn-qty-minus w-7 h-7 rounded-full flex items-center justify-center
                                               font-bold text-lg leading-none hover:opacity-70 transition-opacity"
                                        style="background-color: #ffcb63; color: #525252;">&#8722;</button>
                                <span class="item-qty w-7 text-center font-bold text-sm"
                                      style="color: #3d3d3d;">{{ $item['qty'] }}</span>
                                <button type="button"
                                        class="btn-qty-plus w-7 h-7 rounded-full flex items-center justify-center
                                               font-bold text-lg leading-none hover:opacity-70 transition-opacity"
                                        style="background-color: var(--color-primary); color: white;">+</button>
                            </div>
                            <span class="item-total font-bold text-base sm:text-lg"
                                  style="color: var(--color-primary);">
                                Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- ── Empty state ── --}}
                <div id="cart-empty" class="hidden py-14 flex flex-col items-center gap-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" fill="none"
                         viewBox="0 0 24 24" stroke="#d2d2d2" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                                 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100
                                 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-base font-semibold" style="color: #9c9c9c;">
                        Keranjangmu masih kosong
                    </p>
                    <a href="{{ route('menu') }}"
                       class="inline-flex px-5 py-2 rounded-full text-sm font-bold text-white
                              hover:opacity-90 transition-opacity"
                       style="background-color: var(--color-primary);">
                        Pilih Menu
                    </a>
                </div>

            </div>{{-- /#cart-items --}}

        </div>{{-- /.cart card --}}


        {{-- ── Upsell banner ─────────────────────────────────────── --}}
        <div class="relative overflow-hidden rounded-[30px] p-6 sm:p-8
                    flex items-center justify-between gap-4"
             style="background-color: #fff2d9;">
            <div class="z-10">
                <h3 class="text-xl sm:text-2xl font-bold mb-1.5"
                    style="color: var(--color-black);">Mau tambah lagi?</h3>
                <p class="text-sm sm:text-base" style="color: #333;">
                    Yuk pilih Corndog favoritemu<br>
                    dan nikmati kelezatannya!
                </p>
            </div>
            <a href="{{ route('menu') }}"
               class="flex-none flex items-center gap-2 px-5 py-3 rounded-[15px]
                      text-sm font-bold text-white hover:opacity-90 transition-opacity z-10 whitespace-nowrap"
               style="background-color: var(--color-primary);">
                Lanjut Belanja
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>


        {{-- ── Trust badges ───────────────────────────────────────── --}}
        <div class="bg-white rounded-[10px] p-5 sm:p-6
                    grid grid-cols-2 sm:grid-cols-4 gap-6"
             style="box-shadow: 3px 4px 30px 0px rgba(0,0,0,0.17);">

            @foreach([
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => '100% Halal', 'sub' => 'Terjamin Kehalalannya'],
                ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'title' => 'Bahan Berkualitas', 'sub' => 'Dipilih dengan standar terbaik'],
                ['icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0', 'title' => 'Pengiriman Cepat', 'sub' => 'Sampai hangat di tanganmu'],
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Pembayaran Aman', 'sub' => 'Transaksi Aman & Terpercaya'],
            ] as $badge)
                <div class="flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full"
                         style="background-color: rgba(166,23,28,0.08);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                             viewBox="0 0 24 24" stroke="#A6171C" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="{{ $badge['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="font-bold text-sm" style="color: var(--color-primary);">{{ $badge['title'] }}</p>
                    <p class="text-xs leading-snug" style="color: var(--color-black);">{{ $badge['sub'] }}</p>
                </div>
            @endforeach

        </div>{{-- /.trust-badges --}}

    </div>{{-- /.left column --}}


    {{-- ════════════════════
         RIGHT — Order Summary
    ════════════════════ --}}
    <div class="w-full lg:w-1/3">
        <div class="bg-white p-6 rounded-[10px] sticky top-24"
             style="box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);">

            <h2 class="text-lg font-bold mb-5" style="color: var(--color-black);">
                Rincian Pesanan
            </h2>

            {{-- Summary rows --}}
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color: #848383;">Items</span>
                    <span id="summary-items" class="text-sm font-medium"
                          style="color: #848383;">3</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color: #848383;">Sub Total</span>
                    <span id="summary-subtotal" class="text-sm font-medium"
                          style="color: #848383;">Rp 111.000</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm" style="color: #848383;">Pajak (11%)</span>
                    <span id="summary-tax" class="text-sm font-medium"
                          style="color: #848383;">Rp 12.210</span>
                </div>
            </div>

            {{-- Divider --}}
            <div class="my-4" style="border-top: 1px solid #e0e0e0;"></div>

            {{-- Total --}}
            <div class="flex items-center justify-between mb-6">
                <span class="text-base font-bold" style="color: var(--color-black);">Total</span>
                <span id="summary-total" class="text-2xl font-bold"
                      style="color: var(--color-primary);">Rp 123.210</span>
            </div>

            {{-- CTA --}}
            <a href="{{ route('checkout') }}"
               class="block w-full text-center py-3.5 rounded-[15px] font-bold text-white
                      hover:opacity-90 transition-opacity text-base"
               style="background-color: var(--color-primary);">
                Checkout Sekarang
            </a>

        </div>
    </div>{{-- /.right column --}}

</div>{{-- /.main content --}}

@endsection


@push('scripts')
<script>
$(function () {

    /* ── CSRF header for all AJAX requests ───────────────── */
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var TAX_RATE = 0.11;

    function fmtRp(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function recalc() {
        var subtotal  = 0;
        var itemCount = 0;

        $('.cart-item').each(function () {
            var $item     = $(this);
            var price     = parseInt($item.data('price'), 10) || 0;
            var qty       = parseInt($item.find('.item-qty').text(), 10) || 1;
            var checked   = $item.find('.item-check').prop('checked');
            var lineTotal = price * qty;

            $item.find('.item-total').text(fmtRp(lineTotal));

            if (checked) {
                subtotal  += lineTotal;
                itemCount++;
            }
        });

        var tax   = subtotal * TAX_RATE;
        var total = subtotal + tax;

        $('#summary-items').text(itemCount);
        $('#summary-subtotal').text(fmtRp(subtotal));
        $('#summary-tax').text(fmtRp(tax));
        $('#summary-total').text(fmtRp(total));
        $('#total-items').text($('.cart-item').length);
    }

    function checkEmpty() {
        var isEmpty = $('.cart-item').length === 0;
        $('#cart-empty').toggleClass('hidden', !isEmpty);
    }

    /* ── Qty + ─────────────────────────────────────────── */
    $(document).on('click', '.btn-qty-plus', function () {
        var $item  = $(this).closest('.cart-item');
        var $qty   = $item.find('.item-qty');
        var newQty = parseInt($qty.text(), 10) + 1;
        $qty.text(newQty);
        recalc();
        $.post('{{ route("cart.update") }}', { product_id: $item.data('id'), qty: newQty });
    });

    /* ── Qty − ─────────────────────────────────────────── */
    $(document).on('click', '.btn-qty-minus', function () {
        var $item  = $(this).closest('.cart-item');
        var $qty   = $item.find('.item-qty');
        var q      = parseInt($qty.text(), 10);
        if (q > 1) {
            $qty.text(q - 1);
            recalc();
            $.post('{{ route("cart.update") }}', { product_id: $item.data('id'), qty: q - 1 });
        }
    });

    /* ── Remove single item — AJAX then DOM ─────────────── */
    $(document).on('click', '.btn-remove-item', function () {
        var $item      = $(this).closest('.cart-item');
        var productId  = $item.data('id');

        $.ajax({
            url:    '{{ route("cart.remove") }}',
            method: 'POST',
            data:   { product_id: productId },
            success: function (response) {
                if (response.success) {
                    $item.fadeOut(200, function () {
                        $(this).remove();
                        checkEmpty();
                        recalc();
                    });
                }
            }
        });
    });

    /* ── Delete all — AJAX then DOM ─────────────────────── */
    $('#btn-delete-all').on('click', function () {
        $.ajax({
            url:    '{{ route("cart.clear") }}',
            method: 'POST',
            success: function (response) {
                if (response.success) {
                    $('.cart-item').fadeOut(200, function () {
                        $(this).remove();
                        checkEmpty();
                        recalc();
                    });
                }
            }
        });
    });

    /* ── Select all ─────────────────────────────────────── */
    $('#select-all').on('change', function () {
        $('.item-check').prop('checked', $(this).prop('checked'));
        recalc();
    });

    /* ── Individual checkbox ────────────────────────────── */
    $(document).on('change', '.item-check', function () {
        var total   = $('.item-check').length;
        var checked = $('.item-check:checked').length;
        $('#select-all').prop('checked', total === checked);
        recalc();
    });

    /* ── Init ───────────────────────────────────────────── */
    recalc();
    checkEmpty();

});
</script>
@endpush
