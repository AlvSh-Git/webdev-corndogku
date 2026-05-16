@extends('layouts.app')

@section('title', 'Dashboard — Kasir')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. PAGE HEADER  — matches Figma "E.Dash - cashier" frame
══════════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-4xl font-bold leading-tight" style="color: var(--color-black);">
            Welcome to Corndog-Ku!
        </h1>
        <p class="mt-1 text-base" style="color: #555;">
            Jl. Rungkut Mejoyo Utara No.61, Blora
        </p>
    </div>

    <button type="button"
            class="px-6 py-2 rounded-full font-bold text-sm tracking-wide
                   transition-opacity hover:opacity-80 min-h-[44px]"
            style="background-color: var(--color-primary); color: var(--color-white);">
        BUKA TUTUP TOKO
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════
     2. STAT CARDS + CALENDAR  (3/4 + 1/4 grid)
     Figma: revenue cashier tab, total order cashier tab, pending orders card
══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">

    {{-- ── LEFT: three stat cards (span 3 cols) ──────────────── --}}
    <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Revenue Today — "revenue cashier tab" in Figma --}}
        <div class="rounded-xl p-5"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <p class="text-sm" style="color: #555;">Revenue Today</p>
                {{-- Circle icon from Figma (currency symbol) --}}
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-none"
                     style="background-color: var(--color-primary-surface);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke="#A6171C" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3
                                 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0
                                 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color: var(--color-black);">Rp 800.000</p>
        </div>

        {{-- Total Order Today — "total order cashier tab" in Figma --}}
        <div class="rounded-xl p-5"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <p class="text-sm" style="color: #555;">Total Order Today</p>
                <img src="{{ asset('assets/ui/icon-order.svg') }}" alt="" class="w-5 h-5 opacity-60">
            </div>
            <p class="text-2xl font-bold" style="color: var(--color-black);">35 orders</p>
            <p class="text-xs mt-1">
                <span class="font-bold" style="color: var(--color-primary);">Online:</span>
                <span style="color: #757575;"> 20</span>
                <span style="color: #9c9c9c;"> &nbsp;|&nbsp; </span>
                <span class="font-bold" style="color: var(--color-primary);">Cashier:</span>
                <span style="color: #9c9c9c;"> 15</span>
            </p>
        </div>

        {{-- Pending Orders — node 244:9765 in Figma --}}
        <div class="rounded-xl p-5"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">
            <div class="flex items-start justify-between mb-3">
                <p class="text-sm" style="color: #555;">Pending Orders</p>
                <img src="{{ asset('assets/ui/icon-order.svg') }}" alt="" class="w-5 h-5 opacity-60">
            </div>
            <p class="text-2xl font-bold" style="color: var(--color-black);">8 orders</p>
            <p class="text-xs mt-1 font-bold" style="color: var(--color-primary);">Perlu diproses</p>
        </div>

    </div>

    {{-- ── RIGHT: mini calendar (span 1 col) ─────────────────── --}}
    <div class="lg:col-span-1">
        <div class="rounded-xl p-4 h-full"
             style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

            {{-- Month header — Figma: "November 2025" with nav arrows --}}
            <div class="flex items-center justify-between mb-3">
                <p class="font-bold text-sm" style="color: var(--color-black);">November 2025</p>
                <div class="flex gap-1">
                    <button class="w-6 h-6 rounded flex items-center justify-center text-xs"
                            style="background-color: #EAEDF1; border-radius: 6px;">&lsaquo;</button>
                    <button class="w-6 h-6 rounded flex items-center justify-center text-xs"
                            style="background-color: #EAEDF1; border-radius: 6px;">&rsaquo;</button>
                </div>
            </div>

            {{-- Day-of-week row — Figma: SUN–SAT with muted text, SAT in white (active) --}}
            <div class="grid grid-cols-7 text-center mb-1">
                @php $days = ['SUN','MON','TUE','WED','THU','FRI','SAT']; @endphp
                @foreach ($days as $i => $day)
                    <span class="text-xs font-semibold"
                          style="color: {{ $i === 6 ? 'var(--color-white)' : 'rgba(60,60,67,0.3)' }};
                                 background-color: {{ $i === 6 ? 'var(--color-primary)' : 'transparent' }};
                                 border-radius: {{ $i === 6 ? '4px' : '0' }}; padding: 1px 0;">
                        {{ $day }}
                    </span>
                @endforeach
            </div>

            {{-- Date row — Figma: 12–18, SAT (18) highlighted red --}}
            <div class="grid grid-cols-7 text-center mt-2">
                @php $week = [12, 13, 14, 15, 16, 17, 18]; @endphp
                @foreach ($week as $i => $day)
                    @if ($i === 6)
                        <span class="rounded-full w-7 h-7 flex items-center justify-center mx-auto
                                     text-sm font-bold text-white"
                              style="background-color: var(--color-primary);">{{ $day }}</span>
                    @else
                        <span class="text-sm" style="color: var(--color-black);">{{ $day }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

</div>{{-- /stat + calendar grid --}}

{{-- ══════════════════════════════════════════════════════════════
     3. ORDER LIST  — "Order List" card from Figma (node 1:3727)
     Red header bar, filter tabs (All/Pending/Preparing/Ready/Completed),
     table: Order ID · Customer · Source · Items · Status · Time · Total
══════════════════════════════════════════════════════════════ --}}
<div class="rounded-xl overflow-hidden"
     style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

    {{-- Red header + filter tabs — exact Figma tab labels and colors --}}
    <div class="flex flex-wrap items-center gap-2 px-5 py-3"
         style="background-color: var(--color-primary);">
        <span class="font-bold text-white text-base mr-2">Order List</span>

        @php
            $tabs = [
                ['label' => 'All',       'count' => 35, 'active' => true,  'color' => 'var(--color-primary)', 'bg' => 'var(--color-white)'],
                ['label' => 'Pending',   'count' => 3,  'active' => false, 'color' => '#9c9c9c',              'bg' => 'rgba(255,255,255,0.15)'],
                ['label' => 'Preparing', 'count' => 10, 'active' => false, 'color' => '#FFBE54',              'bg' => 'rgba(255,255,255,0.15)'],
                ['label' => 'Ready',     'count' => 3,  'active' => false, 'color' => '#4CAF50',              'bg' => 'rgba(255,255,255,0.15)'],
                ['label' => 'Completed', 'count' => 3,  'active' => false, 'color' => '#24D366',              'bg' => 'rgba(255,255,255,0.15)'],
            ];
        @endphp
        @foreach ($tabs as $tab)
            <button type="button"
                    class="px-3 py-1 rounded-full text-xs font-bold transition-colors whitespace-nowrap"
                    style="background-color: {{ $tab['bg'] }}; color: {{ $tab['color'] }};">
                {{ $tab['label'] }} ({{ $tab['count'] }})
            </button>
        @endforeach
    </div>

    {{-- Table — columns from Figma: Order ID, Customer, Source, Items, Status, Time, Total --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--color-border);">
                    @foreach (['Order ID', 'Customer', 'Source', 'Items', 'Status', 'Time', 'Total'] as $col)
                        <th class="px-3 py-3 md:px-5 text-left font-semibold whitespace-nowrap"
                            style="color: #555;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--color-border);">
                @php
                    $orders = [
                        ['id' => '#12345', 'customer' => 'Budi S.',    'source' => 'Cashier', 'items' => 'Original Corndog × 1',     'status' => 'Completed', 'time' => '12:30', 'total' => 'Rp 16.000'],
                        ['id' => '#12346', 'customer' => 'Rina W.',    'source' => 'Online',  'items' => 'Squid Nori Corndog × 1',   'status' => 'Ready',     'time' => '12:45', 'total' => 'Rp 20.000'],
                        ['id' => '#12347', 'customer' => 'Doni A.',    'source' => 'Online',  'items' => 'Mozza Cheese Corndog × 1', 'status' => 'Preparing', 'time' => '13:00', 'total' => 'Rp 18.000'],
                        ['id' => '#12348', 'customer' => 'Sari M.',    'source' => 'Cashier', 'items' => 'Original Corndog × 1',     'status' => 'Preparing', 'time' => '13:10', 'total' => 'Rp 18.000'],
                        ['id' => '#12349', 'customer' => 'Joko P.',    'source' => 'Online',  'items' => 'Squid Nori Corndog × 1',   'status' => 'Pending',   'time' => '13:40', 'total' => 'Rp 18.000'],
                    ];
                    $statusStyles = [
                        'Pending'   => 'background-color: rgba(156,156,156,0.15); color: #9c9c9c;',
                        'Preparing' => 'background-color: rgba(255,190,84,0.15);  color: #B8860B;',
                        'Ready'     => 'background-color: rgba(76,175,80,0.15);   color: #2E7D32;',
                        'Completed' => 'background-color: rgba(36,211,102,0.12);  color: #1B8A44;',
                    ];
                @endphp
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 py-3 md:px-5 font-medium whitespace-nowrap"
                            style="color: var(--color-black);">{{ $order['id'] }}</td>
                        <td class="px-3 py-3 md:px-5 whitespace-nowrap"
                            style="color: #555;">{{ $order['customer'] }}</td>
                        <td class="px-3 py-3 md:px-5 whitespace-nowrap"
                            style="color: #555;">{{ $order['source'] }}</td>
                        <td class="px-3 py-3 md:px-5"
                            style="color: #555;">{{ $order['items'] }}</td>
                        <td class="px-3 py-3 md:px-5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap"
                                  style="{{ $statusStyles[$order['status']] }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                        <td class="px-3 py-3 md:px-5 whitespace-nowrap"
                            style="color: #555;">{{ $order['time'] }}</td>
                        <td class="px-3 py-3 md:px-5 font-semibold whitespace-nowrap"
                            style="color: var(--color-black);">{{ $order['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
