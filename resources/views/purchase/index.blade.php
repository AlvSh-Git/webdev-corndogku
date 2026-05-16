@extends('layouts.app')

@section('title', 'Kasir — Purchase')

@section('content')

<h1 class="text-3xl font-black tracking-tight mb-6" style="color: var(--color-black);">KASIR</h1>

<div class="flex flex-col lg:flex-row gap-6">

    {{-- ════════════════════════════════
         LEFT — Product Catalog
    ════════════════════════════════ --}}
    <div class="w-full lg:w-8/12">

        {{-- Search bar --}}
        <div class="relative mb-4">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
            </svg>
            <input
                type="search"
                id="search-input"
                placeholder="Cari produk…"
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-red-200"
                style="border-color: var(--color-border); background: var(--color-white);">
        </div>

        {{-- Category filter tabs --}}
        @php
            $posProducts = [
                ['id' => 1, 'name' => 'Corndog Original',        'category' => 'Corndog Asin',  'price' => 16000, 'image' => 'assets/img/CA_ORIGINAL.png'],
                ['id' => 2, 'name' => 'Corndog Full Mozza',       'category' => 'Corndog Asin',  'price' => 17000, 'image' => 'assets/img/CA_FULL_MOZZA.png'],
                ['id' => 3, 'name' => 'Corndog Squid Mozza',      'category' => 'Corndog Asin',  'price' => 16000, 'image' => 'assets/img/CA_SQUID_ORI.png'],
                ['id' => 4, 'name' => 'Corndog Mozza Potato',     'category' => 'Corndog Asin',  'price' => 20000, 'image' => 'assets/img/CA_MOZZA_POTATO.png'],
                ['id' => 5, 'name' => 'Corndog Ramen Mix',        'category' => 'Corndog Asin',  'price' => 18000, 'image' => 'assets/img/CA_RAMEN_MIX.png'],
                ['id' => 6, 'name' => 'Corndog Choco Crunch',     'category' => 'Corndog Manis', 'price' => 20000, 'image' => 'assets/img/CM_CHOCO_CHRUNCH_CHEESE.png'],
                ['id' => 7, 'name' => 'Corndog Matcha Biskuit',   'category' => 'Corndog Manis', 'price' => 20000, 'image' => 'assets/img/CM_GREENTEA_CHRUNCHY_BISKUIT.png'],
                ['id' => 8, 'name' => 'Corndog Tiramisu Biskuit', 'category' => 'Corndog Manis', 'price' => 20000, 'image' => 'assets/img/CM_TIRAMISU_BISKUIT.png'],
            ];
        @endphp

        <div class="flex gap-2 mb-4 flex-wrap">
            <button class="filter-tab active px-4 py-1.5 rounded-full text-xs font-bold transition-colors"
                    data-cat="all"
                    style="background-color: var(--color-primary); color: var(--color-white);">
                Semua
            </button>
            <button class="filter-tab px-4 py-1.5 rounded-full text-xs font-bold border transition-colors"
                    data-cat="Corndog Asin"
                    style="border-color: var(--color-border); color: #666;">
                Corndog Asin
            </button>
            <button class="filter-tab px-4 py-1.5 rounded-full text-xs font-bold border transition-colors"
                    data-cat="Corndog Manis"
                    style="border-color: var(--color-border); color: #666;">
                Corndog Manis
            </button>
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4" id="product-grid">
            @foreach ($posProducts as $p)
                <div class="product-card bg-white rounded-2xl p-3 flex flex-col items-center text-center
                            cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-150"
                     style="box-shadow: var(--shadow-card, 0 2px 8px rgba(0,0,0,0.08));"
                     data-id="{{ $p['id'] }}"
                     data-name="{{ $p['name'] }}"
                     data-price="{{ $p['price'] }}"
                     data-category="{{ $p['category'] }}">

                    {{-- Circle image on peach bg --}}
                    <div class="w-24 h-24 rounded-full flex items-center justify-center mb-3 flex-none"
                         style="background-color: #FDECD8;">
                        <img src="{{ asset($p['image']) }}"
                             alt="{{ $p['name'] }}"
                             class="w-20 h-20 object-contain">
                    </div>

                    <p class="font-bold text-sm leading-snug mb-0.5 line-clamp-2"
                       style="color: var(--color-primary);">{{ $p['name'] }}</p>
                    <p class="text-[11px] text-gray-400 mb-2">{{ $p['category'] }}</p>
                    <p class="font-black text-sm" style="color: var(--color-primary);">
                        Rp {{ number_format($p['price'], 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>

        <p id="no-results" class="hidden text-center text-sm text-gray-400 mt-10 py-6">
            Produk tidak ditemukan.
        </p>
    </div>

    {{-- ════════════════════════════════
         RIGHT — Order Panel
    ════════════════════════════════ --}}
    <div class="w-full lg:w-4/12">
        <div class="bg-white rounded-2xl lg:sticky lg:top-4 overflow-hidden"
             style="box-shadow: var(--shadow-card, 0 2px 8px rgba(0,0,0,0.08));">

            {{-- Panel header --}}
            <div class="px-5 pt-5 pb-3 border-b" style="border-color: var(--color-border);">
                <h2 class="font-black text-lg tracking-tight" style="color: var(--color-black);">
                    ORDER MENU
                </h2>
            </div>

            <div class="px-5 py-4 space-y-4">

                {{-- Customer Information --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Info Pelanggan
                    </p>
                    <input type="text" id="customer-name" placeholder="Nama pelanggan"
                           class="w-full mb-2 px-3 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-red-200"
                           style="border-color: var(--color-border);">
                    <input type="tel" id="customer-phone" placeholder="Nomor telepon"
                           class="w-full mb-2 px-3 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-red-200"
                           style="border-color: var(--color-border);">
                    <button type="button" id="btn-save-customer"
                            class="w-full py-2 rounded-xl text-sm font-bold tracking-wide transition-opacity hover:opacity-80"
                            style="background-color: var(--color-accent); color: var(--color-black);">
                        Simpan
                    </button>
                </div>

                {{-- Divider --}}
                <div class="border-t" style="border-color: var(--color-border);"></div>

                {{-- Cart item list --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Item Pesanan
                    </p>
                    <div id="cart-list" class="space-y-3 max-h-60 overflow-y-auto">
                        {{-- items injected by jQuery --}}
                    </div>
                    <p id="cart-empty" class="text-center text-xs text-gray-400 py-4">
                        Ketuk produk untuk menambahkan ke pesanan.
                    </p>
                </div>

                {{-- Divider --}}
                <div class="border-t" style="border-color: var(--color-border);"></div>

                {{-- Order summary --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal</span>
                        <span id="summary-subtotal" class="font-semibold text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Pajak (11%)</span>
                        <span id="summary-tax" class="font-semibold text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-black text-base pt-2 border-t"
                         style="border-color: var(--color-border);">
                        <span style="color: var(--color-black);">Total</span>
                        <span id="summary-total" style="color: var(--color-primary);">Rp 0</span>
                    </div>
                </div>

                {{-- Order button --}}
                <button type="button" id="btn-process-order"
                        class="w-full py-3.5 rounded-xl font-black text-sm tracking-wide
                               transition-opacity hover:opacity-85 active:scale-95"
                        style="background: var(--color-primary); color: var(--color-white);">
                    <span id="btn-order-count">0 item</span>
                    &nbsp;·&nbsp;
                    <span id="btn-order-total">Rp 0</span>
                    &nbsp;— Order
                </button>

            </div>
        </div>
    </div>

</div>{{-- /flex wrapper --}}


{{-- ════════════════════════════════
     Order success modal
════════════════════════════════ --}}
<div id="modal-success"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-2xl p-8 w-full max-w-sm text-center shadow-xl">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
             style="background-color: rgba(34,197,94,0.1);">
            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <h2 class="text-xl font-black text-gray-800 mb-1">Pesanan Selesai!</h2>
        <p id="modal-detail" class="text-sm text-gray-500 mb-6"></p>
        <button type="button" id="btn-new-order"
                class="w-full py-2.5 rounded-xl font-bold text-sm hover:opacity-80"
                style="background: var(--color-primary); color: var(--color-white);">
            Pesanan Baru
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    var TAX_RATE = 0.11;
    var cart     = {};   // { id: { name, price, qty } }

    /* ── Helpers ──────────────────────────────────────── */
    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    /* ── Recalculate totals & re-render cart ─────────── */
    function recalc() {
        var subtotal  = 0;
        var itemCount = 0;
        var $list     = $('#cart-list').empty();

        $.each(cart, function (id, item) {
            if (item.qty <= 0) return;
            var sub = item.price * item.qty;
            subtotal  += sub;
            itemCount += item.qty;

            var $row = $(
                '<div class="cart-item flex items-center gap-2" data-id="' + id + '" data-price="' + item.price + '">' +
                    '<div class="flex-1 min-w-0">' +
                        '<p class="text-sm font-semibold text-gray-800 truncate">' + item.name + '</p>' +
                        '<p class="text-xs text-gray-400">Rp ' + item.price.toLocaleString('id-ID') + ' / pcs</p>' +
                    '</div>' +
                    '<div class="flex items-center gap-1.5 shrink-0">' +
                        '<button class="btn-minus w-7 h-7 rounded-full border flex items-center justify-center text-gray-500 hover:bg-gray-50 font-bold text-base leading-none" style="border-color:var(--color-border);">−</button>' +
                        '<span class="qty-display w-6 text-center text-sm font-bold text-gray-800">' + item.qty + '</span>' +
                        '<button class="btn-plus w-7 h-7 rounded-full border flex items-center justify-center text-gray-500 hover:bg-gray-50 font-bold text-base leading-none" style="border-color:var(--color-border);">+</button>' +
                    '</div>' +
                    '<p class="item-subtotal text-sm font-bold w-20 text-right shrink-0" style="color:var(--color-primary);">' + rupiah(sub) + '</p>' +
                '</div>'
            );
            $list.append($row);
        });

        var tax   = subtotal * TAX_RATE;
        var total = subtotal + tax;

        $('#summary-subtotal').text(rupiah(subtotal));
        $('#summary-tax').text(rupiah(tax));
        $('#summary-total').text(rupiah(total));
        $('#btn-order-count').text(itemCount + ' item');
        $('#btn-order-total').text(rupiah(total));

        $('#cart-empty').toggle(itemCount === 0);
    }

    /* ── Add/increment from product grid ────────────── */
    $('#product-grid').on('click', '.product-card', function () {
        var id    = $(this).data('id');
        var name  = $(this).data('name');
        var price = parseInt($(this).data('price'), 10);

        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { name: name, price: price, qty: 1 };
        }
        recalc();
    });

    /* ── Qty buttons (delegated on cart list) ────────── */
    $('#cart-list').on('click', '.btn-minus', function () {
        var id = $(this).closest('.cart-item').data('id');
        if (cart[id]) {
            cart[id].qty--;
            if (cart[id].qty <= 0) delete cart[id];
        }
        recalc();
    });

    $('#cart-list').on('click', '.btn-plus', function () {
        var id = $(this).closest('.cart-item').data('id');
        if (cart[id]) cart[id].qty++;
        recalc();
    });

    /* ── Search ──────────────────────────────────────── */
    $('#search-input').on('input', function () {
        var q   = $(this).val().toLowerCase();
        var vis = 0;
        $('.product-card').each(function () {
            var match = $(this).data('name').toLowerCase().includes(q);
            $(this).toggle(match);
            if (match) vis++;
        });
        $('#no-results').toggleClass('hidden', vis > 0);
    });

    /* ── Category filter ─────────────────────────────── */
    $(document).on('click', '.filter-tab', function () {
        var cat = $(this).data('cat');

        // Update active tab style
        $('.filter-tab').each(function () {
            $(this).css({
                'background-color': '',
                'color': '#666'
            }).removeClass('active');
        });
        $(this).css({
            'background-color': 'var(--color-primary)',
            'color': 'var(--color-white)'
        }).addClass('active');

        var vis = 0;
        $('.product-card').each(function () {
            var show = (cat === 'all') || ($(this).data('category') === cat);
            $(this).toggle(show);
            if (show) vis++;
        });
        $('#no-results').toggleClass('hidden', vis > 0);
    });

    /* ── Save customer info ──────────────────────────── */
    $('#btn-save-customer').on('click', function () {
        var name  = $.trim($('#customer-name').val());
        var phone = $.trim($('#customer-phone').val());
        if (!name && !phone) { return; }
        $(this).text('Tersimpan ✓')
               .css('background-color', '#22c55e')
               .prop('disabled', true);
        setTimeout(function () {
            $('#btn-save-customer').text('Simpan')
                                   .css('background-color', 'var(--color-accent)')
                                   .prop('disabled', false);
        }, 2000);
    });

    /* ── Process order ───────────────────────────────── */
    $('#btn-process-order').on('click', function () {
        var itemCount = 0;
        $.each(cart, function (id, item) { itemCount += item.qty; });

        if (itemCount === 0) {
            alert('Tambahkan produk ke pesanan terlebih dahulu.');
            return;
        }

        var total = $('#summary-total').text();
        var name  = $.trim($('#customer-name').val()) || 'Walk-in';
        $('#modal-detail').text(itemCount + ' item · ' + name + ' · ' + total);
        $('#modal-success').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    /* ── New order (close modal + reset) ────────────── */
    $('#btn-new-order').on('click', function () {
        cart = {};
        recalc();
        $('#customer-name, #customer-phone').val('');
        $('#modal-success').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    /* ── Close modal on backdrop click ──────────────── */
    $('#modal-success').on('click', function (e) {
        if (e.target === this) $('#btn-new-order').trigger('click');
    });

    /* ── Init ────────────────────────────────────────── */
    recalc();

});
</script>
@endpush
