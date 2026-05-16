@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')

@php
    $transactions = [
        ['date' => '2025-11-15', 'order_id' => '#12350', 'items' => 'Original Corndog × 2, Double Cheese × 1', 'total' => 52000,  'payment' => 'Cash'],
        ['date' => '2025-11-15', 'order_id' => '#12351', 'items' => 'Squid Nori × 1',                          'total' => 18000,  'payment' => 'QRIS'],
        ['date' => '2025-11-15', 'order_id' => '#12352', 'items' => 'Full Mozza × 3, Mie Bakar Sosis × 1',    'total' => 78000,  'payment' => 'Cash'],
        ['date' => '2025-11-14', 'order_id' => '#12349', 'items' => 'Es Teler Kwentel Ori × 2',               'total' => 30000,  'payment' => 'QRIS'],
        ['date' => '2025-11-14', 'order_id' => '#12348', 'items' => 'Choco Crunchy Cheese × 1, Full Sausages × 2', 'total' => 56000, 'payment' => 'Cash'],
        ['date' => '2025-11-13', 'order_id' => '#12347', 'items' => 'Double Cheese × 4',                      'total' => 72000,  'payment' => 'QRIS'],
        ['date' => '2025-11-13', 'order_id' => '#12346', 'items' => 'Original Corndog × 1, Es Teler + Durian × 1', 'total' => 31000, 'payment' => 'Cash'],
        ['date' => '2025-11-12', 'order_id' => '#12345', 'items' => 'Mie Bakar Sosis × 2',                    'total' => 48000,  'payment' => 'Cash'],
    ];

    $summaryCards = [
        ['label' => 'Total Revenue',   'value' => 'Rp 1.245.000', 'sub' => 'This month',    'color' => '#A6171C', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
        ['label' => 'Total Orders',    'value' => '312',           'sub' => 'This month',    'color' => '#4A90D9', 'icon' => 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0'],
        ['label' => 'Avg Order Value', 'value' => 'Rp 39.900',    'sub' => 'Per transaction','color' => '#27AE60', 'icon' => 'M3 3v18h18M18.7 8l-5.1 5.2-2.8-2.7L7 14.3'],
        ['label' => 'Cash vs QRIS',    'value' => '58% / 42%',    'sub' => 'Payment split', 'color' => '#8E44AD', 'icon' => 'M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm-1-5h2v2h-2zm0-8h2v5h-2'],
    ];

    // Chart data — daily revenue for last 7 days
    $chartDays  = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $chartVals  = [180000, 240000, 195000, 320000, 280000, 410000, 345000];
    $chartMax   = max($chartVals);
@endphp

{{-- ════════════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-3xl md:text-4xl font-bold" style="color:var(--color-black);">Sales Report</h1>
        <p class="text-sm mt-1" style="color:#555;">Track revenue, orders, and transaction history.</p>
    </div>

    {{-- Export button --}}
    <button type="button"
            class="flex items-center gap-2 px-5 py-2.5 rounded-full font-semibold text-sm
                   transition-opacity hover:opacity-80 min-h-[44px]"
            style="background:var(--color-primary); color:var(--color-white);">
        <svg class="w-4 h-4 flex-none" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export PDF / Excel
    </button>
</div>

{{-- ════════════════════════════════════════════════════════
     DATE FILTER
════════════════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-end gap-3 mb-6 p-4 rounded-xl"
     style="background:var(--color-white); box-shadow:var(--shadow-card);">

    <div class="flex-1 min-w-0">
        <label class="block text-xs font-semibold mb-1" style="color:#555;">Start Date</label>
        <input type="date"
               id="date-start"
               value="{{ date('Y-m-01') }}"
               class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none"
               style="border:1px solid var(--color-border);">
    </div>

    <div class="flex-1 min-w-0">
        <label class="block text-xs font-semibold mb-1" style="color:#555;">End Date</label>
        <input type="date"
               id="date-end"
               value="{{ date('Y-m-d') }}"
               class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none"
               style="border:1px solid var(--color-border);">
    </div>

    <div class="flex gap-2 shrink-0">
        <button type="button"
                class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-opacity hover:opacity-80"
                style="background:var(--color-primary); color:var(--color-white);">
            Apply Filter
        </button>
        <button type="button"
                onclick="resetDates()"
                class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-opacity hover:opacity-70"
                style="border:1px solid var(--color-border); color:#555;">
            Reset
        </button>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     SUMMARY CARDS
════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach ($summaryCards as $card)
        <div class="rounded-xl p-4 flex items-start gap-3"
             style="background:var(--color-white); box-shadow:var(--shadow-card);">

            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                 style="background-color:{{ $card['color'] }}1a;">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                     stroke="{{ $card['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $card['icon'] }}"/>
                </svg>
            </div>

            <div class="min-w-0">
                <p class="text-xs" style="color:#9c9c9c;">{{ $card['label'] }}</p>
                <p class="font-bold text-lg leading-tight" style="color:var(--color-black);">{{ $card['value'] }}</p>
                <p class="text-xs mt-0.5" style="color:#9c9c9c;">{{ $card['sub'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════════════
     SALES CHART
════════════════════════════════════════════════════════ --}}
<div class="rounded-xl p-5 mb-6"
     style="background:var(--color-white); box-shadow:var(--shadow-card);">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <h2 class="font-bold text-base" style="color:var(--color-black);">Daily Revenue</h2>

        {{-- Period toggle --}}
        <div class="flex rounded-lg overflow-hidden text-xs font-semibold"
             style="border:1px solid var(--color-border);">
            @foreach (['7D' => '7 Days', '30D' => '30 Days', 'YTD' => 'Year'] as $key => $label)
                <button type="button"
                        class="px-3 py-1.5 transition-colors"
                        style="{{ $key === '7D'
                            ? 'background:var(--color-primary); color:var(--color-white);'
                            : 'background:var(--color-white); color:#555;' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Bar chart (pure CSS/SVG — no JS library required) --}}
    <div class="w-full h-64 md:h-80 overflow-x-auto">
        <svg viewBox="0 0 700 260" class="w-full h-full" preserveAspectRatio="xMidYMid meet"
             aria-label="Daily revenue bar chart for the past 7 days">

            @php
                $barW   = 54;
                $gap    = 46;
                $startX = 50;
                $chartH = 200;
                $topPad = 20;
                $yStep  = 4;  // number of grid lines
            @endphp

            {{-- Y-axis grid lines & labels --}}
            @for ($i = 0; $i <= $yStep; $i++)
                @php
                    $yVal = ($chartMax / $yStep) * ($yStep - $i);
                    $yPos = $topPad + ($chartH / $yStep) * $i;
                    $label = 'Rp ' . number_format($yVal / 1000, 0) . 'k';
                @endphp
                <line x1="45" y1="{{ $yPos }}" x2="695" y2="{{ $yPos }}"
                      stroke="#E5E7EB" stroke-width="1"/>
                <text x="40" y="{{ $yPos + 4 }}" text-anchor="end"
                      font-size="10" fill="#9ca3af">{{ $label }}</text>
            @endfor

            {{-- Bars + day labels --}}
            @foreach ($chartDays as $idx => $day)
                @php
                    $val    = $chartVals[$idx];
                    $barH   = ($val / $chartMax) * $chartH;
                    $xPos   = $startX + $idx * ($barW + $gap);
                    $yBar   = $topPad + $chartH - $barH;
                    $isMax  = ($val === $chartMax);
                @endphp

                {{-- Bar --}}
                <rect x="{{ $xPos }}" y="{{ $yBar }}"
                      width="{{ $barW }}" height="{{ $barH }}"
                      rx="6"
                      fill="{{ $isMax ? '#A6171C' : '#FFBE54' }}"
                      opacity="{{ $isMax ? '1' : '0.75' }}"/>

                {{-- Value label on top of bar --}}
                <text x="{{ $xPos + $barW / 2 }}" y="{{ $yBar - 5 }}"
                      text-anchor="middle" font-size="9" font-weight="600"
                      fill="{{ $isMax ? '#A6171C' : '#555' }}">
                    {{ number_format($val / 1000, 0) }}k
                </text>

                {{-- Day label --}}
                <text x="{{ $xPos + $barW / 2 }}" y="236"
                      text-anchor="middle" font-size="11" fill="#6b7280">
                    {{ $day }}
                </text>
            @endforeach

            {{-- X axis --}}
            <line x1="45" y1="{{ $topPad + $chartH }}" x2="695" y2="{{ $topPad + $chartH }}"
                  stroke="#E5E7EB" stroke-width="1.5"/>
        </svg>
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-4 mt-2 justify-end text-xs" style="color:#555;">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm" style="background:#A6171C;"></span> Highest day
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm" style="background:#FFBE54; opacity:0.75;"></span> Other days
        </span>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     TRANSACTION TABLE
════════════════════════════════════════════════════════ --}}
<div class="rounded-xl overflow-hidden"
     style="background:var(--color-white); box-shadow:var(--shadow-card);">

    {{-- Table header bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3"
         style="background:var(--color-primary);">
        <span class="font-bold text-white text-sm">Transaction History</span>

        {{-- Quick search --}}
        <div class="relative">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-60 pointer-events-none"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input id="tx-search"
                   type="search"
                   placeholder="Search transactions…"
                   oninput="filterTable()"
                   class="pl-7 pr-3 py-1.5 rounded-lg text-xs focus:outline-none"
                   style="background:rgba(255,255,255,0.15); color:white;
                          border:1px solid rgba(255,255,255,0.3);">
        </div>
    </div>

    {{-- Table (horizontal scroll on mobile) --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--color-border);">
                    @foreach (['Date', 'Order ID', 'Items', 'Total', 'Payment'] as $col)
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap"
                            style="color:#555;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="tx-body" class="divide-y" style="border-color:var(--color-border);">
                @foreach ($transactions as $tx)
                    <tr class="tx-row hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($tx['order_id'] . ' ' . $tx['items'] . ' ' . $tx['payment']) }}">

                        {{-- Date --}}
                        <td class="px-4 py-3 whitespace-nowrap text-xs" style="color:#555;">
                            {{ \Carbon\Carbon::parse($tx['date'])->format('d M Y') }}
                        </td>

                        {{-- Order ID --}}
                        <td class="px-4 py-3 font-semibold whitespace-nowrap"
                            style="color:var(--color-black);">
                            {{ $tx['order_id'] }}
                        </td>

                        {{-- Items (truncated on small screens) --}}
                        <td class="px-4 py-3 max-w-xs" style="color:#555;">
                            <span class="line-clamp-2 text-xs">{{ $tx['items'] }}</span>
                        </td>

                        {{-- Total --}}
                        <td class="px-4 py-3 font-semibold whitespace-nowrap"
                            style="color:var(--color-primary);">
                            Rp {{ number_format($tx['total'], 0, ',', '.') }}
                        </td>

                        {{-- Payment badge --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($tx['payment'] === 'Cash')
                                <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full"
                                      style="background:#FEF3C7; color:#D97706;">Cash</span>
                            @else
                                <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full"
                                      style="background:#DBEAFE; color:#2563EB;">QRIS</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Empty state --}}
        <p id="tx-empty" class="hidden text-center py-10 text-sm" style="color:#9c9c9c;">
            No transactions match your search.
        </p>
    </div>

    {{-- Table footer: row count + totals --}}
    <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3 text-xs"
         style="border-top:1px solid var(--color-border); color:#555;">
        <span id="tx-count">Showing {{ count($transactions) }} transactions</span>
        <span class="font-semibold" style="color:var(--color-primary);">
            Period total: Rp {{ number_format(array_sum(array_column($transactions, 'total')), 0, ',', '.') }}
        </span>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════ --}}
<script>
function filterTable() {
    var q    = document.getElementById('tx-search').value.toLowerCase();
    var rows = document.querySelectorAll('.tx-row');
    var vis  = 0;

    rows.forEach(function (row) {
        var show = row.dataset.search.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) vis++;
    });

    document.getElementById('tx-empty').classList.toggle('hidden', vis > 0);
    document.getElementById('tx-count').textContent = 'Showing ' + vis + ' transaction' + (vis !== 1 ? 's' : '');
}

function resetDates() {
    var now   = new Date();
    var y     = now.getFullYear();
    var m     = String(now.getMonth() + 1).padStart(2, '0');
    var d     = String(now.getDate()).padStart(2, '0');
    document.getElementById('date-start').value = y + '-' + m + '-01';
    document.getElementById('date-end').value   = y + '-' + m + '-' + d;
}
</script>

@endsection
