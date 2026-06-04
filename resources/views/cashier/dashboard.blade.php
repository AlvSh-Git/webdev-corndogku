@extends('layouts.app')

@section('title', 'Dashboard — Kasir')

@section('content')

@php
    /* ── Fallback stubs — only used when DB is completely unavailable ── */
    $selectedDate  = $selectedDate  ?? today()->toDateString();
    $revenueToday  = $revenueToday  ?? 0;
    $revenueGrowth = $revenueGrowth ?? 0;
    $totalOrders   = $totalOrders   ?? 0;
    $onlineOrders  = $onlineOrders  ?? 0;
    $cashierOrders = $cashierOrders ?? 0;
    $pendingOrders = $pendingOrders ?? 0;
    $storeStatus   = $storeStatus   ?? 'available';

    $chartData = $chartData ?? [
        ['label'=>'Mon','value'=>200000],
        ['label'=>'Tue','value'=>590000],
        ['label'=>'Wed','value'=>550000],
        ['label'=>'Thu','value'=>650000],
        ['label'=>'Fri','value'=>400000],
        ['label'=>'Sat','value'=>500000],
        ['label'=>'Sun','value'=>0],
    ];

    $orders = $orders ?? array_map(fn($o) => (object) $o, [
        ['db_id'=>null,'id'=>'#C3322','is_new'=>true,  'customer'=>'Gabriella',        'sub'=>'Customer', 'source'=>'online',  'items'=>'2× Mix Mozzarella',  'status'=>'Pending',   'time'=>'11:27 AM','total'=>35200, 'order_type'=>'takeaway','payment'=>'QRIS',
         'order_items'=>[['name'=>'Mix Mozzarella','variant'=>'Sosis + Mozza','price'=>15000,'qty'=>2,'subtotal'=>30000,'img'=>'CA_MOZZA.png'],['name'=>'Original Corndog','variant'=>'Sosis + Batter','price'=>5200,'qty'=>1,'subtotal'=>5200,'img'=>'CA_ORIGINAL.png']]],
        ['db_id'=>null,'id'=>'#C3323','is_new'=>true,  'customer'=>'Olivia',           'sub'=>'Customer', 'source'=>'online',  'items'=>'1× Original',        'status'=>'Pending',   'time'=>'11:22 AM','total'=>16000, 'order_type'=>'online',  'payment'=>'QRIS',
         'order_items'=>[['name'=>'Original Corndog','variant'=>'Sosis + Batter','price'=>16000,'qty'=>1,'subtotal'=>16000,'img'=>'CA_ORIGINAL.png']]],
        ['db_id'=>null,'id'=>'#C3349','is_new'=>false, 'customer'=>'Ricky',            'sub'=>'Customer', 'source'=>'online',  'items'=>'1× Squid Nori',     'status'=>'Preparing', 'time'=>'10:16 AM','total'=>18000, 'order_type'=>'dine-in', 'payment'=>'Cash',
         'order_items'=>[['name'=>'Squid Nori Corndog','variant'=>'Squid Ink + Nori','price'=>18000,'qty'=>1,'subtotal'=>18000,'img'=>'CA_SQUID_NORI.png']]],
        ['db_id'=>null,'id'=>'#C3340','is_new'=>false, 'customer'=>'Siti Anggraeni',   'sub'=>'Customer', 'source'=>'cashier', 'items'=>'2× Mozza Cheese',   'status'=>'Preparing', 'time'=>'11:05 AM','total'=>24000, 'order_type'=>'takeaway','payment'=>'Debit',
         'order_items'=>[['name'=>'Mozza Cheese','variant'=>'Sosis + Mozza Keju','price'=>12000,'qty'=>2,'subtotal'=>24000,'img'=>'CA_MOZZA.png']]],
        ['db_id'=>null,'id'=>'#C3344','is_new'=>false, 'customer'=>'Budi',             'sub'=>'Customer', 'source'=>'online',  'items'=>'1× Squid Nori',     'status'=>'Ready',     'time'=>'10:43 AM','total'=>18000, 'order_type'=>'takeaway','payment'=>'QRIS',
         'order_items'=>[['name'=>'Squid Nori Corndog','variant'=>'Squid Ink + Nori','price'=>18000,'qty'=>1,'subtotal'=>18000,'img'=>'CA_SQUID_NORI.png']]],
        ['db_id'=>null,'id'=>'#C3350','is_new'=>false, 'customer'=>'Nadia',            'sub'=>'Customer', 'source'=>'online',  'items'=>'3× Mie Mozza',      'status'=>'Completed', 'time'=>'09:30 AM','total'=>45000, 'order_type'=>'online',  'payment'=>'QRIS',
         'order_items'=>[['name'=>'Mix Mozzarella','variant'=>'Sosis + Mozza','price'=>15000,'qty'=>3,'subtotal'=>45000,'img'=>'CA_MOZZA.png']]],
    ]);

    $ordersItems = ($orders instanceof \Illuminate\Contracts\Pagination\Paginator)
        ? collect($orders->items()) : collect($orders);

    $statusConfig = [
        'Pending'   => ['bg'=>'rgba(245,158,11,0.12)','text'=>'#B45309','dot'=>'#F59E0B','row'=>'rgba(255,251,235,0.7)'],
        'Preparing' => ['bg'=>'rgba(249,115,22,0.12)','text'=>'#C2410C','dot'=>'#F97316','row'=>'rgba(255,247,237,0.7)'],
        'Ready'     => ['bg'=>'rgba(34,197,94,0.12)', 'text'=>'#15803D','dot'=>'#22C55E','row'=>'rgba(240,253,244,0.7)'],
        'Completed' => ['bg'=>'rgba(99,102,241,0.12)','text'=>'#4338CA','dot'=>'#6366F1','row'=>'rgba(238,242,255,0.7)'],
        'Cancelled' => ['bg'=>'rgba(239,68,68,0.12)', 'text'=>'#B91C1C','dot'=>'#EF4444','row'=>'rgba(254,226,226,0.7)'],
    ];

    $tabCounts = [
        'All'       => $ordersItems->count(),
        'Pending'   => $ordersItems->where('status','Pending')->count(),
        'Preparing' => $ordersItems->where('status','Preparing')->count(),
        'Ready'     => $ordersItems->where('status','Ready')->count(),
        'Completed' => $ordersItems->where('status','Completed')->count(),
        'Cancelled' => $ordersItems->where('status','Cancelled')->count(),
    ];

    $avatarPalette = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6','#8B5CF6','#EC4899','#06B6D4'];

    /*
     * Calendar — always anchors the 7-day window at today as the right edge.
     * $selectedDate is highlighted inside that window (or defaults to today).
     */
    $todayBase  = today();
    $windowDays = collect(range(6, 0, -1))->map(fn($i) => $todayBase->copy()->subDays($i));
    $winStartPHP = $windowDays->first()->toDateString();
    $winEndPHP   = $windowDays->last()->toDateString();   /* = today */

    $selCal     = \Carbon\Carbon::parse($selectedDate)->min($todayBase);
    $displaySel = $selCal->toDateString();
    if ($displaySel < $winStartPHP) { $displaySel = $winEndPHP; }

    $canGoNext = $selCal->lt($todayBase);
    $dayNames  = ['Su','Mo','Tu','We','Th','Fr','Sa'];
@endphp

<style>
@media (min-width: 1280px) {
    #dash-grid     { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 24px; }
    #dash-stats    { grid-column: 1 / span 8;  grid-row: 1; }
    #dash-calendar { grid-column: 9 / span 4;  grid-row: 1; }
    #dash-chart    { grid-column: 1 / span 8;  grid-row: 2; }
    #dash-status   { grid-column: 9 / span 4;  grid-row: 2; }
    #dash-orders   { grid-column: 1 / span 8;  grid-row: 3; }
}
</style>

{{-- ══════════════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">

    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center flex-none"
             style="background-color:var(--color-accent);">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku"
                 class="w-10 h-10 object-cover rounded-full">
        </div>
        <div>
            <h1 class="text-xl md:text-2xl font-bold leading-tight" style="color:var(--color-black);">
                Welcome to Corndog-Ku!
            </h1>
            <p class="mt-0.5 text-xs" style="color:#888;">{{ config('store.address') }}</p>
        </div>
    </div>

    {{-- Read-only store status indicator --}}
    <div class="flex flex-col items-end gap-1">
        <span class="text-xs font-semibold" style="color:#555;">Status Store</span>
        <div class="flex rounded-full p-0.5 gap-0.5 pointer-events-none select-none"
             style="background-color:#F3F4F6;border:1px solid var(--color-border);">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                  style="{{ $storeStatus === 'available'
                      ? 'background-color:#fff;color:#15803D;box-shadow:0 1px 4px rgba(0,0,0,0.12);'
                      : 'background-color:transparent;color:#9CA3AF;' }}">
                <span class="w-2 h-2 rounded-full flex-none"
                      style="background-color:{{ $storeStatus === 'available' ? '#22C55E' : '#D1D5DB' }};"></span>
                Available
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                  style="{{ $storeStatus === 'unavailable'
                      ? 'background-color:#fff;color:var(--color-primary);box-shadow:0 1px 4px rgba(0,0,0,0.12);'
                      : 'background-color:transparent;color:#9CA3AF;' }}">
                <span class="w-2 h-2 rounded-full flex-none"
                      style="background-color:{{ $storeStatus === 'unavailable' ? 'var(--color-primary)' : '#D1D5DB' }};"></span>
                Unavailable
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MAIN GRID — 12-col on desktop, 1-col on mobile
     Row 1: Stats(8) + Calendar(4)
     Row 2: Orders(8) + Status(4)
══════════════════════════════════════════════════════════════ --}}
<div id="dash-grid" class="flex flex-col gap-6 w-full">

    {{-- ── CALENDAR ──────────────────────────────────────────────────── --}}
    <div id="dash-calendar" class="rounded-xl p-4"
         style="background-color:var(--color-white);box-shadow:var(--shadow-card);">

        <div class="flex items-center justify-between mb-3">
            <p id="cal-month" class="font-bold text-sm" style="color:var(--color-black);">
                {{ $windowDays->last()->translatedFormat('F Y') }}
            </p>
            <div class="flex gap-1">
                <button type="button" id="cal-prev"
                        class="w-9 h-9 rounded flex items-center justify-center text-sm font-bold
                               transition-colors hover:opacity-80"
                        style="background-color:#EAEDF1;color:#555;"
                        title="Hari sebelumnya">&lsaquo;</button>
                <button type="button" id="cal-next"
                        class="w-9 h-9 rounded flex items-center justify-center text-sm font-bold"
                        style="{{ $canGoNext
                            ? 'background-color:#EAEDF1;color:#555;cursor:pointer;'
                            : 'background-color:#F3F4F6;color:#D1D5DB;cursor:not-allowed;' }}"
                        {{ $canGoNext ? '' : 'disabled' }}
                        title="Hari berikutnya">&rsaquo;</button>
            </div>
        </div>

        <div id="cal-daynames" class="grid grid-cols-7 text-center mb-1">
            @foreach ($windowDays as $wd)
                <span class="text-[9px] font-semibold" style="color:rgba(60,60,67,0.4);">
                    {{ $dayNames[$wd->dayOfWeek] }}
                </span>
            @endforeach
        </div>

        <div id="cal-dates" class="grid grid-cols-7 text-center">
            @foreach ($windowDays as $wd)
                @php
                    $wdStr   = $wd->toDateString();
                    $isSel   = $wdStr === $displaySel;
                    $isToday = $wdStr === $winEndPHP;
                @endphp
                <button type="button" class="cal-date-btn"
                        data-date="{{ $wdStr }}"
                        style="width:36px;height:36px;display:flex;align-items:center;
                               justify-content:center;border-radius:50%;font-size:12px;
                               margin:auto;border:none;cursor:pointer;
                               {{ $isSel
                                   ? 'background-color:var(--color-primary);color:#fff;font-weight:700;'
                                   : ($isToday
                                       ? 'background-color:transparent;color:var(--color-primary);font-weight:700;'
                                       : 'background-color:transparent;color:var(--color-black);font-weight:400;') }}">
                    {{ $wd->day }}
                </button>
            @endforeach
        </div>

        <p id="cal-selected-label" class="text-center text-[10px] mt-2 font-semibold"
           style="color:#9CA3AF;">
            Data: {{ $selCal->translatedFormat('d F Y') }}
        </p>
    </div>

    {{-- ── TOP STATS WRAPPER ─────────────────────────────────────────── --}}
    <div id="dash-stats">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Revenue Today --}}
            <div class="rounded-xl p-5 flex flex-col gap-2 h-full w-full"
                 style="background-color:var(--color-white);box-shadow:var(--shadow-card);">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold" style="color:#555;">Revenue Today</p>
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                         style="background-color:var(--color-primary-surface);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="var(--color-primary)" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2
                                     m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1
                                     m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="widget-revenue" class="text-2xl font-bold" style="color:var(--color-black);">
                    Rp {{ number_format($revenueToday, 0, ',', '.') }}
                </p>
                <div id="stat-revenue-growth">
                    @if ($revenueGrowth >= 0)
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold
                                     px-2 py-0.5 rounded-full"
                              style="background-color:#DCFCE7;color:#15803D;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                            </svg>
                            +{{ $revenueGrowth }}% dari kemarin
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold
                                     px-2 py-0.5 rounded-full"
                              style="background-color:#FEE2E2;color:#B91C1C;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                            {{ $revenueGrowth }}% dari kemarin
                        </span>
                    @endif
                </div>
            </div>

            {{-- Total Orders --}}
            <div class="rounded-xl p-5 flex flex-col gap-2 h-full w-full"
                 style="background-color:var(--color-white);box-shadow:var(--shadow-card);">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold" style="color:#555;">Total Orders</p>
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                         style="background-color:#FFF7ED;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="#F97316" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <p id="widget-total-orders" class="text-2xl font-bold" style="color:var(--color-black);">
                    {{ $totalOrders }} orders
                </p>
                <p id="stat-orders-detail" class="text-[11px]">
                    <span style="font-weight:700;color:var(--color-primary);">Online:</span>
                    <span style="color:#555;"> {{ $onlineOrders }}</span>
                    <span style="color:#CBD5E1;">&nbsp;|&nbsp;</span>
                    <span style="font-weight:700;color:#FF9E00;">Cashier:</span>
                    <span style="color:#555;"> {{ $cashierOrders }}</span>
                </p>
            </div>

            {{-- Pending Orders --}}
            <div class="rounded-xl p-5 flex flex-col gap-2 h-full w-full"
                 style="background-color:var(--color-white);box-shadow:var(--shadow-card);">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold" style="color:#555;">Pending Orders</p>
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-none"
                         style="background-color:#FEF3C7;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="#D97706" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="widget-pending-orders" class="text-2xl font-bold" style="color:var(--color-black);">
                    {{ $pendingOrders }} orders
                </p>
                <span class="text-[11px] font-semibold" style="color:var(--color-primary);">
                    Perlu diproses
                </span>
            </div>

        </div>
    </div>{{-- /stats wrapper --}}

    {{-- ── REVENUE CHART ─────────────────────────────────────────────── --}}
    <div id="dash-chart" class="rounded-xl p-5"
         style="background-color:var(--color-white);box-shadow:var(--shadow-card);">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold" style="color:var(--color-black);">Revenue</h2>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold
                         px-2 py-1 rounded-lg flex-none"
                  style="background-color:var(--color-primary-surface);color:var(--color-primary);">
                <span class="w-2 h-2 rounded-full" style="background-color:var(--color-primary);"></span>
                Revenue
            </span>
        </div>
        <div style="position:relative;height:180px;">
            <canvas id="revenue-chart"></canvas>
        </div>
    </div>

    {{-- ── ORDER STATUS SUMMARY ──────────────────────────────────────── --}}
    <div id="dash-status" class="rounded-xl p-4 flex flex-col gap-3"
         style="background-color:var(--color-white);box-shadow:var(--shadow-card);">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold" style="color:#555;">Order Status</p>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                  style="background-color:var(--color-primary-surface);color:var(--color-primary);">Today</span>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div class="rounded-xl p-3 flex flex-col gap-1" style="background-color:rgba(245,158,11,0.10);">
                <span class="text-[11px] font-semibold" style="color:#B45309;">Pending</span>
                <span id="status-pending" class="text-2xl font-black" style="color:#F59E0B;">{{ $tabCounts['Pending'] ?? 0 }}</span>
            </div>
            <div class="rounded-xl p-3 flex flex-col gap-1" style="background-color:rgba(249,115,22,0.10);">
                <span class="text-[11px] font-semibold" style="color:#C2410C;">Preparing</span>
                <span id="status-preparing" class="text-2xl font-black" style="color:#F97316;">{{ $tabCounts['Preparing'] ?? 0 }}</span>
            </div>
            <div class="rounded-xl p-3 flex flex-col gap-1" style="background-color:rgba(34,197,94,0.10);">
                <span class="text-[11px] font-semibold" style="color:#15803D;">Ready</span>
                <span id="status-ready" class="text-2xl font-black" style="color:#22C55E;">{{ $tabCounts['Ready'] ?? 0 }}</span>
            </div>
            <div class="rounded-xl p-3 flex flex-col gap-1" style="background-color:rgba(99,102,241,0.10);">
                <span class="text-[11px] font-semibold" style="color:#4338CA;">Completed</span>
                <span id="status-completed" class="text-2xl font-black" style="color:#6366F1;">{{ $tabCounts['Completed'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- ── ACTIVE ORDERS TABLE ───────────────────────────────────────── --}}
    <div id="dash-orders" class="rounded-xl overflow-hidden"
         style="background-color:var(--color-white);box-shadow:var(--shadow-card);">

        <div class="relative flex flex-wrap items-center gap-2 px-4 py-3 overflow-hidden"
             style="background-color:var(--color-primary);min-height:56px;">
            <div class="absolute right-4 top-0 flex items-end gap-2 pointer-events-none select-none"
                 aria-hidden="true">
                <img src="{{ asset('assets/img/CB_02.png') }}" alt=""
                     class="h-14 w-auto object-contain opacity-90"
                     style="transform:rotate(-8deg) translateY(4px);">
                <img src="{{ asset('assets/img/CB_01.png') }}" alt=""
                     class="h-14 w-auto object-contain opacity-90"
                     style="transform:rotate(6deg) translateY(2px);">
            </div>
            <span class="font-bold text-white text-sm mr-2 z-10">Active Orders</span>
            @foreach (['All','Pending','Preparing','Ready','Completed','Cancelled'] as $tab)
                <button type="button"
                        class="order-tab z-10 px-2.5 py-1 rounded-full text-xs font-bold
                               transition-all whitespace-nowrap"
                        data-tab="{{ $tab }}"
                        data-status="{{ $tab === 'All' ? 'all' : $tab }}"
                        style="{{ $tab === 'All'
                            ? 'background-color:#fff;color:var(--color-primary);'
                            : 'background-color:rgba(255,255,255,0.18);color:rgba(255,255,255,0.9);' }}">
                    {{ $tab }}
                    <span class="tab-count ml-0.5 opacity-80">({{ $tabCounts[$tab] ?? 0 }})</span>
                </button>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color:#FAFAFA;border-bottom:2px solid var(--color-border);">
                        @foreach (['Order ID','Customer','Source','Items','Status','Time','Total','Action'] as $col)
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase
                                       tracking-wide whitespace-nowrap"
                                style="color:#9CA3AF;">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    @foreach ($ordersItems as $order)
                        @php
                            $cfg       = $statusConfig[$order->status] ?? $statusConfig['Pending'];
                            $initial   = strtoupper(mb_substr($order->customer, 0, 1));
                            $avatarBg  = $avatarPalette[abs(crc32($order->customer)) % count($avatarPalette)];
                            $orderJson = htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8');
                        @endphp
                        <tr class="order-row transition-colors"
                            data-status="{{ $order->status }}"
                            data-order="{{ $orderJson }}"
                            style="background-color:{{ $cfg['row'] }};border-bottom:1px solid var(--color-border);">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-mono font-bold text-xs" style="color:var(--color-black);">{{ $order->id }}</p>
                                @if (!empty($order->is_new))
                                    <span class="text-[9px] font-black px-1 py-0.5 rounded leading-none"
                                          style="background-color:var(--color-primary);color:#fff;">NEW</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex-none flex items-center
                                                justify-content:center text-white text-[11px] font-bold"
                                         style="background-color:{{ $avatarBg }};">{{ $initial }}</div>
                                    <div>
                                        <p class="font-semibold text-xs" style="color:var(--color-black);">{{ $order->customer }}</p>
                                        @if (!empty($order->sub))
                                            <p class="text-[10px]" style="color:#9CA3AF;">{{ $order->sub }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($order->source === 'online')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                          style="background-color:var(--color-primary-surface);color:var(--color-primary);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                          style="background-color:#FFF7ED;color:#F97316;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>Cashier
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs max-w-[130px] truncate" style="color:#555;">{{ $order->items }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                                      style="background-color:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full flex-none" style="background-color:{{ $cfg['dot'] }};"></span>
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap" style="color:#6B7280;">{{ $order->time }}</td>
                            <td class="px-4 py-3 text-xs font-bold whitespace-nowrap" style="color:var(--color-black);">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button type="button"
                                        class="view-detail-btn inline-flex items-center gap-1
                                               px-3 py-1.5 rounded-lg text-[11px] font-semibold
                                               border transition-all hover:opacity-80"
                                        data-order="{{ $orderJson }}"
                                        style="border-color:var(--color-primary);color:var(--color-primary);background:transparent;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="cashier-pagination"
             class="flex flex-wrap justify-center items-center gap-2 p-4"
             style="border-top:1px solid var(--color-border);min-height:56px;"></div>
    </div>

</div>{{-- /main grid --}}

@include('components.order-detail-drawer')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(function () {
    const CSRF = '{{ csrf_token() }}';
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF } });

    /* ═══════════════════════════════════════════════════════════
       SHARED STATE
    ═══════════════════════════════════════════════════════════ */
    let SELECTED_DATE = '{{ $displaySel }}';
    let currentStatus = 'all';
    let currentPage   = 1;

    let winStart = '{{ $winStartPHP }}';
    let winEnd   = '{{ $winEndPHP }}';
    let lastTotalOrders = -1;

    /* ═══════════════════════════════════════════════════════════
       AUDIO — Web Audio API (no external file, no CORS issues)
       The AudioContext must be created/resumed after a user gesture.
       We unlock it on the very first interaction so the beep is
       ready before the first 15-second poll fires.
    ═══════════════════════════════════════════════════════════ */
    let audioCtx = null;

    function unlockAudio() {
        if (audioCtx) return;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
        } catch (e) {}
    }
    $(document).one('click keydown touchstart', unlockAudio);

    function playNotificationBeep() {
        if (!audioCtx) return;
        if (audioCtx.state === 'suspended') audioCtx.resume();
        try {
            const osc  = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.value = 880;
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.6);
        } catch (e) {}
    }

    /* ═══════════════════════════════════════════════════════════
       DATE UTILITIES
    ═══════════════════════════════════════════════════════════ */
    function toISO(d) {
        return d.getFullYear()
            + '-' + String(d.getMonth() + 1).padStart(2, '0')
            + '-' + String(d.getDate()).padStart(2, '0');
    }
    function shiftDate(iso, n) {
        const d = new Date(iso + 'T00:00:00');
        d.setDate(d.getDate() + n);
        return toISO(d);
    }
    function todayISO() { return toISO(new Date()); }

    /* ═══════════════════════════════════════════════════════════
       CALENDAR — AJAX navigation (no full page reload)
    ═══════════════════════════════════════════════════════════ */
    function highlightDate(dateStr) {
        const today = todayISO();
        $('#cal-dates .cal-date-btn').each(function () {
            const d = $(this).data('date');
            if (d === dateStr) {
                $(this).css({ 'background-color': 'var(--color-primary)', color: '#fff', 'font-weight': '700' });
            } else if (d === today) {
                $(this).css({ 'background-color': 'transparent', color: 'var(--color-primary)', 'font-weight': '700' });
            } else {
                $(this).css({ 'background-color': 'transparent', color: 'var(--color-black)', 'font-weight': '400' });
            }
        });

        const sel = new Date(dateStr + 'T00:00:00');
        $('#cal-selected-label').text(
            'Data: ' + sel.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
        );

        const next = shiftDate(dateStr, 1);
        if (next > today) {
            $('#cal-next').prop('disabled', true).css({ 'background-color': '#F3F4F6', color: '#D1D5DB', cursor: 'not-allowed' });
        } else {
            $('#cal-next').prop('disabled', false).css({ 'background-color': '#EAEDF1', color: '#555', cursor: 'pointer' });
        }

        SELECTED_DATE = dateStr;
    }

    const ID_MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const DAY_NAMES = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    function rebuildWindow(newWinStart, selectedISO) {
        const today = todayISO();
        winStart = newWinStart;

        const days = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(newWinStart + 'T00:00:00');
            d.setDate(d.getDate() + i);
            days.push(d);
        }
        winEnd = toISO(days[6]);

        const last = days[6];
        $('#cal-month').text(ID_MONTHS[last.getMonth()] + ' ' + last.getFullYear());

        let namesHtml = '';
        days.forEach(d => {
            namesHtml += `<span style="font-size:9px;font-weight:600;color:rgba(60,60,67,0.4);text-align:center;display:block;">${DAY_NAMES[d.getDay()]}</span>`;
        });
        $('#cal-daynames').html(namesHtml);

        let datesHtml = '';
        days.forEach(d => {
            const dStr    = toISO(d);
            const isSel   = dStr === selectedISO;
            const isToday = dStr === today;
            const isFut   = dStr > today;

            if (isFut) {
                datesHtml += `<span style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:12px;color:#D1D5DB;margin:auto;">${d.getDate()}</span>`;
            } else if (isSel) {
                datesHtml += `<button type="button" class="cal-date-btn" data-date="${dStr}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:12px;font-weight:700;color:#fff;margin:auto;background-color:var(--color-primary);border:none;cursor:pointer;">${d.getDate()}</button>`;
            } else {
                const col = isToday ? 'var(--color-primary)' : 'var(--color-black)';
                const fw  = isToday ? '700' : '400';
                datesHtml += `<button type="button" class="cal-date-btn" data-date="${dStr}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:12px;color:${col};font-weight:${fw};margin:auto;background:transparent;border:none;cursor:pointer;">${d.getDate()}</button>`;
            }
        });
        $('#cal-dates').html(datesHtml);

        const selD = new Date(selectedISO + 'T00:00:00');
        $('#cal-selected-label').text(
            'Data: ' + selD.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
        );

        const next = shiftDate(selectedISO, 1);
        if (next > today) {
            $('#cal-next').prop('disabled', true).css({ 'background-color': '#F3F4F6', color: '#D1D5DB', cursor: 'not-allowed' });
        } else {
            $('#cal-next').prop('disabled', false).css({ 'background-color': '#EAEDF1', color: '#555', cursor: 'pointer' });
        }

        SELECTED_DATE = selectedISO;
    }

    /* PREV button */
    $(document).on('click', '#cal-prev', function () {
        const prev = shiftDate(SELECTED_DATE, -1);
        if (prev >= winStart) {
            highlightDate(prev);
        } else {
            rebuildWindow(shiftDate(winStart, -7), prev);
        }
        fetchStats(prev);
        fetchOrders(currentStatus, 1);
        fetchChartData(prev);
    });

    /* NEXT button */
    $(document).on('click', '#cal-next', function () {
        if ($(this).prop('disabled')) return;
        const today = todayISO();
        const next  = shiftDate(SELECTED_DATE, 1);
        if (next > today) return;

        if (next <= winEnd) {
            highlightDate(next);
        } else {
            rebuildWindow(shiftDate(today, -6), next);
        }
        fetchStats(next);
        fetchOrders(currentStatus, 1);
        fetchChartData(next);
    });

    /* Date cell click */
    $(document).on('click', '.cal-date-btn', function () {
        const d = $(this).data('date');
        highlightDate(d);
        fetchStats(d);
        fetchOrders(currentStatus, 1);
        fetchChartData(d);
    });

    /* ═══════════════════════════════════════════════════════════
       STATS — AJAX update
    ═══════════════════════════════════════════════════════════ */
    const UP_ARROW   = '<svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;margin-right:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>';
    const DOWN_ARROW = '<svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;margin-right:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';

   
            function fetchStats(dateStr) {
        $.get('{{ route("cashier.get-stats") }}', { date: dateStr })
            .done(function (res) {
                $('#widget-revenue').text('Rp ' + Number(res.revenue).toLocaleString('id-ID'));

                const g = Number(res.growth);
                $('#stat-revenue-growth').html(g >= 0
                    ? `<span style="display:inline-flex;align-items:center;font-size:11px;font-weight:600;padding:2px 8px;border-radius:9999px;background-color:#DCFCE7;color:#15803D;">${UP_ARROW}+${g}% dari kemarin</span>`
                    : `<span style="display:inline-flex;align-items:center;font-size:11px;font-weight:600;padding:2px 8px;border-radius:9999px;background-color:#FEE2E2;color:#B91C1C;">${DOWN_ARROW}${g}% dari kemarin</span>`
                );

                $('#widget-total-orders').text(Number(res.totalOrders) + ' orders');
                $('#stat-orders-detail').html(
                    `<span style="font-weight:700;color:var(--color-primary);">Online:</span>
                     <span style="color:#555;"> ${res.onlineOrders}</span>
                     <span style="color:#CBD5E1;">&nbsp;|&nbsp;</span>
                     <span style="font-weight:700;color:#FF9E00;">Cashier:</span>
                     <span style="color:#555;"> ${res.cashierOrders}</span>`
                );

                $('#widget-pending-orders').text(Number(res.pendingOrders) + ' orders');

                /* ── NEW ORDER DETECTION ── */
                if (dateStr === todayISO()) {
                    const currentTotal = Number(res.totalOrders);

                    if (lastTotalOrders !== -1 && currentTotal > lastTotalOrders) {
                        const newCount = currentTotal - lastTotalOrders;

                        // FIX 1: Web Audio API beep — replaces new Audio(externalURL) which
                        // is always blocked by autoplay policy when called outside a gesture.
                        playNotificationBeep();

                        // FIX 2: SweetAlert2 toast — was completely missing before.
                        Swal.fire({
                            toast:             true,
                            position:          'top-end',
                            icon:              'success',
                            title:             '🛎️ Pesanan Baru Masuk!',
                            text:              newCount + ' pesanan baru telah diterima.',
                            showConfirmButton:  false,
                            timer:             5000,
                            timerProgressBar:  true,
                        });

                        // FIX 3: fetchOrders removed from here — the setInterval already
                        // calls fetchOrders(silent=true) on the same tick, so calling it
                        // here too would fire two overlapping AJAX requests for the table.
                    }

                    lastTotalOrders = currentTotal;
                } else {
                    // Viewing a past date: reset so switching back to today doesn't
                    // trigger a false-positive notification on the next poll.
                    lastTotalOrders = -1;
                }
            });
    }
    /* ═══════════════════════════════════════════════════════════
       REVENUE CHART
    ═══════════════════════════════════════════════════════════ */
    const revenueChart = new Chart(document.getElementById('revenue-chart'), {
        type: 'line',
        data: {
            labels: @json(collect($chartData)->pluck('label')),
            datasets: [{
                label: 'Revenue',
                data:  @json(collect($chartData)->pluck('value')),
                borderColor: '#A6171C',
                borderWidth: 2.5,
                fill: false,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#A6171C',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#A6171C',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: { label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID') },
                    backgroundColor: '#1F2937', padding: 10, cornerRadius: 8,
                }
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ font:{size:11}, color:'#9CA3AF' } },
                y: {
                    grid: { color:'#E5E7EB', borderDash:[5,5] },
                    ticks: {
                        font:{size:10}, color:'#9CA3AF', maxTicksLimit:5,
                        callback: v => Number(v).toLocaleString('id-ID'),
                    },
                    beginAtZero: true,
                    min: 0,
                }
            }
        }
    });

    function fetchChartData(dateStr) {
        $.get('{{ route("cashier.get-chart-data") }}', { date: dateStr })
            .done(function (res) {
                revenueChart.data.labels           = res.labels;
                revenueChart.data.datasets[0].data = res.values;
                revenueChart.update();
            });
    }

    /* ═══════════════════════════════════════════════════════════
       ORDER TABLE HELPERS
    ═══════════════════════════════════════════════════════════ */
    const statusCfg = {
        Pending:   { bg:'rgba(245,158,11,0.12)', text:'#B45309', dot:'#F59E0B', row:'rgba(255,251,235,0.7)' },
        Preparing: { bg:'rgba(249,115,22,0.12)', text:'#C2410C', dot:'#F97316', row:'rgba(255,247,237,0.7)' },
        Ready:     { bg:'rgba(34,197,94,0.12)',  text:'#15803D', dot:'#22C55E', row:'rgba(240,253,244,0.7)' },
        Completed: { bg:'rgba(99,102,241,0.12)', text:'#4338CA', dot:'#6366F1', row:'rgba(238,242,255,0.7)' },
        Cancelled: { bg:'rgba(239,68,68,0.12)',  text:'#B91C1C', dot:'#EF4444', row:'rgba(254,226,226,0.7)' },
    };

    const tabStyles = {
        All:       { bg:'#fff',    text:'var(--color-primary)' },
        Pending:   { bg:'#F59E0B', text:'#fff' },
        Preparing: { bg:'#F97316', text:'#fff' },
        Ready:     { bg:'#22C55E', text:'#fff' },
        Completed: { bg:'#6366F1', text:'#fff' },
        Cancelled: { bg:'#EF4444', text:'#fff' },
    };

    const avatarPalette = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6','#8B5CF6','#EC4899','#06B6D4'];

    function getAvatarBg(name) {
        let h = 0;
        for (let i = 0; i < (name || '').length; i++) h = (h * 31 + name.charCodeAt(i)) & 0x7fffffff;
        return avatarPalette[h % avatarPalette.length];
    }

    function escAttr(str) {
        return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function buildRowHTML(order) {
        const cfg       = statusCfg[order.status] || statusCfg.Pending;
        const initial   = ((order.customer || 'W').charAt(0)).toUpperCase();
        const bg        = getAvatarBg(order.customer || '');
        const orderAttr = escAttr(JSON.stringify(order));
        const totalFmt  = 'Rp ' + Number(order.total || 0).toLocaleString('id-ID');
        const srcBadge  = order.source === 'online'
            ? `<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;background-color:var(--color-primary-surface);color:var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>Online</span>`
            : `<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;background-color:#FFF7ED;color:#F97316;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Cashier</span>`;
        const newBadge = order.is_new
            ? `<span style="font-size:9px;font-weight:900;padding:2px 4px;border-radius:4px;background-color:var(--color-primary);color:#fff;display:inline-block;">NEW</span>` : '';
        const subText = order.sub
            ? `<p style="font-size:10px;color:#9CA3AF;margin:0;">${order.sub}</p>` : '';

        return `<tr class="order-row" data-status="${order.status}" data-order="${orderAttr}"
                    style="background-color:${cfg.row};border-bottom:1px solid var(--color-border);">
            <td class="px-4 py-3 whitespace-nowrap"><p style="font-family:monospace;font-size:12px;font-weight:700;color:var(--color-black);margin:0 0 2px;">${order.id}</p>${newBadge}</td>
            <td class="px-4 py-3 whitespace-nowrap">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;background-color:${bg};">${initial}</div>
                    <div><p style="font-size:12px;font-weight:600;color:var(--color-black);margin:0;">${order.customer}</p>${subText}</div>
                </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">${srcBadge}</td>
            <td class="px-4 py-3 text-xs" style="color:#555;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${order.items}</td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:600;background-color:${cfg.bg};color:${cfg.text};">
                    <span style="width:6px;height:6px;border-radius:50%;flex-shrink:0;background-color:${cfg.dot};"></span>${order.status}
                </span>
            </td>
            <td class="px-4 py-3 text-xs whitespace-nowrap" style="color:#6B7280;">${order.time}</td>
            <td class="px-4 py-3 text-xs font-bold whitespace-nowrap" style="color:var(--color-black);">${totalFmt}</td>
            <td class="px-4 py-3 whitespace-nowrap">
                <button type="button" class="view-detail-btn" data-order="${orderAttr}"
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;border:1px solid var(--color-primary);color:var(--color-primary);background:transparent;cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>Detail
                </button>
            </td>
        </tr>`;
    }

    function buildPagination(current, last, status) {
        if (last <= 1) { $('#cashier-pagination').html(''); return; }
        const btn = (label, page, active, disabled) => {
            const base  = 'padding:5px 11px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid;cursor:pointer;';
            const color = disabled ? 'border-color:#E5E7EB;background:#fff;color:#D1D5DB;cursor:not-allowed;'
                        : active   ? 'border-color:var(--color-primary);background:var(--color-primary);color:#fff;'
                        :            'border-color:var(--color-border);background:#fff;color:#555;';
            return `<button class="page-link" data-page="${page}" data-status="${status}" ${disabled?'disabled':''} style="${base}${color}">${label}</button>`;
        };
        let html = btn('‹', current - 1, false, current <= 1), prev = null;
        for (let p = 1; p <= last; p++) {
            const inW = p === 1 || p === last || (p >= current - 2 && p <= current + 2);
            if (!inW) { if (prev !== '…') { html += `<span style="color:#9CA3AF;font-size:13px;padding:0 2px;">…</span>`; prev = '…'; } continue; }
            html += btn(p, p, p === current, false); prev = p;
        }
        html += btn('›', current + 1, false, current >= last);
        $('#cashier-pagination').html(html);
    }

    // FIX 4: added `silent` parameter (default false).
    // When silent=true the loading spinner is suppressed — used by the polling
    // interval so the table doesn't flash "Memuat data…" every 15 seconds.
    function fetchOrders(status, page, silent = false) {
        currentStatus = status;
        currentPage   = page;
        if (!silent) {
            $('#orders-tbody').html('<tr><td colspan="8" style="text-align:center;padding:32px;color:#9CA3AF;font-size:13px;">Memuat data…</td></tr>');
            $('#cashier-pagination').html('');
        }

        $.get('{{ route("cashier.get-orders") }}', { status, page, date: SELECTED_DATE })
            .done(function (res) {
                const tabMap = { all:'All', Pending:'Pending', Preparing:'Preparing', Ready:'Ready', Completed:'Completed', Cancelled:'Cancelled' };
                $.each(tabMap, function (key, label) {
                    if (res.counts && res.counts[key] !== undefined)
                        $('.order-tab[data-tab="' + label + '"] .tab-count').text('(' + res.counts[key] + ')');
                });

                /* Also sync the Order Status sidebar counts */
                if (res.counts) {
                    $('#status-pending').text(res.counts['Pending'] || 0);
                    $('#status-preparing').text(res.counts['Preparing'] || 0);
                    $('#status-ready').text(res.counts['Ready'] || 0);
                    $('#status-completed').text(res.counts['Completed'] || 0);
                }

                if (!res.items || !res.items.length) {
                    $('#orders-tbody').html('<tr><td colspan="8" style="text-align:center;padding:40px;color:#9CA3AF;font-size:13px;">Tidak ada order ditemukan</td></tr>');
                } else {
                    let rows = ''; $.each(res.items, function (i, o) { rows += buildRowHTML(o); });
                    $('#orders-tbody').html(rows);
                }
                buildPagination(res.current_page, res.last_page, status);
            })
            .fail(function () {
                if (!silent) {
                    $('#orders-tbody').html('<tr><td colspan="8" style="text-align:center;padding:32px;color:#EF4444;font-size:13px;">Gagal memuat data. Coba lagi.</td></tr>');
                }
            });
    }

    $(document).on('click', '.order-tab', function () {
        const tab    = $(this).data('tab');
        const status = $(this).data('status') || 'all';
        $('.order-tab').css({ 'background-color': 'rgba(255,255,255,0.18)', color: 'rgba(255,255,255,0.9)' });
        const s = tabStyles[tab] || tabStyles.All;
        $(this).css({ 'background-color': s.bg, color: s.text });
        fetchOrders(status, 1);
    });

    $(document).on('click', '.page-link:not([disabled])', function () {
        const page = parseInt($(this).data('page'), 10);
        if (!isNaN(page) && page > 0) fetchOrders(currentStatus, page);
    });

    /* Initial load */
    fetchStats(SELECTED_DATE);
    fetchOrders('all', 1);

    /* ═══════════════════════════════════════════════════════════
       ORDER DETAIL DRAWER
    ═══════════════════════════════════════════════════════════ */
    const stepOrder   = ['Pending','Preparing','Ready','Completed'];
    const typeMap     = { takeaway:'Take Away', 'dine-in':'Dine In', online:'Online Order' };
    const payMap      = { QRIS:'QRIS', Cash:'Cash', Debit:'Debit Card' };
    const stepFillPct = ['0%','33.33%','66.66%','100%'];
    const stepColors  = ['#F59E0B','#F97316','#22C55E','#6366F1'];
    let drawerOrder   = null;

    function renderStepperForStatus(status) {
        const idx = stepOrder.indexOf(status);
        $('#stepper-fill').css({ width: idx >= 0 ? stepFillPct[idx] : '0%', background: stepColors[Math.max(idx, 0)] });
        $('#drawer-stepper .step-item').each(function (i) {
            const $c = $(this).find('.step-circle'), $b = $(this).find('.step-badge');
            if (i <= idx) {
                $c.css({ 'border-color': $c.data('active-color'), color: $c.data('active-color'), background: '#fff' });
                $b.css({ 'border-color': $b.data('active-border'), color: $b.data('active-color'), background: $b.data('active-bg') });
            } else {
                $c.css({ 'border-color': '#E5E7EB', color: '#D1D5DB', background: '#F9FAFB' });
                $b.css({ 'border-color': '#E5E7EB', color: '#D1D5DB', background: '#F9FAFB' });
            }
        });
    }

    // Escape text before interpolating into innerHTML. Order item fields like
    // the custom-corndog notes (isi/varian/sauces) originate from customer input
    // and must never be injected raw, or they become stored XSS in this dashboard.
    function escHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function buildItemsHTML(order) {
        const items = order.order_items || [];
        if (!items.length) return '<p style="font-size:12px;color:#9CA3AF;text-align:center;padding:16px 0;">No items available</p>';
        return items.map(function (item) {
            const name = escHtml(item.name);
            let thumbInner, thumbBg;
            if (item.is_custom && item.base_img) {
                // Custom corndog → layered preview (varian base + sauce overlay),
                // matching the customer order-history thumbnail.
                thumbBg = '#FDECD8';
                thumbInner =
                    `<img src="${escHtml(item.base_img)}" alt="${name}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;">`
                    + (item.sauce_img
                        ? `<img src="${escHtml(item.sauce_img)}" alt="sauce" style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;z-index:2;">`
                        : '');
            } else {
                thumbBg = '#F3F4F6';
                const imgSrc = item.img || '';
                thumbInner = imgSrc
                    ? `<img src="${escHtml(imgSrc)}" alt="${name}" onerror="this.style.display='none';this.parentElement.style.background='#F3F4F6';" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">`
                    : '';
            }

            // Subtitle: custom → "isi · varian · sauce"; otherwise nothing extra.
            const subtitle = item.is_custom ? (item.custom_info || 'Custom Corndog') : (item.variant || '');
            const subtitleHTML = subtitle
                ? `<p style="font-size:11px;color:#9CA3AF;margin:0;line-height:1.35;">${escHtml(subtitle)}</p>`
                : '';

            return `<div style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #F5F5F5;">
                <div style="position:relative;width:52px;height:52px;border-radius:10px;overflow:hidden;flex-shrink:0;background:${thumbBg};border:1px solid #EFEFEF;">${thumbInner}</div>
                <div style="flex:1;min-width:0;padding-top:2px;">
                    <p style="font-size:13px;font-weight:700;color:var(--color-black);margin:0 0 2px;">${name}</p>
                    ${subtitleHTML}
                </div>
                <div style="flex-shrink:0;text-align:right;padding-top:2px;">
                    <p style="font-size:12px;font-weight:700;color:var(--color-black);margin:0 0 3px;">Rp ${Number(item.price || 0).toLocaleString('id-ID')}</p>
                    <span style="font-size:13px;font-weight:900;color:var(--color-primary);">×${item.qty}</span>
                </div>
            </div>`;
        }).join('');
    }

    function openDrawer(order) {
        drawerOrder = order;
        $('#drawer-order-id').text(order.id || '-');
        $('#drawer-customer-name').text(order.customer || 'Customer');
        $('#drawer-customer-phone').text(order.phone || 'Phone');
        $('#drawer-order-type').text(typeMap[order.order_type] || order.order_type || '-');
        $('#drawer-payment').text(payMap[order.payment] || order.payment || '-');
        $('#drawer-source').text(order.source === 'online' ? 'Online Order' : 'Kasir');
        $('#drawer-time').text(order.time || '-');
        $('#drawer-date').text(order.date || new Date().toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }));
        const grandTotal = Number(order.total || 0), subtotal = Math.round(grandTotal / 1.11), tax = grandTotal - subtotal;
        $('#drawer-subtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
        $('#drawer-tax').text('Rp ' + tax.toLocaleString('id-ID'));
        $('#drawer-total').text('Rp ' + grandTotal.toLocaleString('id-ID'));
        const totalQty = (order.order_items || []).reduce((s, i) => s + (i.qty || 0), 0);
        $('#drawer-item-count').text('×' + (totalQty || 1));
        order.is_new ? $('#drawer-new-badge').css('display','inline') : $('#drawer-new-badge').css('display','none');

        // Cashier name — always populate; show fallback when not yet assigned
        $('#drawer-cashier-name-text').text(
            order.cashier_name || (order.source === 'online' ? 'Online Order' : '-')
        );

        // Cancellation reason + lock stepper if Cancelled
        if (order.status === 'Cancelled') {
            if (order.cancellation_reason) {
                $('#drawer-cancel-reason-text').text(order.cancellation_reason);
                $('#drawer-cancel-reason-line').show();
            } else {
                $('#drawer-cancel-reason-line').hide();
            }
            $('#drawer-stepper-wrapper').css({ opacity: '0.45', 'pointer-events': 'none' });
            $('#btn-batalkan-order').css({ opacity: '0.45', 'pointer-events': 'none', cursor: 'not-allowed' });
        } else {
            $('#drawer-cancel-reason-line').hide();
            $('#drawer-stepper-wrapper').css({ opacity: '1', 'pointer-events': 'auto' });
            $('#btn-batalkan-order').css({ opacity: '1', 'pointer-events': 'auto', cursor: 'pointer' });
        }

        $('#drawer-items').html(buildItemsHTML(order));
        renderStepperForStatus(order.status);
        $('#drawer-backdrop').css('display', 'block');
        $('#order-detail-drawer').css('transform', 'translateX(0)');
    }

    function closeDrawer() {
        $('#order-detail-drawer').css('transform', 'translateX(100%)');
        $('#drawer-backdrop').css('display', 'none');
        drawerOrder = null;
    }

    $(document).on('click', '#order-detail-drawer', function (e) { e.stopPropagation(); });
    $(document).on('click', '.view-detail-btn', function (e) {
        e.stopPropagation();
        let o = {};
        try { o = JSON.parse($(this).attr('data-order') || '{}'); } catch (_) {}
        openDrawer(o);
    });
    $(document).on('click', '#close-drawer-btn', function (e) { e.preventDefault(); e.stopPropagation(); closeDrawer(); });
    $(document).on('click', '#drawer-backdrop', function (e) { e.preventDefault(); closeDrawer(); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });

    /* ═══════════════════════════════════════════════════════════
       STEPPER STEP CLICK — SweetAlert2 confirmation + AJAX
    ═══════════════════════════════════════════════════════════ */
    $(document).on('click', '.step-item', function (e) {
        e.stopPropagation();
        if (!drawerOrder) return;

        const targetStatus = $(this).data('step');
        const currentIdx   = stepOrder.indexOf(drawerOrder.status);
        const targetIdx    = stepOrder.indexOf(targetStatus);
        if (targetIdx <= currentIdx) return;

        Swal.fire({
            title: 'Ubah Status Order',
            text: 'Yakin ingin mengubah status menjadi "' + targetStatus + '"?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#A6171C',
            cancelButtonColor: '#6B7280',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            if (!drawerOrder.db_id) {
                drawerOrder.status = targetStatus;
                renderStepperForStatus(targetStatus);
                updateTableRow(drawerOrder);
                return;
            }

            $.ajax({
                type: 'POST',
                url:  '{{ route("cashier.orders.status", ":id") }}'.replace(':id', drawerOrder.db_id),
                data: { status: targetStatus, _token: CSRF },
                success: function (res) {
                    drawerOrder.status = res.status;
                    renderStepperForStatus(res.status);
                    updateTableRow(drawerOrder);
                },
                error: function () {
                    Swal.fire('Gagal', 'Tidak dapat mengubah status. Coba lagi.', 'error');
                }
            });
        });
    });

    /* ═══════════════════════════════════════════════════════════
       BATALKAN ORDER — SweetAlert2 textarea for reason
    ═══════════════════════════════════════════════════════════ */
    $(document).on('click', '#btn-batalkan-order', function (e) {
        e.stopPropagation();
        if (!drawerOrder) return;

        Swal.fire({
            title: 'Batalkan Order',
            html: '<p style="color:#555;font-size:13px;margin-bottom:6px;">Masukkan alasan pembatalan untuk <strong>' + drawerOrder.id + '</strong>:</p>',
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan pembatalan...',
            inputAttributes: { style: 'font-size:13px;border-radius:8px;border:1px solid #E5E7EB;padding:8px;' },
            showCancelButton: true,
            confirmButtonText: 'Batalkan Order',
            cancelButtonText: 'Tidak',
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            inputValidator: function (value) {
                if (!value || !value.trim()) return 'Alasan pembatalan wajib diisi!';
            }
        }).then(function (result) {
            if (!result.isConfirmed) return;

            if (!drawerOrder.db_id) {
                drawerOrder.status = 'Cancelled';
                drawerOrder.cancellation_reason = result.value;
                renderStepperForStatus('Cancelled');
                updateTableRow(drawerOrder);
                closeDrawer();
                return;
            }

            $.ajax({
                type: 'POST',
                url:  '{{ route("cashier.orders.status", ":id") }}'.replace(':id', drawerOrder.db_id),
                data: { status: 'Cancelled', cancellation_reason: result.value, _token: CSRF },
                success: function () {
                    drawerOrder.status = 'Cancelled';
                    drawerOrder.cancellation_reason = result.value;
                    updateTableRow(drawerOrder);
                    closeDrawer();
                    fetchOrders(currentStatus, currentPage || 1);
                    Swal.fire('Berhasil', 'Order dibatalkan dan uang telah di-refund.', 'success');
                },
                // --- PASTIKAN BAGIAN INI SAMA ---
                error: function (xhr) {
                    let errorMsg = 'Tidak dapat membatalkan order. Coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal Membatalkan', errorMsg, 'error');
                }
            });
        });
    });

    /* ═══════════════════════════════════════════════════════════
       UPDATE TABLE ROW after status change
    ═══════════════════════════════════════════════════════════ */
    function updateTableRow(order) {
        const cfg = statusCfg[order.status] || statusCfg.Pending;
        $('#orders-tbody .order-row').each(function () {
            try {
                const o = JSON.parse($(this).attr('data-order') || '{}');
                if (o.id === order.id) {
                    o.status = order.status;
                    $(this).attr('data-order', JSON.stringify(o))
                           .attr('data-status', order.status)
                           .css('background-color', cfg.row);
                    const $badge = $(this).find('span[style*="border-radius"]').filter(function () {
                        return $(this).text().trim().length < 20;
                    }).first();
                    $badge.css({ 'background-color': cfg.bg, color: cfg.text });
                    $badge.find('span').css('background-color', cfg.dot);
                    $badge.contents().filter(function () { return this.nodeType === 3; }).last().replaceWith(' ' + order.status);
                }
            } catch (_) {}
        });
    }
    /* ═══════════════════════════════════════════════════════════
       AUTO-REFRESH POLLING — every 15 seconds
       FIX 5: interval now calls BOTH fetchStats (detects new orders
       and triggers the toast + beep) AND fetchOrders with silent=true
       (keeps the table current for status changes without a spinner).
    ═══════════════════════════════════════════════════════════ */
    setInterval(function () {
        if (SELECTED_DATE !== todayISO()) return;
        fetchStats(SELECTED_DATE);
        fetchOrders(currentStatus, currentPage, true);
    }, 15000);
});
</script>

@endsection
