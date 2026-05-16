@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. PAGE HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-4xl font-bold leading-tight" style="color: var(--color-black);">
            Welcome to Corndog-Ku!
        </h1>
        <p class="mt-1 text-base" style="color: #555;">
            Jl. Pemuda No.18, Blora
        </p>
    </div>

    {{-- BUKA / TUTUP TOKO toggle --}}
    <button type="button"
            class="px-6 py-2 rounded-full font-bold text-sm tracking-wide transition-opacity hover:opacity-80"
            style="background-color: var(--color-primary); color: var(--color-white);">
        BUKA TUTUP TOKO
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════
     2. MAIN GRID  (left 3/4 · right 1/4)
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- ── LEFT COLUMN ───────────────────────────────────────── --}}
    <div class="lg:col-span-3 flex flex-col gap-6">

        {{-- 2a. SUMMARY STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Revenue Today --}}
            <div class="rounded-xl p-4 relative"
                 style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-sm" style="color: #555;">Revenue Today</p>
                    <img src="{{ asset('assets/ui/icon-revenue.svg') }}" alt="" class="w-5 h-5 opacity-60">
                </div>
                <p class="text-2xl font-bold" style="color: var(--color-black);">Rp 800.000</p>
            </div>

            {{-- Total Orders Today --}}
            <div class="rounded-xl p-4 relative"
                 style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-sm" style="color: #555;">Total Orders Today</p>
                    <img src="{{ asset('assets/ui/icon-order.svg') }}" alt="" class="w-5 h-5 opacity-60">
                </div>
                <p class="text-2xl font-bold" style="color: var(--color-black);">35 orders</p>
                <p class="text-xs mt-1" style="color: #9c9c9c;">Online: 20 &nbsp;|&nbsp; Cashier: 15</p>
            </div>

            {{-- Pending Orders --}}
            <div class="rounded-xl p-4 relative"
                 style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-sm" style="color: #555;">Pending Orders</p>
                    <img src="{{ asset('assets/ui/icon-profit.svg') }}" alt="" class="w-5 h-5 opacity-60">
                </div>
                <p class="text-2xl font-bold" style="color: var(--color-black);">8 orders</p>
                <p class="text-xs mt-1" style="color: var(--color-primary);">Perlu diproses</p>
            </div>
        </div>

        {{-- 2b. REVENUE LINE CHART --}}
        <div class="rounded-xl p-5"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
            <h2 class="text-lg font-bold mb-4" style="color: var(--color-black);">Revenue</h2>

            <svg viewBox="0 0 680 250" class="w-full h-auto" aria-label="Revenue chart for the past week">

                {{-- Y-axis grid lines & labels --}}
                @php
                    $yLabels = ['1.000.000', '800.000', '600.000', '400.000', '200.000', '0'];
                    $yPositions = [20, 58, 96, 134, 172, 210];
                @endphp
                @foreach ($yPositions as $i => $yPos)
                    <line x1="65" y1="{{ $yPos }}" x2="645" y2="{{ $yPos }}"
                          stroke="#E5E7EB" stroke-width="1"/>
                    <text x="58" y="{{ $yPos + 4 }}" text-anchor="end"
                          font-size="11" fill="#9ca3af">{{ $yLabels[$i] }}</text>
                @endforeach

                {{-- Data line (Sun–Sat: 0, 200k, 590k, 550k, 650k, 400k, 500k) --}}
                <polyline
                    points="65,210 162,172 259,98 356,106 453,87 550,134 645,115"
                    fill="none"
                    stroke="#A6171C"
                    stroke-width="2.5"
                    stroke-linejoin="round"
                    stroke-linecap="round"/>

                {{-- Data points --}}
                @php
                    $points = [
                        ['x' => 65,  'y' => 210, 'label' => 'Sun'],
                        ['x' => 162, 'y' => 172, 'label' => 'Mon'],
                        ['x' => 259, 'y' => 98,  'label' => 'Tue'],
                        ['x' => 356, 'y' => 106, 'label' => 'Wed'],
                        ['x' => 453, 'y' => 87,  'label' => 'Thu'],
                        ['x' => 550, 'y' => 134, 'label' => 'Fri'],
                        ['x' => 645, 'y' => 115, 'label' => 'Sat'],
                    ];
                @endphp
                @foreach ($points as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5"
                            fill="white" stroke="#A6171C" stroke-width="2.5"/>
                    <text x="{{ $pt['x'] }}" y="230" text-anchor="middle"
                          font-size="12" fill="#6b7280">{{ $pt['label'] }}</text>
                @endforeach
            </svg>
        </div>

        {{-- 2c. ACTIVE ORDERS STATUS SUMMARY --}}
        <div>
            <h2 class="text-base font-bold mb-3" style="color: var(--color-black);">Active Orders</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @php
                    $statuses = [
                        ['label' => 'Pending',    'count' => 3,  'bg' => '#FEF3C7', 'text' => '#D97706'],
                        ['label' => 'Preparing',  'count' => 10, 'bg' => '#DBEAFE', 'text' => '#2563EB'],
                        ['label' => 'Ready',      'count' => 3,  'bg' => '#DCFCE7', 'text' => '#16A34A'],
                        ['label' => 'Completed',  'count' => 19, 'bg' => '#F3F4F6', 'text' => '#6B7280'],
                    ];
                @endphp
                @foreach ($statuses as $s)
                    <div class="rounded-xl p-4 text-center"
                         style="background-color: {{ $s['bg'] }};">
                        <p class="text-3xl font-bold" style="color: {{ $s['text'] }};">{{ $s['count'] }}</p>
                        <p class="text-xs font-medium mt-1" style="color: {{ $s['text'] }};">{{ $s['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /left column --}}

    {{-- ── RIGHT COLUMN ──────────────────────────────────────── --}}
    <div class="lg:col-span-1 flex flex-col gap-4">

        {{-- 2d. LOW STOCK PANEL --}}
        <div class="rounded-xl p-4 flex flex-col gap-3"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm" style="color: #555;">Low Stock</p>
                    <p class="text-xl font-bold" style="color: var(--color-primary);">4 Items</p>
                </div>
                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-white text-lg"
                     style="background-color: var(--color-primary);">!</div>
            </div>

            {{-- Item list --}}
            <ul class="divide-y text-sm" style="border-color: var(--color-border);">
                @php
                    $lowStockItems = [
                        ['name' => 'Sosis',             'qty' => '3 pcs'],
                        ['name' => 'Corndog Coklat',    'qty' => '3 pcs'],
                        ['name' => 'Bingsoo Strowberry','qty' => '3 pcs'],
                        ['name' => 'Sosis',             'qty' => '3 pcs'],
                    ];
                @endphp
                @foreach ($lowStockItems as $item)
                    <li class="flex justify-between py-2">
                        <span style="color: var(--color-black);">{{ $item['name'] }}</span>
                        <span class="font-medium" style="color: var(--color-primary);">{{ $item['qty'] }}</span>
                    </li>
                @endforeach
            </ul>

            {{-- Restock button --}}
            <button type="button"
                    class="w-full py-2 rounded-lg font-bold text-sm transition-opacity hover:opacity-80"
                    style="background-color: var(--color-primary); color: var(--color-white);">
                Restock Item
            </button>
        </div>

        {{-- 2e. MINI CALENDAR --}}
        <div class="rounded-xl p-4"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

            {{-- Month header --}}
            <div class="flex items-center justify-between mb-3">
                <p class="font-bold text-sm" style="color: var(--color-black);">November 2025</p>
                <div class="flex gap-1">
                    <button class="w-6 h-6 rounded flex items-center justify-center text-xs"
                            style="border: 1px solid var(--color-border);">&lsaquo;</button>
                    <button class="w-6 h-6 rounded flex items-center justify-center text-xs"
                            style="border: 1px solid var(--color-border);">&rsaquo;</button>
                </div>
            </div>

            {{-- Day-of-week headers --}}
            <div class="grid grid-cols-7 text-center mb-1">
                @foreach (['SUN','MON','TUE','WED','THU','FRI','SAT'] as $day)
                    <span class="text-xs font-medium" style="color: #9c9c9c;">{{ $day }}</span>
                @endforeach
            </div>

            {{-- Week row (12–18 Nov 2025, Saturday highlighted) --}}
            <div class="grid grid-cols-7 text-center">
                @php $week = [12, 13, 14, 15, 16, 17, 18]; @endphp
                @foreach ($week as $i => $day)
                    @if ($i === 6) {{-- Saturday = today/active --}}
                        <span class="rounded-full w-7 h-7 flex items-center justify-center mx-auto text-sm font-bold text-white"
                              style="background-color: var(--color-primary);">{{ $day }}</span>
                    @else
                        <span class="text-sm py-1" style="color: var(--color-black);">{{ $day }}</span>
                    @endif
                @endforeach
            </div>
        </div>

    </div>{{-- /right column --}}
</div>{{-- /main grid --}}

{{-- ══════════════════════════════════════════════════════════════
     3. NEW ORDERS + ORDER DETAIL  (side by side)
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- New Orders incoming list --}}
    <div class="rounded-xl overflow-hidden"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="px-5 py-3 font-bold text-white text-sm"
             style="background-color: var(--color-primary);">
            New Orders
        </div>
        <ul class="divide-y text-sm" style="border-color: var(--color-border);">
            @php
                $newOrders = [
                    ['id' => '#12350', 'item' => 'Original Corndog',   'qty' => 2, 'time' => '13:45'],
                    ['id' => '#12351', 'item' => 'Squid Nori Corndog', 'qty' => 1, 'time' => '13:47'],
                    ['id' => '#12352', 'item' => 'Mozza Cheese',       'qty' => 3, 'time' => '13:50'],
                ];
            @endphp
            @foreach ($newOrders as $order)
                <li class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 cursor-pointer">
                    <div>
                        <p class="font-semibold" style="color: var(--color-black);">{{ $order['id'] }}</p>
                        <p style="color: #757575;">{{ $order['item'] }} &times; {{ $order['qty'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                              style="background-color: var(--color-status-inactive-bg);
                                     color: var(--color-status-inactive-text);">
                            Pending
                        </span>
                        <p class="text-xs mt-1" style="color: #9c9c9c;">{{ $order['time'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Order Detail panel --}}
    <div class="rounded-xl overflow-hidden"
         style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
        <div class="px-5 py-3 font-bold text-white text-sm"
             style="background-color: var(--color-primary);">
            Order Detail
        </div>
        <div class="p-5">
            {{-- Selected order header --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-bold text-base" style="color: var(--color-black);">#12350</p>
                    <p class="text-xs" style="color: #9c9c9c;">13:45 &bull; Online</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-medium"
                      style="background-color: var(--color-status-inactive-bg);
                             color: var(--color-status-inactive-text);">
                    Pending
                </span>
            </div>

            {{-- Items --}}
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="text-left" style="border-bottom: 1px solid var(--color-border);">
                        <th class="pb-2 font-medium" style="color: #9c9c9c;">Item</th>
                        <th class="pb-2 font-medium text-center" style="color: #9c9c9c;">Qty</th>
                        <th class="pb-2 font-medium text-right" style="color: #9c9c9c;">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--color-border);">
                    <tr>
                        <td class="py-2" style="color: var(--color-black);">Original Corndog</td>
                        <td class="py-2 text-center" style="color: #757575;">2</td>
                        <td class="py-2 text-right" style="color: var(--color-black);">Rp 36.000</td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between font-bold text-sm pt-2"
                 style="border-top: 2px solid var(--color-border);">
                <span>Total</span>
                <span style="color: var(--color-primary);">Rp 36.000</span>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 mt-5">
                <button type="button"
                        class="flex-1 py-2 rounded-lg text-sm font-semibold transition-opacity hover:opacity-80"
                        style="border: 1.5px solid var(--color-primary); color: var(--color-primary);">
                    Reject
                </button>
                <button type="button"
                        class="flex-1 py-2 rounded-lg text-sm font-semibold transition-opacity hover:opacity-80"
                        style="background-color: var(--color-primary); color: var(--color-white);">
                    Accept &amp; Process
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     4. ORDER LIST TABLE  (with filter tabs)
══════════════════════════════════════════════════════════════ --}}
<div class="mt-6 rounded-xl overflow-hidden"
     style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

    {{-- Table header + filter tabs --}}
    <div class="flex flex-wrap items-center gap-2 px-5 py-3"
         style="background-color: var(--color-primary);">
        <span class="font-bold text-white mr-2">Order List</span>

        @php
            $tabs = [
                ['label' => 'All',       'count' => 35, 'active' => true],
                ['label' => 'Pending',   'count' => 3,  'active' => false],
                ['label' => 'Preparing', 'count' => 10, 'active' => false],
                ['label' => 'Ready',     'count' => 3,  'active' => false],
                ['label' => 'Completed', 'count' => 3,  'active' => false],
            ];
        @endphp
        @foreach ($tabs as $tab)
            <button type="button"
                    class="px-3 py-1 rounded-full text-xs font-semibold transition-colors"
                    style="{{ $tab['active']
                        ? 'background-color: var(--color-white); color: var(--color-primary);'
                        : 'background-color: rgba(255,255,255,0.15); color: var(--color-white);' }}">
                {{ $tab['label'] }} ({{ $tab['count'] }})
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--color-border);">
                    @foreach (['Order ID', 'Customer', 'Source', 'Items', 'Status', 'Time', 'Total'] as $col)
                        <th class="px-3 py-2 md:px-5 md:py-3 text-left font-semibold" style="color: #555;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--color-border);">
                @php
                    $orders = [
                        ['id' => '#12345', 'customer' => 'Budi S.',    'source' => 'Online',  'items' => 'Original',     'status' => 'Completed', 'time' => '12:30', 'total' => 'Rp 16.000'],
                        ['id' => '#12346', 'customer' => 'Rina W.',    'source' => 'Cashier', 'items' => 'Squid Nori',   'status' => 'Ready',     'time' => '12:45', 'total' => 'Rp 20.000'],
                        ['id' => '#12347', 'customer' => 'Doni A.',    'source' => 'Online',  'items' => 'Mozza Cheese', 'status' => 'Preparing', 'time' => '13:00', 'total' => 'Rp 18.000'],
                        ['id' => '#12348', 'customer' => 'Sari M.',    'source' => 'Cashier', 'items' => 'Original',     'status' => 'Preparing', 'time' => '13:10', 'total' => 'Rp 18.000'],
                        ['id' => '#12349', 'customer' => 'Joko P.',    'source' => 'Online',  'items' => 'Squid Nori',   'status' => 'Pending',   'time' => '13:40', 'total' => 'Rp 18.000'],
                    ];
                    $statusStyles = [
                        'Pending'   => 'background-color:#FEF3C7; color:#D97706;',
                        'Preparing' => 'background-color:#DBEAFE; color:#2563EB;',
                        'Ready'     => 'background-color:var(--color-status-active-bg); color:var(--color-status-active-text);',
                        'Completed' => 'background-color:#F3F4F6; color:#6B7280;',
                    ];
                @endphp
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 py-2 md:px-5 md:py-3 font-medium" style="color: var(--color-black);">{{ $order['id'] }}</td>
                        <td class="px-3 py-2 md:px-5 md:py-3" style="color: #555;">{{ $order['customer'] }}</td>
                        <td class="px-3 py-2 md:px-5 md:py-3" style="color: #555;">{{ $order['source'] }}</td>
                        <td class="px-3 py-2 md:px-5 md:py-3" style="color: #555;">{{ $order['items'] }}</td>
                        <td class="px-3 py-2 md:px-5 md:py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="{{ $statusStyles[$order['status']] }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                        <td class="px-3 py-2 md:px-5 md:py-3" style="color: #555;">{{ $order['time'] }}</td>
                        <td class="px-3 py-2 md:px-5 md:py-3 font-semibold" style="color: var(--color-black);">{{ $order['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
