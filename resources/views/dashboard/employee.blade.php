@extends('layouts.app')

@section('title', 'Dashboard — Kasir')

@section('content')

@php
    /* ── Data stubs (controller dapat override semua ini) ────── */
    $revenueToday   = $revenueToday   ?? 800000;
    $revenueGrowth  = $revenueGrowth  ?? 12;      // persen, positif = naik
    $totalOrders    = $totalOrders    ?? 35;
    $onlineOrders   = $onlineOrders   ?? 20;
    $cashierOrders  = $cashierOrders  ?? 15;
    $pendingOrders  = $pendingOrders  ?? 8;
    $storeStatus    = $storeStatus    ?? 'available';

    /* order_type: dine-in | takeaway | online */
    $orders = $orders ?? [
        ['id' => '#C3322', 'is_new' => true,  'customer' => 'Gabriella',        'sub' => 'Customer',  'source' => 'online',  'items' => '3× Mie Mozza',          'status' => 'Pending',    'time' => '11:27 AM', 'total' => 35000, 'order_type' => 'takeaway', 'payment' => 'QRIS'],
        ['id' => '#C3323', 'is_new' => true,  'customer' => 'Olivia',           'sub' => 'Customer',  'source' => 'online',  'items' => '1× Original',           'status' => 'Pending',    'time' => '11:22 AM', 'total' => 16000, 'order_type' => 'online',   'payment' => 'QRIS'],
        ['id' => '#C3349', 'is_new' => false, 'customer' => 'Ricky',            'sub' => 'Customer',  'source' => 'online',  'items' => '1× Squid Nori',         'status' => 'Preparing',  'time' => '10:16 AM', 'total' => 18000, 'order_type' => 'dine-in',  'payment' => 'Cash'],
        ['id' => '#C3340', 'is_new' => false, 'customer' => 'Siti Anggraeni',   'sub' => 'Customer',  'source' => 'cashier', 'items' => '2× Mozza Cheese',       'status' => 'Preparing',  'time' => '11:05 AM', 'total' => 18000, 'order_type' => 'takeaway', 'payment' => 'Debit'],
        ['id' => '#C3341', 'is_new' => false, 'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '2× Original',           'status' => 'Preparing',  'time' => '10:55 AM', 'total' => 24000, 'order_type' => 'dine-in',  'payment' => 'Cash'],
        ['id' => '#C3344', 'is_new' => false, 'customer' => 'Budi',             'sub' => 'Customer',  'source' => 'online',  'items' => '1× Squid Nori',         'status' => 'Ready',      'time' => '10:43 AM', 'total' => 18000, 'order_type' => 'takeaway', 'payment' => 'QRIS'],
        ['id' => '#C3348', 'is_new' => false, 'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '1× Squid Nori',         'status' => 'Ready',      'time' => '10:36 AM', 'total' => 18000, 'order_type' => 'dine-in',  'payment' => 'Cash'],
        ['id' => '#C3350', 'is_new' => false, 'customer' => 'Nadia',            'sub' => 'Customer',  'source' => 'online',  'items' => '3× Mie Mozza',          'status' => 'Completed',  'time' => '09:30 AM', 'total' => 35000, 'order_type' => 'online',   'payment' => 'QRIS'],
        ['id' => '#C3351', 'is_new' => false, 'customer' => 'Nadia',            'sub' => 'Customer',  'source' => 'online',  'items' => '2× Mozza Cheese',       'status' => 'Completed',  'time' => '08:30 AM', 'total' => 18000, 'order_type' => 'online',   'payment' => 'QRIS'],
        ['id' => '#C3352', 'is_new' => false, 'customer' => 'Walk-in Customer', 'sub' => '',          'source' => 'cashier', 'items' => '1× Mozza Cheese',       'status' => 'Completed',  'time' => '08:00 AM', 'total' => 18000, 'order_type' => 'dine-in',  'payment' => 'Cash'],
    ];

    $statusConfig = [
        'Pending'   => ['bg' => 'rgba(156,163,175,0.18)', 'text' => '#6B7280',  'dot' => '#9CA3AF',  'row' => 'rgba(255,253,219,0.6)'],
        'Preparing' => ['bg' => 'rgba(255,158,0,0.15)',   'text' => '#B45309',  'dot' => '#FF9E00',  'row' => 'rgba(255,236,199,0.5)'],
        'Ready'     => ['bg' => 'rgba(34,197,94,0.15)',   'text' => '#15803D',  'dot' => '#22C55E',  'row' => 'rgba(220,252,231,0.5)'],
        'Completed' => ['bg' => 'rgba(99,102,241,0.12)',  'text' => '#4338CA',  'dot' => '#6366F1',  'row' => 'rgba(238,242,255,0.5)'],
        'Cancelled' => ['bg' => 'rgba(239,68,68,0.12)',   'text' => '#B91C1C',  'dot' => '#EF4444',  'row' => 'rgba(254,226,226,0.5)'],
    ];

    $tabCounts = [
        'All'       => count($orders),
        'Pending'   => collect($orders)->where('status', 'Pending')->count(),
        'Preparing' => collect($orders)->where('status', 'Preparing')->count(),
        'Ready'     => collect($orders)->where('status', 'Ready')->count(),
        'Completed' => collect($orders)->where('status', 'Completed')->count(),
        'Cancelled' => collect($orders)->where('status', 'Cancelled')->count(),
    ];

    $avatarPalette = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6','#8B5CF6','#EC4899','#06B6D4'];
@endphp

{{-- ══════════════════════════════════════════════════════════════
     1. PAGE HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">

    {{-- LEFT: logo avatar + title --}}
    <div class="flex items-center gap-3">
        {{-- Avatar toko dengan badge BAR --}}
        <div class="relative flex-none">
            <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center"
                 style="background-color: var(--color-accent);">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku"
                     class="w-10 h-10 object-cover rounded-full">
            </div>
            {{-- Badge BAR merah --}}
            <span class="absolute -bottom-1 -right-1 text-[9px] font-black px-1 rounded leading-tight"
                  style="background-color: var(--color-primary); color: #fff;">BAR</span>
        </div>

        <div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight" style="color: var(--color-black);">
                Welcome to Corndog-Ku!
            </h1>
            <p class="mt-0.5 text-xs" style="color: #888;">
                Jl. Rungkut Mejoyo Utara No.61, Blora
            </p>
        </div>
    </div>

    {{-- RIGHT: Status Store toggle pill --}}
    <div class="flex flex-col items-end gap-1">
        <span class="text-xs font-semibold" style="color: #555;">Status Store</span>
        <div class="flex rounded-full p-0.5 gap-0.5"
             style="background-color: #F3F4F6; border: 1px solid var(--color-border);">
            {{-- Available --}}
            <button id="btn-available" type="button"
                    class="store-toggle inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all"
                    data-status="available"
                    style="{{ $storeStatus === 'available'
                        ? 'background-color: #fff; color: #15803D; box-shadow: 0 1px 4px rgba(0,0,0,0.12);'
                        : 'background-color: transparent; color: #9CA3AF;' }}">
                <span class="w-2 h-2 rounded-full flex-none"
                      style="background-color: {{ $storeStatus === 'available' ? '#22C55E' : '#D1D5DB' }};"></span>
                Available
            </button>
            {{-- Unavailable --}}
            <button id="btn-unavailable" type="button"
                    class="store-toggle inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all"
                    data-status="unavailable"
                    style="{{ $storeStatus === 'unavailable'
                        ? 'background-color: #fff; color: var(--color-primary); box-shadow: 0 1px 4px rgba(0,0,0,0.12);'
                        : 'background-color: transparent; color: #9CA3AF;' }}">
                <span class="w-2 h-2 rounded-full flex-none"
                      style="background-color: {{ $storeStatus === 'unavailable' ? 'var(--color-primary)' : '#D1D5DB' }};"></span>
                Unavailable
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     2. SUMMARY CARDS + CALENDAR
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Revenue Today --}}
    <div class="rounded-xl p-5 flex flex-col gap-2"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold" style="color: #555;">Revenue Today</p>
            {{-- Icon: simbol dolar merah, kotak bersudut tumpul --}}
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                 style="background-color: var(--color-primary-surface);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="var(--color-primary)" stroke-width="2">
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
        {{-- Badge indikator pertumbuhan --}}
        @if ($revenueGrowth >= 0)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full self-start"
                  style="background-color: #DCFCE7; color: #15803D;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                </svg>
                +{{ $revenueGrowth }}% from yesterday
            </span>
        @else
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full self-start"
                  style="background-color: #FEE2E2; color: #B91C1C;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
                {{ $revenueGrowth }}% from yesterday
            </span>
        @endif
    </div>

    {{-- Total Order Today --}}
    <div class="rounded-xl p-5 flex flex-col gap-2"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold" style="color: #555;">Total order Today</p>
            {{-- Icon: kotak paket oranye --}}
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                 style="background-color: #FFF7ED;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="#F97316" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold" style="color: var(--color-black);">{{ $totalOrders }} orders</p>
        <p class="text-[11px]">
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
            {{-- Icon: jam kuning --}}
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                 style="background-color: #FEF3C7;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="#D97706" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold" style="color: var(--color-black);">{{ $pendingOrders }} orders</p>
        <span class="text-[11px] font-semibold" style="color: var(--color-primary);">Perlu diproses</span>
    </div>

    {{-- Mini Calendar --}}
    <div class="rounded-xl p-4 sm:col-span-2 lg:col-span-1"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="flex items-center justify-between mb-2">
            <p class="font-bold text-sm" style="color: var(--color-black);">{{ now()->format('F Y') }}</p>
            <div class="flex gap-1">
                <button class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold"
                        style="background-color: #EAEDF1; color: #555;">&lsaquo;</button>
                <button class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold"
                        style="background-color: #EAEDF1; color: #555;">&rsaquo;</button>
            </div>
        </div>
        <div class="grid grid-cols-7 text-center mb-1">
            @foreach (['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
                <span class="text-[10px] font-semibold" style="color: rgba(60,60,67,0.4);">{{ $d }}</span>
            @endforeach
        </div>
        <div class="grid grid-cols-7 text-center gap-y-1">
            @php $startBlank = now()->startOfMonth()->dayOfWeek; @endphp
            @for ($b = 0; $b < $startBlank; $b++)<span></span>@endfor
            @for ($d = 1; $d <= now()->daysInMonth; $d++)
                @if ($d === now()->day)
                    <span class="w-6 h-6 mx-auto flex items-center justify-center rounded-full text-[11px] font-bold text-white"
                          style="background-color: var(--color-primary);">{{ $d }}</span>
                @else
                    <span class="text-[11px]" style="color: var(--color-black);">{{ $d }}</span>
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

    {{-- ── Red header bar with corndog illustrations ─────────── --}}
    <div class="relative flex flex-wrap items-center gap-2 px-4 py-3 overflow-hidden"
         style="background-color: var(--color-primary); min-height: 56px;">

        {{-- Corndog illustrations (kanan) — dua corndog peeking from top --}}
        <div class="absolute right-4 top-0 flex items-end gap-2 pointer-events-none select-none"
             aria-hidden="true">
            <img src="{{ asset('assets/img/CB_02.png') }}" alt=""
                 class="h-14 w-auto object-contain opacity-90"
                 style="transform: rotate(-8deg) translateY(4px);">
            <img src="{{ asset('assets/img/CB_01.png') }}" alt=""
                 class="h-14 w-auto object-contain opacity-90"
                 style="transform: rotate(6deg) translateY(2px);">
        </div>

        {{-- Title + tabs --}}
        <span class="font-bold text-white text-sm mr-2 z-10">Active Orders</span>

        @php
            $tabs = ['All', 'Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
        @endphp
        @foreach ($tabs as $tab)
            <button type="button"
                    class="order-tab z-10 px-2.5 py-1 rounded-full text-xs font-bold transition-all whitespace-nowrap"
                    data-tab="{{ $tab }}"
                    style="{{ $tab === 'All'
                        ? 'background-color: #fff; color: var(--color-primary);'
                        : 'background-color: rgba(255,255,255,0.18); color: rgba(255,255,255,0.9);' }}">
                {{ $tab }}
                <span class="ml-0.5 opacity-80">({{ $tabCounts[$tab] ?? 0 }})</span>
            </button>
        @endforeach
    </div>

    {{-- ── Table ─────────────────────────────────────────────── --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background-color: #FAFAFA; border-bottom: 2px solid var(--color-border);">
                    @foreach (['Order ID', 'Customer', 'Source', 'Items', 'Status', 'Time', 'Total', 'Action'] as $col)
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide whitespace-nowrap"
                            style="color: #9CA3AF;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="orders-tbody">
                @foreach ($orders as $order)
                    @php
                        $cfg      = $statusConfig[$order['status']] ?? $statusConfig['Pending'];
                        $initial  = strtoupper(mb_substr($order['customer'], 0, 1));
                        $avatarBg = $avatarPalette[abs(crc32($order['customer'])) % count($avatarPalette)];
                    @endphp
                    <tr class="order-row transition-colors"
                        data-status="{{ $order['status'] }}"
                        data-order="{{ json_encode($order) }}"
                        style="background-color: {{ $cfg['row'] }}; border-bottom: 1px solid var(--color-border);">

                        {{-- Order ID + NEW badge --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="font-mono font-bold text-xs" style="color: var(--color-black);">
                                {{ $order['id'] }}
                            </p>
                            @if (!empty($order['is_new']))
                                <span class="text-[9px] font-black px-1 py-0.5 rounded leading-none"
                                      style="background-color: var(--color-primary); color: #fff;">NEW</span>
                            @endif
                        </td>

                        {{-- Customer — initial avatar saja (no img) --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex-none flex items-center justify-center
                                            text-white text-[11px] font-bold"
                                     style="background-color: {{ $avatarBg }};">
                                    {{ $initial }}
                                </div>
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

                        {{-- Source — pill badge dengan icon --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($order['source'] === 'online')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                      style="background-color: var(--color-primary-surface); color: var(--color-primary);">
                                    {{-- Wi-Fi icon merah --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01
                                                 m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0
                                                 M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                    </svg>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                      style="background-color: #FFF7ED; color: #F97316;">
                                    {{-- Person icon oranye --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Cashier
                                </span>
                            @endif
                        </td>

                        {{-- Items --}}
                        <td class="px-4 py-3 text-xs max-w-[130px] truncate"
                            style="color: #555;">{{ $order['items'] }}</td>

                        {{-- Status — dot + label --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                                  style="background-color: {{ $cfg['bg'] }}; color: {{ $cfg['text'] }};">
                                <span class="w-1.5 h-1.5 rounded-full flex-none"
                                      style="background-color: {{ $cfg['dot'] }};"></span>
                                {{ $order['status'] }}
                            </span>
                        </td>

                        {{-- Time --}}
                        <td class="px-4 py-3 text-xs whitespace-nowrap"
                            style="color: #6B7280;">{{ $order['time'] }}</td>

                        {{-- Total --}}
                        <td class="px-4 py-3 text-xs font-bold whitespace-nowrap"
                            style="color: var(--color-black);">
                            Rp {{ number_format($order['total'], 0, ',', '.') }}
                        </td>

                        {{-- Action — outline button dengan icon mata --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button type="button"
                                    class="view-detail-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg
                                           text-[11px] font-semibold border transition-all hover:opacity-80"
                                    data-order="{{ json_encode($order) }}"
                                    style="border-color: var(--color-primary);
                                           color: var(--color-primary);
                                           background-color: transparent;">
                                {{-- Eye icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                             9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Table footer: View all orders link --}}
    <div class="flex justify-center py-4"
         style="border-top: 1px solid var(--color-border);">
        <a href="{{ route('cashier.purchase') }}"
           class="inline-flex items-center gap-1 text-sm font-semibold hover:underline"
           style="color: var(--color-primary);">
            View all orders
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     Order Detail Drawer Component
══════════════════════════════════════════════════════════════ --}}
@include('components.order-detail-drawer')

{{-- ══════════════════════════════════════════════════════════════
     jQuery — Tab filter + Store toggle + Drawer open/close
══════════════════════════════════════════════════════════════ --}}
<script>
$(function () {

    /* ── 1. ORDER FILTER TABS ─────────────────────────────────── */
    const tabActiveStyles = {
        'All':       { bg: '#fff',     text: 'var(--color-primary)' },
        'Pending':   { bg: '#9CA3AF',  text: '#fff' },
        'Preparing': { bg: '#FF9E00',  text: '#fff' },
        'Ready':     { bg: '#22C55E',  text: '#fff' },
        'Completed': { bg: '#6366F1',  text: '#fff' },
        'Cancelled': { bg: '#EF4444',  text: '#fff' },
    };

    $('.order-tab').on('click', function () {
        const tab = $(this).data('tab');

        /* reset semua tab */
        $('.order-tab').css({
            'background-color': 'rgba(255,255,255,0.18)',
            'color': 'rgba(255,255,255,0.9)'
        });

        /* aktifkan tab yang diklik */
        const s = tabActiveStyles[tab] || tabActiveStyles['All'];
        $(this).css({ 'background-color': s.bg, 'color': s.text });

        /* filter baris tabel */
        if (tab === 'All') {
            $('#orders-tbody .order-row').show();
        } else {
            $('#orders-tbody .order-row').hide();
            $('#orders-tbody .order-row[data-status="' + tab + '"]').show();
        }
    });


    /* ── 2. STORE STATUS TOGGLE ────────────────────────────────── */
    $('.store-toggle').on('click', function () {
        const status = $(this).data('status');

        /* reset kedua tombol */
        $('.store-toggle').css({
            'background-color': 'transparent',
            'color': '#9CA3AF',
            'box-shadow': 'none'
        });
        $('.store-toggle .w-2').css('background-color', '#D1D5DB');

        /* aktifkan tombol terpilih */
        $(this).css({
            'background-color': '#fff',
            'box-shadow': '0 1px 4px rgba(0,0,0,0.12)'
        });

        if (status === 'available') {
            $(this).css('color', '#15803D');
            $(this).find('.w-2').css('background-color', '#22C55E');
        } else {
            $(this).css('color', 'var(--color-primary)');
            $(this).find('.w-2').css('background-color', 'var(--color-primary)');
        }

        $.post('{{ route('cashier.store.status') }}', {
            status: status,
            _token: '{{ csrf_token() }}'
        }).fail(function () {
            console.warn('Gagal menyimpan status toko.');
        });
    });


    /* ── 3. ORDER DETAIL DRAWER ────────────────────────────────── */

    const stepOrder    = ['Pending', 'Preparing', 'Ready', 'Completed'];
    const typeMap      = { 'takeaway': 'Take Away', 'dine-in': 'Dine In', 'online': 'Online Order' };
    const payMap       = { 'QRIS': 'QRIS', 'Cash': 'Cash', 'Debit': 'Debit Card' };
    /* persentase fill line per step (index 0–3) */
    const stepFillPct  = ['0%', '33.33%', '66.66%', '100%'];
    const stepFillColor = { 0: '#FF9E00', 1: '#FF9E00', 2: '#22C55E', 3: '#6366F1' };

    function openDrawer(order) {
        /* ── Isi field teks ── */
        const name     = order.customer || 'Customer';
        const srcLabel = order.source === 'online' ? 'Online Order' : 'Kasir';

        $('#drawer-order-id').text(order.id);
        $('#drawer-customer-name').text(name);
        $('#drawer-order-type').text(typeMap[order.order_type] || order.order_type || '-');
        $('#drawer-payment').text(payMap[order.payment] || order.payment || '-');
        $('#drawer-source').text(srcLabel);
        $('#drawer-time').text(order.time || '-');
        $('#drawer-date').text(new Date().toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }));
        $('#drawer-total').text('Rp ' + Number(order.total).toLocaleString('id-ID'));
        $('#drawer-item-count').text('x1'); /* placeholder; ganti dengan qty real */

        /* badge NEW: tampilkan hanya jika pesanan baru */
        if (order.is_new) {
            $('#drawer-new-badge').show();
        } else {
            $('#drawer-new-badge').hide();
        }

        /* ── Update horizontal stepper ── */
        const currentIdx = stepOrder.indexOf(order.status);
        const fillColor  = stepFillColor[currentIdx] || '#E5E7EB';

        /* fill line panjangnya sesuai progress */
        $('#stepper-fill').css({
            'width': stepFillPct[currentIdx] || '0%',
            'background-color': fillColor
        });

        $('#drawer-stepper .step-item').each(function (i) {
            const $circle = $(this).find('.step-circle');
            const $badge  = $(this).find('.step-badge');
            const aColor  = $circle.data('active-color');
            const aBg     = $badge.data('active-bg');
            const aBorder = $badge.data('active-border');

            if (i <= currentIdx) {
                /* langkah yang sudah dilalui atau aktif saat ini */
                $circle.css({ 'border-color': aColor, 'color': aColor, 'background-color': '#fff' });
                $badge.css({ 'border-color': aBorder, 'color': aColor, 'background-color': aBg });
            } else {
                /* langkah mendatang — abu-abu */
                $circle.css({ 'border-color': '#E5E7EB', 'color': '#D1D5DB', 'background-color': '#F9FAFB' });
                $badge.css({ 'border-color': '#E5E7EB', 'color': '#D1D5DB', 'background-color': '#F9FAFB' });
            }
        });

        /* ── Buka panel: hapus translate-x-full ── */
        $('#drawer-backdrop').removeClass('hidden');
        $('#order-detail-drawer').removeClass('translate-x-full');
    }

    function closeDrawer() {
        /* tutup panel: tambahkan kembali translate-x-full ── */
        $('#order-detail-drawer').addClass('translate-x-full');
        $('#drawer-backdrop').addClass('hidden');
    }

    /* Buka saat "View Detail" diklik */
    $(document).on('click', '.view-detail-btn', function () {
        let order = {};
        try { order = JSON.parse($(this).attr('data-order') || '{}'); } catch (e) {}
        openDrawer(order);
    });

    /* Tutup lewat tombol X di drawer */
    $(document).on('click', '#close-drawer-btn', closeDrawer);

    /* Tutup lewat klik backdrop */
    $('#drawer-backdrop').on('click', closeDrawer);

    /* Tutup dengan tombol Escape */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });

});
</script>

@endsection
