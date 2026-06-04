<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — Corndog-Ku</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($snapToken)
    <script src="{{ config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $clientKey }}"></script>
    @endif
    <style>
        body { background-color: #FFFDDB; }
        .payment-method-card {
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .payment-method-card.selected {
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 3px rgba(166,23,28,0.12);
        }
        .qris-pulse {
            animation: qris-glow 2s ease-in-out infinite;
        }
        @keyframes qris-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(166,23,28,0.0); }
            50%       { box-shadow: 0 0 0 10px rgba(166,23,28,0.08); }
        }
    </style>
</head>
<body class="font-sans antialiased" style="color: var(--color-black);">

{{-- ══════════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white border-b"
        style="border-color: var(--color-border); box-shadow: 0 1px 6px rgba(0,0,0,0.07);">
    <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-16 h-16 flex items-center justify-between gap-6">

        <a href="{{ route('welcome') }}" class="flex items-center gap-2 flex-none">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku"
                 class="w-10 h-10 rounded-full object-cover">
            <span class="font-bold text-base tracking-tight hidden sm:inline"
                  style="color: var(--color-black);">Corndog-Ku</span>
        </a>

        {{-- Breadcrumb --}}
        <div class="hidden sm:flex items-center gap-2 text-sm font-medium flex-1 justify-center">
            <a href="{{ route('cart') }}" class="hover:opacity-70" style="color: #9c9c9c;">Keranjang</a>
            <span style="color: #d1d5db;">›</span>
            <span class="font-bold" style="color: var(--color-primary);">Checkout</span>
        </div>

        {{-- Right: Cart → Avatar → Greeting → Logout --}}
        <div class="flex items-center gap-4 flex-none">
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
                      style="background-color: var(--color-accent); color: var(--color-black);">{{ count($cart) }}</span>
            </a>
            @auth
                <a href="{{ route('profile') }}"
                   class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center
                          text-white text-sm font-extrabold transition-opacity hover:opacity-80"
                   style="background-color: {{ auth()->user()->profile_photo ? 'transparent' : 'var(--color-primary)' }};"
                   title="{{ auth()->user()->name }}">
                    @if (auth()->user()->profile_photo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->profile_photo) }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </a>
                <span class="hidden sm:block text-sm font-semibold" style="color: var(--color-black);">
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
            @endauth
        </div>
    </div>
</header>


{{-- ══════════════════════════════════════════════════════════════
     PAGE CONTENT
══════════════════════════════════════════════════════════════ --}}
<main>

{{-- Page header --}}
<section style="background-color: var(--color-light);">
    <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-16 pt-8 pb-6">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('cart') }}"
               class="w-8 h-8 flex items-center justify-center rounded-full bg-white border hover:bg-gray-50 transition-colors"
               style="border-color: var(--color-border);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl sm:text-4xl font-bold" style="color: var(--color-black);">Checkout</h1>
        </div>
        <p class="text-sm font-medium ml-11" style="color: #9c9c9c;">
            Periksa pesananmu dan selesaikan pembayaran via QRIS
        </p>
    </div>
</section>


{{-- Two-column layout --}}
<div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 lg:px-16 py-8
            flex flex-col lg:flex-row gap-8 items-start">

    {{-- ════════════════════
         LEFT — Order Summary
    ════════════════════ --}}
    <div class="w-full lg:flex-1 flex flex-col gap-5">

        {{-- Order items card --}}
        <div class="bg-white rounded-2xl overflow-hidden"
             style="box-shadow: 0 2px 20px rgba(0,0,0,0.09);">

            <div class="px-6 py-4 border-b" style="border-color: #f0f0f0;">
                <h2 class="font-bold text-lg" style="color: var(--color-black);">Rincian Pesanan</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ count($cart) }} item{{ count($cart) > 1 ? '' : '' }}</p>
            </div>

            <div class="divide-y" style="divide-color: #f5f5f5;">
                @foreach ($cart as $item)
                <div class="flex items-start gap-4 px-6 py-4">
                    @if (!empty($item['is_custom']))
                        <div class="relative w-16 h-16 rounded-xl overflow-hidden flex-none border"
                             style="background-color:#FDECD8; border-color:#ececec; flex-shrink:0;">
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
                             class="w-16 h-16 rounded-xl object-cover flex-none border"
                             style="border-color:#ececec;"
                             onerror="this.src='{{ asset('assets/img/CA_ORIGINAL.png') }}'">
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-bold text-sm leading-tight" style="color: var(--color-black);">
                                {{ $item['name'] }}
                            </p>
                            @if (!empty($item['is_custom']))
                                <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded-full"
                                      style="background-color: #FFF3CD; color: #A6171C; border: 1px solid #FFBE54;">
                                    Custom
                                </span>
                            @endif
                        </div>

                        @if (!empty($item['is_custom']))
                            <p class="text-xs mt-0.5 leading-snug" style="color: #9c9c9c;">
                                {{ implode(' · ', array_filter([
                                    $item['isi'] ?? null,
                                    $item['varian'] ?? null,
                                    !empty($item['sauces']) ? $item['sauces'] : null,
                                ])) }}
                            </p>
                        @elseif (!empty($item['description']))
                            <p class="text-xs mt-0.5 leading-snug line-clamp-2" style="color: #9c9c9c;">
                                {{ $item['description'] }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">
                                × {{ $item['qty'] }}
                            </span>
                            <span class="font-bold text-sm" style="color: var(--color-primary);">
                                Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>{{-- /.order items card --}}


        {{-- Price breakdown card --}}
        <div class="bg-white rounded-2xl px-6 py-5"
             style="box-shadow: 0 2px 20px rgba(0,0,0,0.09);">

            <h3 class="font-bold text-base mb-4" style="color: var(--color-black);">Ringkasan Biaya</h3>

            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span style="color: #848383;">Sub Total</span>
                    <span class="font-medium" style="color: var(--color-black);">
                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color: #848383;">Pajak (11%)</span>
                    <span class="font-medium" style="color: var(--color-black);">
                        Rp {{ number_format($tax, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t flex justify-between items-center"
                 style="border-color: #f0f0f0;">
                <span class="font-bold text-base" style="color: var(--color-black);">Total Pembayaran</span>
                <span class="font-black text-2xl" style="color: var(--color-primary);">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>

            {{-- Order ID --}}
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-gray-400">No. Order</span>
                <span class="text-xs font-mono font-semibold text-gray-500">{{ $orderId }}</span>
            </div>
        </div>

    </div>{{-- /.left --}}


    {{-- ════════════════════
         RIGHT — Payment
    ════════════════════ --}}
    <div class="w-full lg:w-[380px] flex flex-col gap-5">

        {{-- Payment method selector --}}
        <div class="bg-white rounded-2xl overflow-hidden"
             style="box-shadow: 0 2px 20px rgba(0,0,0,0.09);">

            <div class="px-6 py-4 border-b" style="border-color: #f0f0f0;">
                <h2 class="font-bold text-lg" style="color: var(--color-black);">Metode Pembayaran</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pilih metode pembayaran yang kamu inginkan</p>
            </div>

            <div class="px-6 py-4">

                {{-- QRIS option (only option, pre-selected) --}}
                <div class="payment-method-card selected cursor-pointer rounded-2xl border-2 p-4
                            flex items-center gap-4"
                     style="border-color: var(--color-primary); background-color: #FFF8F8;">

                    {{-- QRIS logo box --}}
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-none"
                         style="background: linear-gradient(135deg, #FF0080 0%, #FF4D4D 50%, #FF8C00 100%);">
                        <span class="text-white font-black text-lg tracking-tight">QR</span>
                    </div>

                    <div class="flex-1">
                        <p class="font-bold text-sm" style="color: var(--color-black);">QRIS</p>
                        <p class="text-xs mt-0.5" style="color: #9c9c9c;">
                            GoPay · OVO · Dana · ShopeePay · dan lainnya
                        </p>
                    </div>

                    {{-- Selected radio --}}
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-none"
                         style="border-color: var(--color-primary);">
                        <div class="w-2.5 h-2.5 rounded-full"
                             style="background-color: var(--color-primary);"></div>
                    </div>
                </div>

                {{-- QRIS info blurb --}}
                <div class="mt-4 flex items-start gap-2.5 p-3 rounded-xl"
                     style="background-color: #FFF9E6; border: 1px solid #FFBE54;">
                    <svg class="w-4 h-4 flex-none mt-0.5" fill="none" stroke="#A6171C" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs leading-relaxed" style="color: #7a5000;">
                        Scan QR code dari aplikasi e-wallet favoritemu. Pembayaran real-time dan langsung dikonfirmasi.
                    </p>
                </div>

            </div>
        </div>{{-- /.payment selector --}}


        {{-- Pay button card --}}
        <div class="bg-white rounded-2xl px-6 py-6"
             style="box-shadow: 0 2px 20px rgba(0,0,0,0.09);">

            {{-- Total recap --}}
            <div class="flex items-center justify-between mb-5 p-4 rounded-xl"
                 style="background-color: #FFF3F3;">
                <span class="text-sm font-semibold" style="color: #848383;">Total</span>
                <span class="font-black text-xl" style="color: var(--color-primary);">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>

            @if ($snapToken)
                {{-- Midtrans Snap is available — show live pay button --}}
                <button id="btn-pay-qris" type="button"
                        class="qris-pulse w-full py-4 rounded-2xl font-black text-lg text-white
                               transition-all duration-200 hover:opacity-90 active:scale-[0.98]
                               flex items-center justify-center gap-3"
                        style="background: linear-gradient(135deg, #A6171C 0%, #C41E24 100%);
                               box-shadow: 0 6px 24px rgba(166,23,28,0.35);">
                    <svg class="w-6 h-6 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2"/>
                    </svg>
                    Bayar via QRIS
                </button>

                <p class="text-center text-xs text-gray-400 mt-3">
                    Kamu akan diarahkan ke halaman pembayaran Midtrans
                </p>
            @else
                {{-- Snap token not available (keys not configured) --}}
                <div class="w-full py-4 rounded-2xl text-center text-sm font-semibold border-2 border-dashed"
                     style="border-color: #d1d5db; color: #9ca3af;">
                    Pembayaran Midtrans belum dikonfigurasi
                </div>
                <p class="text-center text-xs text-gray-400 mt-3 leading-relaxed">
                    Tambahkan <code class="bg-gray-100 px-1 rounded text-xs">MIDTRANS_SERVER_KEY</code> &
                    <code class="bg-gray-100 px-1 rounded text-xs">MIDTRANS_CLIENT_KEY</code> di file <code class="bg-gray-100 px-1 rounded text-xs">.env</code>
                </p>
            @endif

            {{-- Back link --}}
            <div class="mt-4 text-center">
                <a href="{{ route('cart') }}"
                   class="text-sm font-semibold hover:opacity-70 transition-opacity"
                   style="color: #9c9c9c;">
                    ← Kembali ke Keranjang
                </a>
            </div>

        </div>{{-- /.pay button card --}}


        {{-- Security badges --}}
        <div class="grid grid-cols-3 gap-3">
            @foreach([
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label' => 'SSL Aman'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Terverifikasi'],
                ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'label' => '100% Halal'],
            ] as $b)
                <div class="bg-white rounded-xl p-3 flex flex-col items-center gap-1.5 text-center"
                     style="box-shadow: 0 1px 8px rgba(0,0,0,0.07);">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                         style="background-color: rgba(166,23,28,0.08);">
                        <svg class="w-4 h-4" fill="none" stroke="#A6171C" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $b['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-[10px] font-bold leading-tight" style="color: var(--color-black);">{{ $b['label'] }}</p>
                </div>
            @endforeach
        </div>

    </div>{{-- /.right --}}

</div>{{-- /.two-column --}}

</main>


{{-- ══════════════════════════════════════════════════════════════
     FOOTER (minimal)
══════════════════════════════════════════════════════════════ --}}
<footer class="mt-8 py-6 border-t text-center text-xs"
        style="border-color: var(--color-border); color: #9c9c9c;">
    &copy; {{ date('Y') }} Corndog-Ku. Pembayaran diproses aman oleh
    <span class="font-semibold" style="color: var(--color-primary);">Midtrans</span>.
</footer>


@if($snapToken)
<script>
$(function () {
    $('#btn-pay-qris').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html(
            '<svg class="w-5 h-5 animate-spin flex-none" fill="none" viewBox="0 0 24 24">' +
            '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
            '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>' +
            '</svg> Membuka QRIS...'
        );

        snap.pay('{{ $snapToken }}', {
            enabledPayments: ['qris', 'gopay', 'shopeepay'],
            onSuccess: function (result) {
                $btn.prop('disabled', true).html(
                    '<svg class="w-5 h-5 animate-spin flex-none" fill="none" viewBox="0 0 24 24">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>' +
                    '</svg> Menyimpan pesanan...'
                );
                $.ajax({
                    url: '{{ route("checkout.store") }}',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), order_number: '{{ $orderId }}' },
                    success: function (res) { window.location.href = res.redirect; },
                    error:   function ()    { window.location.href = '{{ route("history") }}'; }
                });
            },
            onPending: function (result) {
                $.ajax({
                    url: '{{ route("checkout.store") }}',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), order_number: '{{ $orderId }}' },
                    success: function (res) { window.location.href = res.redirect; },
                    error:   function ()    {
                        $btn.prop('disabled', false).html(
                            '<svg class="w-6 h-6 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" ' +
                            'd="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2"/>' +
                            '</svg> Bayar via QRIS'
                        );
                    }
                });
            },
            onError: function (result) {
                alert('Pembayaran gagal. Silakan coba lagi.');
                $btn.prop('disabled', false).html(
                    '<svg class="w-6 h-6 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" ' +
                    'd="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2"/>' +
                    '</svg> Bayar via QRIS'
                );
            },
            onClose: function () {
                $btn.prop('disabled', false).html(
                    '<svg class="w-6 h-6 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" ' +
                    'd="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2"/>' +
                    '</svg> Bayar via QRIS'
                );
            }
        });
    });
});
</script>
@endif

</body>
</html>
