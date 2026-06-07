@extends('layouts.customer')
@section('title', 'Riwayat Pesanan – Corndog-Ku')

@push('styles')
<style>
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:9999px;font-size:12px;font-weight:700; }
    .filter-tab   { padding:6px 18px;border-radius:9999px;font-size:13px;font-weight:600;white-space:nowrap;transition:background 0.15s,color 0.15s; }
    .filter-tab:hover { opacity:0.85; }
</style>
@endpush

@section('content')

{{-- PAGE WRAPPER --}}
<div class="relative min-h-screen" style="background-color:var(--color-light);">

    {{-- Decorative blob (top-right) --}}
    <div class="absolute top-0 right-0 pointer-events-none overflow-hidden" style="width:380px;height:380px;z-index:0;">
        <svg viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M350 30 Q420 100 370 200 Q320 300 200 330 Q80 360 40 260 Q0 160 80 80 Q160 0 350 30Z"
                  fill="#FFBE54" opacity="0.18"/>
        </svg>
    </div>

    <div class="relative z-10 max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pb-20">

        {{-- HERO --}}
        <div class="flex flex-col sm:flex-row sm:items-end gap-6 pt-10 mb-10">

            {{-- Avatar --}}
            <div class="flex-none self-center sm:self-auto">
                <div class="w-36 h-36 rounded-full overflow-hidden flex items-center justify-center
                            text-white text-5xl font-bold select-none"
                     style="background-color: {{ auth()->user()->profile_picture_url ? 'transparent' : 'var(--color-primary)' }};
                            box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);">
                    @if (auth()->user()->profile_picture_url)
                        <img src="{{ auth()->user()->profile_picture_url }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </div>

            {{-- Greeting --}}
            <div class="text-center sm:text-left">
                <p class="text-4xl sm:text-5xl font-bold leading-tight"
                   style="color: var(--color-black);">
                    Halo <span style="color: var(--color-primary);">{{ auth()->user()->name }}!</span>
                </p>
                <p class="text-sm mt-1" style="color: #9c9c9c;">
                    Lihat semua pesanan, status, dan detail transaksimu di sini.
                </p>
            </div>

        </div>

        {{-- TAB NAVIGATION (Profile / History) --}}
        <div class="flex mb-8 overflow-hidden"
             style="background-color: rgba(255,255,255,0.9);
                    box-shadow: 3px 4px 20px 0px rgba(0,0,0,0.25);
                    border-radius: 15px;">

            <a href="{{ route('profile') }}"
               class="relative flex items-center justify-center gap-2
                      px-8 py-4 flex-1 text-sm font-medium transition-colors"
               style="color: #9c9c9c;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil Saya</span>
            </a>

            <a href="{{ route('history') }}"
               class="relative flex items-center justify-center gap-2
                      px-8 py-4 flex-1 text-sm font-bold transition-colors"
               style="color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-none" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Riwayat Pesanan</span>
                <div class="absolute bottom-0 left-0 right-0 h-[2px]"
                     style="background-color: var(--color-primary);"></div>
            </a>

        </div>{{-- /.tab bar --}}

        {{-- ORDERS CARD --}}
        <div class="bg-white rounded-2xl overflow-hidden mb-12"
             style="box-shadow:0 4px 30px rgba(0,0,0,0.08);">

            {{-- Card header --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b" style="border-color:#f0f0f0;">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-none"
                     style="background-color:rgba(166,23,28,0.10);">
                    <svg class="w-5 h-5" fill="none" stroke="#A6171C" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                 m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h2 class="font-bold text-lg" style="color:var(--color-black);">Riwayat Pesanan</h2>
            </div>

            {{-- Status filter tabs --}}
            @php
                $tabs = [
                    ['label' => 'Semua',      'key' => null],
                    ['label' => 'Menunggu',   'key' => 'Menunggu'],
                    ['label' => 'Diproses',   'key' => 'Diproses'],
                    ['label' => 'Selesai',    'key' => 'Selesai'],
                    ['label' => 'Dibatalkan', 'key' => 'Dibatalkan'],
                ];
            @endphp
            <div class="filter-tab-wrap relative pt-5 pb-4">
                <div class="px-6 flex gap-2 overflow-x-auto hide-scrollbar">
                    @foreach($tabs as $tab)
                        @php $isActive = $statusFilter === $tab['key']; @endphp
                        <a href="{{ route('history', $tab['key'] ? ['status' => $tab['key']] : []) }}"
                           class="filter-tab {{ $isActive ? 'text-white' : 'text-gray-500 bg-gray-100' }}"
                           style="{{ $isActive ? 'background-color:var(--color-primary);' : '' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>
                {{-- Right-edge fade indicating more pills offscreen --}}
                <div class="pointer-events-none absolute top-0 right-0 h-full w-8" aria-hidden="true"
                     style="background:linear-gradient(to left, white 30%, rgba(255,255,255,0));"></div>
            </div>

            {{-- Order list --}}
            @php
                $statusConfig = [
                    'Pending'   => ['label'=>'Menunggu',   'color'=>'#3B82F6', 'bg'=>'#EFF6FF',
                                    'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Preparing' => ['label'=>'Diproses',   'color'=>'#F59E0B', 'bg'=>'#FFFBEB',
                                    'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Ready'     => ['label'=>'Siap Ambil', 'color'=>'#8B5CF6', 'bg'=>'#F5F3FF',
                                    'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Completed' => ['label'=>'Selesai',    'color'=>'#10B981', 'bg'=>'#ECFDF5',
                                    'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Cancelled' => ['label'=>'Dibatalkan', 'color'=>'#6B7280', 'bg'=>'#F9FAFB',
                                    'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp

            @php
                // Image maps centralized in config/corndog.php (shared with the
                // cashier/owner order-detail drawers via Controller::mapOrderItems).
                $customVarianMap = collect(config('corndog.varian_images', []))->map(fn($p) => asset($p))->all();
                $customSauceMap  = collect(config('corndog.sauce_images', []))->map(fn($p) => asset($p))->all();
            @endphp
            @forelse($orders as $order)
                @php
                    $sc = $statusConfig[$order->status] ?? $statusConfig['Pending'];
                    $customItem = $order->items->first(fn($i) => !empty($i->custom_notes));
                    $thumbVarianImg = null;
                    $thumbSauceImg  = null;
                    if ($customItem) {
                        $cn = json_decode($customItem->custom_notes, true) ?? [];
                        $thumbVarianImg = $customVarianMap[$cn['varian'] ?? ''] ?? null;
                        $firstSauce = trim(explode(',', $cn['sauces'] ?? '')[0]);
                        $thumbSauceImg = $customSauceMap[$firstSauce] ?? null;
                    }
                @endphp
                <div class="border-b" style="border-color:#f5f5f5;">
                    <div class="flex items-start gap-4 px-6 py-5">

                        {{-- Product thumbnail --}}
                        @if ($customItem && $thumbVarianImg)
                            <div class="relative w-20 h-20 rounded-2xl overflow-hidden flex-none border"
                                 style="background-color:#FDECD8; border-color:#ececec; flex-shrink:0;">
                                <img src="{{ $thumbVarianImg }}" alt="base"
                                     class="absolute inset-0 w-full h-full object-contain">
                                @if ($thumbSauceImg)
                                    <img src="{{ $thumbSauceImg }}" alt="sauce"
                                         class="absolute inset-0 w-full h-full object-contain"
                                         style="z-index:10;">
                                @endif
                            </div>
                        @else
                            <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}"
                                 alt="Order thumbnail"
                                 class="w-20 h-20 rounded-2xl object-cover flex-none border"
                                 style="border-color:#ececec;">
                        @endif

                        {{-- Order body --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 flex-wrap">

                                {{-- Left: order info + items --}}
                                <div class="min-w-0">
                                    <p class="font-bold text-base leading-tight" style="color:var(--color-black);">
                                        #{{ $order->order_number }}
                                    </p>
                                    <p class="text-xs mt-0.5" style="color:#9c9c9c;">
                                        {{ $order->created_at->translatedFormat('d M Y') }}
                                        &bull;
                                        {{ $order->created_at->format('H:i') }}
                                    </p>
                                    <p class="text-xs font-bold mt-2" style="color:var(--color-primary);">
                                        {{ $order->items->count() }} Item
                                    </p>

                                    <ul class="mt-1.5 space-y-0.5">
                                        @foreach($order->items as $item)
                                            <li class="flex items-baseline gap-3 text-xs" style="color:#555;">
                                                <span class="flex-1">
                                                    &bull; {{ $item->product_name }}
                                                    @if($item->custom_notes)
                                                        @php $cn = json_decode($item->custom_notes, true); @endphp
                                                        @if(!empty($cn))
                                                            <span class="text-[10px]" style="color:#9c9c9c;">
                                                                ({{ implode(' · ', array_filter([
                                                                    !empty($cn['isi'])    ? $cn['isi']    : null,
                                                                    !empty($cn['varian']) ? $cn['varian'] : null,
                                                                    !empty($cn['sauces']) ? $cn['sauces'] : null,
                                                                ])) }})
                                                            </span>
                                                        @endif
                                                    @endif
                                                </span>
                                                <span class="font-semibold flex-none" style="color:#9c9c9c;">
                                                    x{{ $item->quantity }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Right: status badge --}}
                                <div class="status-badge flex-none"
                                     style="background-color:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                    <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['icon'] }}"/>
                                    </svg>
                                    {{ $sc['label'] }}
                                </div>

                            </div>

                            {{-- Total + Struk button --}}
                            <div class="mt-3 flex items-end justify-between gap-3">
                                <button type="button"
                                        onclick="toggleReceipt('receiptModal-{{ $order->id }}')"
                                        class="flex items-center gap-1.5 text-xs font-semibold
                                               px-3 py-1.5 rounded-full transition-opacity hover:opacity-70"
                                        style="background-color:rgba(166,23,28,0.08);color:var(--color-primary);">
                                    <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0
                                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1
                                                 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Lihat Struk
                                </button>
                                <div class="text-right">
                                    <p class="text-xs" style="color:#9c9c9c;">Total Pembayaran</p>
                                    <p class="font-black text-lg leading-tight" style="color:var(--color-primary);">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BILL Modal — Figma-matched receipt for order #{{ $order->id }} --}}
                @php
                    $receiptType = match ($order->order_type) {
                        'dine-in'  => 'Dine-in',
                        'takeaway' => 'Takeaway',
                        'online'   => 'Online',
                        default    => ucfirst($order->order_type ?? 'Online'),
                    };
                    $paymentLabel    = $order->order_type === 'online' ? 'QRIS / Midtrans' : 'Tunai';
                    $receiptPhone    = $order->customer_phone ?? auth()->user()->phone ?? null;
                    $receiptSubtotal = (int) round($order->total_price / 1.11);
                    $receiptTax      = $order->total_price - $receiptSubtotal;
                @endphp
                <div id="receiptModal-{{ $order->id }}"
                     class="fixed inset-0 z-50 hidden bg-black/60 items-center justify-center p-4"
                     onclick="if(event.target===this) toggleReceipt('receiptModal-{{ $order->id }}')">
                    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative">

                        {{-- Top-right X close --}}
                        <button type="button"
                                data-capture-ignore
                                onclick="toggleReceipt('receiptModal-{{ $order->id }}')"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600
                                       cursor-pointer transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Status badge --}}
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full
                                    bg-[#E8F5E9] text-[#4CAF50] mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-2xl font-bold text-center text-gray-900 mb-6">Order Complete!</h3>

                        {{-- Customer & order metadata --}}
                        <div class="bg-[#F5F5F5] rounded-xl p-4 mb-6 text-sm text-gray-600 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Name</span>
                                <span class="font-semibold text-gray-800">
                                    {{ auth()->user()->name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Phone</span>
                                <span class="font-semibold text-gray-800">
                                    {{ $receiptPhone ?? '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Order Type</span>
                                <span class="font-semibold text-gray-800">{{ $receiptType }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Payment</span>
                                <span class="font-semibold text-gray-800">{{ $paymentLabel }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Date</span>
                                <span class="font-semibold text-gray-800">
                                    {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>

                        {{-- Order summary --}}
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                            Order Summary
                        </p>
                        <div class="space-y-2">
                            @foreach($order->items as $rItem)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        {{ $rItem->quantity }}x {{ $rItem->product_name }}
                                    </span>
                                    <span class="font-semibold text-gray-800 flex-none ml-3">
                                        Rp {{ number_format($rItem->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Subtotal + Tax --}}
                        <div class="flex justify-between text-gray-500 text-sm mt-3">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($receiptSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500 text-sm mb-3">
                            <span>Tax (11%)</span>
                            <span>Rp {{ number_format($receiptTax, 0, ',', '.') }}</span>
                        </div>

                        {{-- Dashed separator --}}
                        <div class="border-t border-dashed border-gray-300 my-4"></div>

                        {{-- Total --}}
                        <div class="flex items-center justify-between mb-6">
                            <span class="font-semibold text-gray-600">Total</span>
                            <span class="text-xl font-bold text-gray-900">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- WhatsApp / Fonnte button --}}
                        <button type="button"
                                data-capture-ignore
                                data-order-id="{{ $order->id }}"
                                data-phone="{{ $receiptPhone }}"
                                onclick="sendFonnteWhatsApp(this)"
                                class="bg-[#4CAF50] hover:bg-[#43A047] text-white font-semibold
                                       w-full py-3 rounded-xl flex items-center justify-center
                                       gap-2 shadow-md transition-colors">
                            <svg class="w-5 h-5 flex-none" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            Send Receipt via WhatsApp
                        </button>

                        {{-- Close button --}}
                        <button type="button"
                                data-capture-ignore
                                onclick="toggleReceipt('receiptModal-{{ $order->id }}')"
                                class="bg-[#E0E0E0] hover:bg-[#D6D6D6] text-gray-700 font-semibold
                                       w-full py-3 rounded-xl mt-3 transition-colors">
                            Close
                        </button>

                    </div>
                </div>

            @empty
                <div class="py-20 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-5"
                         style="background-color:rgba(166,23,28,0.08);">
                        <svg class="w-8 h-8" fill="none" stroke="#A6171C" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                     M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="font-bold text-base" style="color:var(--color-black);">Belum ada pesanan</p>
                    <p class="text-sm mt-1" style="color:#9c9c9c;">Yuk, pesan corndog favoritmu sekarang!</p>
                    <a href="{{ route('menu') }}"
                       class="inline-flex items-center gap-2 mt-5 px-6 py-2.5 rounded-full
                              text-white text-sm font-bold transition-opacity hover:opacity-80"
                       style="background-color:var(--color-primary);">
                        Lihat Menu
                    </a>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="px-6 py-5 flex items-center justify-center gap-1">
                    {{-- Prev --}}
                    @if($orders->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-full text-gray-300 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}"
                           class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page === $orders->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-full
                                         text-white text-sm font-bold"
                                  style="background-color:var(--color-primary);">{{ $page }}</span>
                        @elseif(abs($page - $orders->currentPage()) <= 2 || $page === 1 || $page === $orders->lastPage())
                            <a href="{{ $url }}"
                               class="w-9 h-9 flex items-center justify-center rounded-full
                                      text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                                {{ $page }}
                            </a>
                        @elseif(abs($page - $orders->currentPage()) === 3)
                            <span class="w-9 h-9 flex items-center justify-center text-sm text-gray-400">…</span>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}"
                           class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center rounded-full text-gray-300 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </div>
            @endif

        </div>{{-- /.orders card --}}

    </div>{{-- /.container --}}
</div>{{-- /.page --}}


@endsection

@push('scripts')
<script>
/*  Toggle receipt modal visibility  */
function toggleReceipt(modalID) {
    var modal = document.getElementById(modalID);
    if (modal) {
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
}

/*  Send plain-text receipt via Fonnte (no image capture)  */
function sendFonnteWhatsApp(button) {
    let orderId      = $(button).data('order-id');
    let originalText = $(button).html();
    $(button).html('Mengirim WhatsApp...').prop('disabled', true);

    $.post('/orders/' + orderId + '/send-whatsapp', {
        _token: '{{ csrf_token() }}'
    })
    .done(function (res) {
        if (res.success) {
            // Inline success feedback — no popup
            $(button)
                .html('✓ Struk Terkirim!')
                .prop('disabled', true)
                .css({ background: '#16A34A', opacity: '1' });
        } else {
            alert(res.message || 'Gagal mengirim struk.');
            $(button).html(originalText).prop('disabled', false);
        }
    })
    .fail(function (xhr) {
        let errorMsg = xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message
            : 'Terjadi kesalahan saat mengirim.';
        alert(errorMsg);
        $(button).html(originalText).prop('disabled', false);
    });
}

/*  Auto-open after Midtrans redirect  */
@if(session('show_receipt_for_order'))
window.addEventListener('DOMContentLoaded', function () {
    toggleReceipt('receiptModal-{{ session("show_receipt_for_order") }}');
});
@endif
</script>
@endpush
