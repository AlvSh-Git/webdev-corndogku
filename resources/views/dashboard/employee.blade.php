@extends('layouts.app')

@section('title', 'Dashboard — Kasir')

@section('content')

@php
    /* ── Mock / real data stubs ──────────────────────────────────── */
    $revenueToday   = $revenueToday   ?? 800000;
    $totalOrders    = $totalOrders    ?? 35;
    $onlineOrders   = $onlineOrders   ?? 20;
    $cashierOrders  = $cashierOrders  ?? 15;
    $pendingOrders  = $pendingOrders  ?? 8;
    $storeStatus    = $storeStatus    ?? 'available'; // 'available' | 'unavailable'

    $orders = $orders ?? [
        ['id' => '#C3322', 'avatar' => 'CB_01.png',  'customer' => 'Gabriella',        'sub' => 'Customer',  'source' => 'online',  'items' => '3× Mie Mozza',          'status' => 'Pending',    'time' => '11:27 AM', 'total' => 35000],
        ['id' => '#C3322', 'avatar' => 'CB_02.png',  'customer' => 'Olivia',           'sub' => 'Customer',  'source' => 'online',  'items' => '1× Original',           'status' => 'Pending',    'time' => '11:22 AM', 'total' => 16000],
        ['id' => '#C3349', 'avatar' => 'CB_03.png',  'customer' => 'Ricky',            'sub' => 'Customer',  'source' => 'online',  'items' => '1× Squid Nori',         'status' => 'Preparing',  'time' => '10:16 AM', 'total' => 18000],
        ['id' => '#C3340', 'avatar' => null,          'customer' => 'Siti Anggraeni',   'sub' => 'Customer',  'source' => 'cashier', 'items' => '2× Mozza Cheese',       'status' => 'Preparing',  'time' => '11:05 AM', 'total' => 18000],
        ['id' => '#C3341', 'avatar' => null,          'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '2× Original',           'status' => 'Preparing',  'time' => '10:55 AM', 'total' => 24000],
        ['id' => '#C3344', 'avatar' => 'CB_04.png',  'customer' => 'Budi',             'sub' => 'Customer',  'source' => 'online',  'items' => '1× Squid Nori',         'status' => 'Ready',      'time' => '10:43 AM', 'total' => 18000],
        ['id' => '#C3348', 'avatar' => null,          'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '1× Squid Nori',         'status' => 'Ready',      'time' => '10:36 AM', 'total' => 18000],
        ['id' => '#C3344', 'avatar' => 'CB_01.png',  'customer' => 'Nadia',            'sub' => 'Customer',  'source' => 'online',  'items' => '3× Mie Mozza',          'status' => 'Completed',  'time' => '09:30 AM', 'total' => 35000],
        ['id' => '#C3344', 'avatar' => 'CB_02.png',  'customer' => 'Nadia',            'sub' => 'Customer',  'source' => 'online',  'items' => '2× Mozza Cheese',       'status' => 'Completed',  'time' => '08:30 AM', 'total' => 18000],
        ['id' => '#C3344', 'avatar' => 'CB_03.png',  'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '1× Mozza Cheese',       'status' => 'Completed',  'time' => '08:00 AM', 'total' => 18000],
    ];

    $statusConfig = [
        'Pending'   => ['bg' => 'rgba(156,163,175,0.18)', 'text' => '#6B7280',  'dot' => '#9CA3AF'],
        'Preparing' => ['bg' => 'rgba(255,158,0,0.15)',   'text' => '#B45309',  'dot' => '#FF9E00'],
        'Ready'     => ['bg' => 'rgba(34,197,94,0.15)',   'text' => '#15803D',  'dot' => '#22C55E'],
        'Completed' => ['bg' => 'rgba(36,211,102,0.12)',  'text' => '#1B8A44',  'dot' => '#24D366'],
        'Cancelled' => ['bg' => 'rgba(239,68,68,0.12)',   'text' => '#B91C1C',  'dot' => '#EF4444'],
    ];

    $tabCounts = [
        'All'       => count($orders),
        'Pending'   => collect($orders)->where('status', 'Pending')->count(),
        'Preparing' => collect($orders)->where('status', 'Preparing')->count(),
        'Ready'     => collect($orders)->where('status', 'Ready')->count(),
        'Completed' => collect($orders)->where('status', 'Completed')->count(),
        'Cancelled' => collect($orders)->where('status', 'Cancelled')->count(),
    ];
@endphp

{{-- ══════════════════════════════════════════════════════════════
     1. PAGE HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        {{-- Corndog mascot mini icon --}}
        <div class="w-10 h-10 rounded-full flex-none overflow-hidden">
            <img src="{{ asset('assets/ui/icon-dashboard.svg') }}" alt="" class="w-full h-full object-contain p-1"
                 style="background-color: var(--color-accent); border-radius: 50%;">
        </div>
        <div>
            <h1 class="text-xl md:text-3xl font-bold leading-tight" style="color: var(--color-black);">
                Welcome to Corndog-Ku!
            </h1>
            <p class="mt-0.5 text-sm" style="color: #888;">
                Jl. Rungkut Mejoyo Utara No.61, Blora
            </p>
        </div>
    </div>

    {{-- Status Store toggle --}}
    <div class="flex flex-col items-end gap-1">
        <span class="text-xs font-semibold tracking-wide" style="color: #555;">Status Store</span>
        <div class="flex rounded-full overflow-hidden border" style="border-color: var(--color-border);">
            <button id="btn-available" type="button"
                    class="store-toggle px-4 py-1.5 text-xs font-bold transition-colors whitespace-nowrap"
                    data-status="available"
                    style="{{ $storeStatus === 'available'
                        ? 'background-color: #22C55E; color: #fff;'
                        : 'background-color: #fff; color: #9CA3AF;' }}">
                Available
            </button>
            <button id="btn-unavailable" type="button"
                    class="store-toggle px-4 py-1.5 text-xs font-bold transition-colors whitespace-nowrap"
                    data-status="unavailable"
                    style="{{ $storeStatus === 'unavailable'
                        ? 'background-color: var(--color-primary); color: #fff;'
                        : 'background-color: #fff; color: #9CA3AF;' }}">
                Unavailable
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     2. STAT CARDS + CALENDAR
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Revenue Today --}}
    <div class="rounded-xl p-5 flex flex-col gap-2"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold" style="color: #555;">Revenue Today</p>
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-none"
                 style="background-color: #FEF9C3;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="#CA8A04" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0
                             2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1
                             M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold" style="color: var(--color-black);">
            Rp {{ number_format($revenueToday, 0, ',', '.') }}
        </p>
        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full self-start"
              style="background-color: #DCFCE7; color: #15803D;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
            </svg>
            +1% from yesterday
        </span>
    </div>

    {{-- Total Order Today --}}
    <div class="rounded-xl p-5 flex flex-col gap-2"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold" style="color: #555;">Total Order Today</p>
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-none"
                 style="background-color: #EEF2FF;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="#4F46E5" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold" style="color: var(--color-black);">{{ $totalOrders }} orders</p>
        <p class="text-xs">
            <span class="font-bold" style="color: var(--color-primary);">Online:</span>
            <span style="color: #555;"> {{ $onlineOrders }}</span>
            <span style="color: #CBD5E1;">&nbsp;|&nbsp;</span>
            <span class="font-bold" style="color: #FF9E00;">Cashier:</span>
            <span style="color: #555;"> {{ $cashierOrders }}</span>
        </p>
    </div>

    {{-- Pending Orders --}}
    <div class="rounded-xl p-5 flex flex-col gap-2"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold" style="color: #555;">Pending Orders</p>
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-none"
                 style="background-color: #FEF3C7;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="#D97706" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold" style="color: var(--color-black);">{{ $pendingOrders }} orders</p>
        <span class="text-xs font-semibold" style="color: var(--color-primary);">Perlu diproses</span>
    </div>

    {{-- Mini Calendar --}}
    <div class="rounded-xl p-4 sm:col-span-2 lg:col-span-1"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        @php
            $now       = now();
            $monthName = $now->translatedFormat('F Y');
            $today     = (int) $now->format('j');
            $dayOfWeek = (int) $now->startOfMonth()->format('N') % 7; // Sun=0
            $daysInMonth = (int) $now->format('t');
        @endphp
        <div class="flex items-center justify-between mb-3">
            <p class="font-bold text-sm" style="color: var(--color-black);">{{ now()->format('F Y') }}</p>
            <div class="flex gap-1">
                <button class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold"
                        style="background-color: #EAEDF1; color: #555;">&lsaquo;</button>
                <button class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold"
                        style="background-color: #EAEDF1; color: #555;">&rsaquo;</button>
            </div>
        </div>
        {{-- Day headers --}}
        <div class="grid grid-cols-7 text-center mb-1">
            @foreach (['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                <span class="text-[10px] font-semibold" style="color: rgba(60,60,67,0.4);">{{ $d }}</span>
            @endforeach
        </div>
        {{-- Date grid --}}
        <div class="grid grid-cols-7 text-center gap-y-1">
            @php $startBlank = now()->startOfMonth()->dayOfWeek; @endphp
            @for ($b = 0; $b < $startBlank; $b++)
                <span></span>
            @endfor
            @for ($d = 1; $d <= now()->daysInMonth; $d++)
                @if ($d === now()->day)
                    <span class="w-6 h-6 mx-auto flex items-center justify-center rounded-full text-xs font-bold text-white"
                          style="background-color: var(--color-primary);">{{ $d }}</span>
                @else
                    <span class="text-xs" style="color: var(--color-black);">{{ $d }}</span>
                @endif
            @endfor
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     3. ACTIVE ORDERS TABLE
══════════════════════════════════════════════════════════════ --}}
<div class="rounded-xl overflow-hidden"
     style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

    {{-- Table header bar --}}
    <div class="flex flex-wrap items-center gap-2 px-4 py-3"
         style="background-color: var(--color-primary);">
        <span class="font-bold text-white text-sm mr-1">Active Orders</span>

        @php
            $tabs = ['All', 'Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
            $tabColors = [
                'All'       => ['active_bg' => '#fff',     'active_text' => 'var(--color-primary)'],
                'Pending'   => ['active_bg' => '#6B7280',  'active_text' => '#fff'],
                'Preparing' => ['active_bg' => '#FF9E00',  'active_text' => '#fff'],
                'Ready'     => ['active_bg' => '#22C55E',  'active_text' => '#fff'],
                'Completed' => ['active_bg' => '#24D366',  'active_text' => '#fff'],
                'Cancelled' => ['active_bg' => '#EF4444',  'active_text' => '#fff'],
            ];
        @endphp

        @foreach ($tabs as $tab)
            <button type="button"
                    class="order-tab px-3 py-1 rounded-full text-xs font-bold transition-all whitespace-nowrap"
                    data-tab="{{ $tab }}"
                    style="{{ $tab === 'All'
                        ? 'background-color: #fff; color: var(--color-primary);'
                        : 'background-color: rgba(255,255,255,0.18); color: rgba(255,255,255,0.85);' }}">
                {{ $tab }} ({{ $tabCounts[$tab] ?? 0 }})
            </button>
        @endforeach
    </div>

    {{-- Responsive table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="orders-table">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border); background-color: #FAFAFA;">
                    @foreach (['Order ID', 'Customer', 'Source', 'Items', 'Status', 'Time', 'Total', 'Action'] as $col)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap"
                            style="color: #888;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="orders-tbody">
                @foreach ($orders as $order)
                    @php
                        $cfg = $statusConfig[$order['status']] ?? $statusConfig['Pending'];
                        $rowBg = in_array($order['status'], ['Preparing'])
                            ? 'background-color: rgba(255,158,0,0.04);'
                            : '';
                        $initials = strtoupper(substr($order['customer'], 0, 1));
                        $avatarColors = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6','#8B5CF6','#EC4899'];
                        $avatarBg = $avatarColors[crc32($order['customer']) % count($avatarColors)];
                    @endphp
                    <tr class="order-row transition-colors hover:bg-gray-50"
                        data-status="{{ $order['status'] }}"
                        style="{{ $rowBg }} border-bottom: 1px solid var(--color-border);">

                        {{-- Order ID --}}
                        <td class="px-4 py-3 font-mono font-semibold text-xs whitespace-nowrap"
                            style="color: var(--color-black);">{{ $order['id'] }}</td>

                        {{-- Customer --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if (!empty($order['avatar']))
                                    <img src="{{ asset('assets/img/' . $order['avatar']) }}"
                                         alt="{{ $order['customer'] }}"
                                         class="w-8 h-8 rounded-full object-cover flex-none">
                                @else
                                    <div class="w-8 h-8 rounded-full flex-none flex items-center justify-center text-white text-xs font-bold"
                                         style="background-color: {{ $avatarBg }};">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-xs" style="color: var(--color-black);">
                                        {{ $order['customer'] }}
                                    </p>
                                    @if (!empty($order['sub']))
                                        <p class="text-[10px]" style="color: #9CA3AF;">{{ $order['sub'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Source --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($order['source'] === 'online')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: #EEF2FF; color: #4F46E5;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
                                    </svg>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: #FEF3C7; color: #B45309;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9"/>
                                    </svg>
                                    Cashier
                                </span>
                            @endif
                        </td>

                        {{-- Items --}}
                        <td class="px-4 py-3 text-xs max-w-[140px]"
                            style="color: #555;">{{ $order['items'] }}</td>

                        {{-- Status --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['text'] }};">
                                <span class="w-1.5 h-1.5 rounded-full flex-none"
                                      style="background-color: {{ $cfg['dot'] }};"></span>
                                {{ $order['status'] }}
                            </span>
                        </td>

                        {{-- Time --}}
                        <td class="px-4 py-3 text-xs whitespace-nowrap"
                            style="color: #555;">{{ $order['time'] }}</td>

                        {{-- Total --}}
                        <td class="px-4 py-3 text-xs font-bold whitespace-nowrap"
                            style="color: var(--color-black);">
                            Rp {{ number_format($order['total'], 0, ',', '.') }}
                        </td>

                        {{-- Action --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button type="button"
                                    class="view-detail-btn px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors hover:opacity-80"
                                    data-order-id="{{ $order['id'] }}"
                                    style="border-color: var(--color-primary); color: var(--color-primary); background-color: transparent;">
                                View Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- View all link --}}
    <div class="flex justify-center py-4" style="border-top: 1px solid var(--color-border);">
        <a href="{{ route('cashier.purchase') }}"
           class="flex items-center gap-1 text-sm font-semibold hover:underline"
           style="color: var(--color-primary);">
            View all orders
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     jQuery — Tab filter + Store toggle
══════════════════════════════════════════════════════════════ --}}
<script>
$(function () {

    /* ── Order filter tabs ──────────────────────────────────────── */
    $('.order-tab').on('click', function () {
        const tab = $(this).data('tab');

        // Reset all tab styles
        $('.order-tab').css({
            'background-color': 'rgba(255,255,255,0.18)',
            'color': 'rgba(255,255,255,0.85)'
        });

        // Activate clicked tab
        if (tab === 'All') {
            $(this).css({ 'background-color': '#fff', 'color': 'var(--color-primary)' });
        } else {
            const activeMap = {
                'Pending':   { bg: '#6B7280',  text: '#fff' },
                'Preparing': { bg: '#FF9E00',  text: '#fff' },
                'Ready':     { bg: '#22C55E',  text: '#fff' },
                'Completed': { bg: '#24D366',  text: '#fff' },
                'Cancelled': { bg: '#EF4444',  text: '#fff' },
            };
            const c = activeMap[tab] || { bg: '#fff', text: 'var(--color-primary)' };
            $(this).css({ 'background-color': c.bg, 'color': c.text });
        }

        // Show / hide rows
        if (tab === 'All') {
            $('#orders-tbody .order-row').show();
        } else {
            $('#orders-tbody .order-row').hide();
            $('#orders-tbody .order-row[data-status="' + tab + '"]').show();
        }
    });

    /* ── Store status toggle ────────────────────────────────────── */
    $('.store-toggle').on('click', function () {
        const status = $(this).data('status');

        // Reset both buttons
        $('.store-toggle').css({ 'background-color': '#fff', 'color': '#9CA3AF' });

        // Highlight selected
        if (status === 'available') {
            $(this).css({ 'background-color': '#22C55E', 'color': '#fff' });
        } else {
            $(this).css({ 'background-color': 'var(--color-primary)', 'color': '#fff' });
        }

        // Optional: POST status to server via AJAX
        // $.post('{{ route("cashier.store.status") }}', { status: status, _token: '{{ csrf_token() }}' });
    });

    /* ── View Detail button ─────────────────────────────────────── */
    $(document).on('click', '.view-detail-btn', function () {
        const orderId = $(this).data('order-id');
        // Placeholder: navigate or open modal
        // window.location.href = '/cashier/orders/' + orderId;
    });

});
</script>

@endsection
