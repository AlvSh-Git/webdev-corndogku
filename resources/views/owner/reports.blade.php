@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')

{{-- PAGE HEADER --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-3xl md:text-4xl font-bold" style="color:var(--color-black);">Sales Report</h1>
        <p class="text-sm mt-1" style="color:#555;">Track revenue, orders, and transaction history.</p>
    </div>

    {{-- Export CSV button --}}
    <a href="{{ route('owner.reports.export', ['start_date' => $startDateStr, 'end_date' => $endDateStr]) }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-full font-semibold text-sm
              transition-opacity hover:opacity-80 min-h-[44px]"
       style="background:#059669; color:#fff;">
        <svg class="w-4 h-4 flex-none" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export Laporan (.csv)
    </a>
</div>

{{-- DATE FILTER --}}
<form method="GET" action="{{ route('owner.reports') }}">
    <div class="flex flex-col sm:flex-row sm:items-end gap-3 mb-6 p-4 rounded-xl"
         style="background:var(--color-white); box-shadow:var(--shadow-card);">

        <div class="flex-1 min-w-0">
            <label class="block text-xs font-semibold mb-1" style="color:#555;">Start Date</label>
            <input type="date"
                   name="start_date"
                   id="date-start"
                   value="{{ request('start_date', $startDateStr) }}"
                   class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none"
                   style="border:1px solid var(--color-border);">
        </div>

        <div class="flex-1 min-w-0">
            <label class="block text-xs font-semibold mb-1" style="color:#555;">End Date</label>
            <input type="date"
                   name="end_date"
                   id="date-end"
                   value="{{ request('end_date', $endDateStr) }}"
                   class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none"
                   style="border:1px solid var(--color-border);">
        </div>

        <div class="flex gap-2 shrink-0">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-opacity hover:opacity-80"
                    style="background:var(--color-primary); color:var(--color-white);">
                Apply Filter
            </button>
            <a href="{{ route('owner.reports') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl font-semibold text-sm transition-opacity hover:opacity-70"
               style="border:1px solid var(--color-border); color:#555;">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Revenue --}}
    <div class="rounded-xl p-4 flex items-start gap-3"
         style="background:var(--color-white); box-shadow:var(--shadow-card);">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
             style="background-color:#A6171C1a;">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                 stroke="#A6171C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs" style="color:#9c9c9c;">Total Revenue</p>
            <p class="font-bold text-lg leading-tight" style="color:var(--color-black);">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-0.5" style="color:#9c9c9c;">Selected period</p>
        </div>
    </div>

    {{-- Total Orders --}}
    <div class="rounded-xl p-4 flex items-start gap-3"
         style="background:var(--color-white); box-shadow:var(--shadow-card);">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
             style="background-color:#4A90D91a;">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                 stroke="#4A90D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs" style="color:#9c9c9c;">Total Orders</p>
            <p class="font-bold text-lg leading-tight" style="color:var(--color-black);">
                {{ number_format($totalOrders, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-0.5" style="color:#9c9c9c;">Selected period</p>
        </div>
    </div>

    {{-- Avg Order Value --}}
    <div class="rounded-xl p-4 flex items-start gap-3"
         style="background:var(--color-white); box-shadow:var(--shadow-card);">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
             style="background-color:#27AE601a;">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                 stroke="#27AE60" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3v18h18M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs" style="color:#9c9c9c;">Avg Order Value</p>
            <p class="font-bold text-lg leading-tight" style="color:var(--color-black);">
                Rp {{ number_format($avgOrderValue, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-0.5" style="color:#9c9c9c;">Per transaction</p>
        </div>
    </div>

    {{-- Payment Split: Cash / QRIS / Debit --}}
    <div class="rounded-xl p-4 flex items-start gap-3"
         style="background:var(--color-white); box-shadow:var(--shadow-card);">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
             style="background-color:#8E44AD1a;">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                 stroke="#8E44AD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs" style="color:#9c9c9c;">Cash / QRIS / Debit</p>
            <div class="mt-1 space-y-0.5">
                <div class="text-sm font-bold leading-tight" style="color:var(--color-black);">
                    <span style="color:#D97706;">C:</span> Rp {{ number_format($revenueCash, 0, ',', '.') }}
                </div>
                <div class="text-sm font-bold leading-tight" style="color:var(--color-black);">
                    <span style="color:#2563EB;">Q:</span> Rp {{ number_format($revenueQris, 0, ',', '.') }}
                </div>
                <div class="text-sm font-bold leading-tight" style="color:var(--color-black);">
                    <span style="color:#059669;">D:</span> Rp {{ number_format($revenueDebit, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SALES CHART --}}
<div class="rounded-xl p-5 mb-6"
     style="background:var(--color-white); box-shadow:var(--shadow-card);">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <h2 class="font-bold text-base" style="color:var(--color-black);">Daily Revenue</h2>
        <span id="chart-period-label" class="text-xs px-3 py-1 rounded-lg font-semibold"
              style="background:var(--color-primary); color:var(--color-white);">
            {{ \Carbon\Carbon::parse($startDateStr)->format('d M') }} – {{ \Carbon\Carbon::parse($endDateStr)->format('d M Y') }}
        </span>
    </div>

    <div style="position:relative;height:280px;">
        <canvas id="daily-revenue-chart"></canvas>
    </div>
</div>

{{-- TRANSACTION TABLE --}}
<div class="rounded-xl overflow-hidden"
     style="background:var(--color-white); box-shadow:var(--shadow-card);">

    {{-- Table header bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3"
         style="background:var(--color-primary);">
        <span class="font-bold text-white text-sm">Transaction History</span>
        <span class="text-xs text-white opacity-75">
            {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
        </span>
    </div>

    {{-- Table (horizontal scroll on mobile) --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--color-border);">
                    @foreach (['Date', 'Order ID', 'Items', 'Total', 'Payment', 'Action'] as $col)
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap"
                            style="color:#555;">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="transactions-table-body" class="divide-y" style="border-color:var(--color-border);">
                @forelse ($transactions as $tx)
                    @php
                        $method = $tx->payment_method ?? 'Cash';
                        $badgeBg = match($method) {
                            'QRIS'  => '#DBEAFE',
                            'Debit' => '#D1FAE5',
                            default => '#FEF3C7',
                        };
                        $badgeColor = match($method) {
                            'QRIS'  => '#2563EB',
                            'Debit' => '#059669',
                            default => '#D97706',
                        };
                    @endphp
                    <tr class="tx-row hover:bg-gray-50 transition-colors">

                        {{-- Date --}}
                        <td class="px-4 py-3 whitespace-nowrap text-xs" style="color:#555;">
                            {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y') }}
                        </td>

                        {{-- Order ID --}}
                        <td class="px-4 py-3 font-semibold whitespace-nowrap"
                            style="color:var(--color-black);">
                            #{{ $tx->order_number }}
                        </td>

                        {{-- Items count --}}
                        <td class="px-4 py-3 text-xs" style="color:#555;">
                            {{ $tx->items_count }} item{{ $tx->items_count != 1 ? 's' : '' }}
                        </td>

                        {{-- Total --}}
                        <td class="px-4 py-3 font-semibold whitespace-nowrap"
                            style="color:var(--color-primary);">
                            Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                        </td>

                        {{-- Payment badge --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full"
                                  style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                {{ $method }}
                            </span>
                        </td>

                        {{-- Action: View Detail --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button class="btn-view-detail inline-flex items-center gap-1.5 px-3 py-1.5
                                           rounded-lg text-xs font-semibold transition-all hover:opacity-80
                                           focus:outline-none"
                                    data-id="{{ $tx->id }}"
                                    style="background:#F3F4F6; color:#374151;">
                                <svg class="w-3.5 h-3.5 pointer-events-none" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm" style="color:#9c9c9c;">
                            No transactions found for the selected period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div id="table-pagination-container"
         class="px-5 py-3 flex items-center justify-between gap-4 flex-wrap"
         style="border-top:1px solid var(--color-border);">
        <span class="text-xs" style="color:#555;">
            Period total: <span class="font-semibold" style="color:var(--color-primary);">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </span>
        </span>
        <div class="text-xs [&_nav]:flex [&_nav]:items-center [&_nav]:gap-1
                    [&_a]:px-2.5 [&_a]:py-1 [&_a]:rounded-lg [&_a]:font-medium [&_a]:transition-colors
                    [&_span[aria-current]]:px-2.5 [&_span[aria-current]]:py-1 [&_span[aria-current]]:rounded-lg [&_span[aria-current]]:font-semibold"
             style="--pg-active-bg:var(--color-primary); --pg-active-color:var(--color-white);
                    --pg-border:1px solid var(--color-border);">
            {{ $transactions->links() }}
        </div>
    </div>
</div>


{{-- ORDER DETAIL MODAL --}}
<div id="modal-order-detail"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);">

    <div class="relative w-full max-w-lg rounded-2xl overflow-hidden"
         style="background:var(--color-white); box-shadow:0 20px 60px rgba(0,0,0,0.25); max-height:90vh; display:flex; flex-direction:column;">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 shrink-0"
             style="background:var(--color-primary);">
            <div>
                <p class="text-xs text-white opacity-75 uppercase tracking-wide">Order Receipt</p>
                <p id="modal-order-number" class="text-white font-bold text-lg leading-tight">#—</p>
            </div>
            <button id="modal-close"
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-colors hover:bg-white/20"
                    style="color:white;">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

            {{-- Meta row --}}
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-semibold mb-0.5" style="color:#9c9c9c;">Customer</p>
                    <p id="modal-customer" class="font-medium" style="color:var(--color-black);">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold mb-0.5" style="color:#9c9c9c;">Payment Method</p>
                    <p id="modal-payment-method" class="font-medium" style="color:var(--color-black);">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold mb-0.5" style="color:#9c9c9c;">Date</p>
                    <p id="modal-date" class="font-medium" style="color:var(--color-black);">—</p>
                </div>
                <div>
                    <p class="text-xs font-semibold mb-0.5" style="color:#9c9c9c;">Order Type</p>
                    <p id="modal-order-type" class="font-medium capitalize" style="color:var(--color-black);">—</p>
                </div>
            </div>

            {{-- Divider --}}
            <hr style="border-color:var(--color-border);">

            {{-- Items table --}}
            <div>
                <p class="text-xs font-semibold mb-2" style="color:#9c9c9c;">Items Ordered</p>
                <div id="modal-items-loading" class="text-center py-6 text-sm hidden" style="color:#9c9c9c;">
                    Loading…
                </div>
                <table class="w-full text-sm" id="modal-items-table">
                    <thead>
                        <tr style="border-bottom:1px solid var(--color-border);">
                            <th class="py-2 text-left text-xs font-semibold" style="color:#555;">Product</th>
                            <th class="py-2 text-center text-xs font-semibold" style="color:#555;">Qty</th>
                            <th class="py-2 text-right text-xs font-semibold" style="color:#555;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modal-items-body" class="divide-y" style="border-color:var(--color-border);">
                        {{-- populated by JS --}}
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Footer: Grand Total --}}
        <div class="shrink-0 px-6 py-4 flex items-center justify-between"
             style="border-top:2px solid var(--color-border); background:#FAFAFA;">
            <span class="text-sm font-semibold" style="color:#555;">Grand Total</span>
            <span id="modal-grand-total" class="text-xl font-bold" style="color:var(--color-primary);">Rp —</span>
        </div>
    </div>
</div>


{{-- JAVASCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(function () {

    //  Helpers 
    function fmtRp(n) {
        return 'Rp ' + parseInt(n).toLocaleString('id-ID');
    }
    function fmtDate(str) {
        var d = new Date(str);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    //  Daily Revenue Chart 
    const dailyChart = new Chart(document.getElementById('daily-revenue-chart'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Revenue',
                data: @json($chartData),
                borderColor: '#A6171C',
                borderWidth: 2.5,
                fill: false,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#A6171C',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#A6171C',
                pointHoverBorderColor: '#ffffff',
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
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9CA3AF' } },
                y: {
                    grid: { color: '#E5E7EB' },
                    ticks: {
                        font: { size: 10 }, color: '#9CA3AF', maxTicksLimit: 5,
                        callback: v => Number(v).toLocaleString('id-ID'),
                    },
                    beginAtZero: true,
                    min: 0,
                }
            }
        }
    });

    function fetchReportChart() {
        var start = $('#date-start').val();
        var end   = $('#date-end').val();
        if (!start || !end) return;

        $.get('{{ route("owner.reports.chart-data") }}', { start_date: start, end_date: end })
            .done(function (res) {
                dailyChart.data.labels           = res.labels;
                dailyChart.data.datasets[0].data = res.values;
                dailyChart.update();
            });
    }

    $('#date-start, #date-end').on('change', function () { fetchReportChart(); });

    //  Order Detail Modal 
    var $modal = $('#modal-order-detail');

    $(document).on('click', '.btn-view-detail', function () {
        var id = $(this).data('id');

        // Show modal with loading state
        $('#modal-order-number').text('#…');
        $('#modal-customer').text('…');
        $('#modal-payment-method').text('…');
        $('#modal-date').text('…');
        $('#modal-order-type').text('…');
        $('#modal-grand-total').text('Rp …');
        $('#modal-items-body').empty();
        $('#modal-items-loading').removeClass('hidden');
        $('#modal-items-table').hide();
        $modal.removeClass('hidden');

        $.ajax({
            url: '/owner/reports/order/' + id + '/detail',
            type: 'GET',
            success: function (res) {
                var o = res.order;

                $('#modal-order-number').text('#' + o.order_number);
                $('#modal-customer').text(o.customer_name || 'Walk-in Customer');
                $('#modal-payment-method').text(o.payment_method || '—');
                $('#modal-date').text(fmtDate(o.created_at));
                $('#modal-order-type').text(o.order_type || '—');
                $('#modal-grand-total').text(fmtRp(o.total_price));

                var rows = '';
                if (res.items.length === 0) {
                    rows = '<tr><td colspan="3" class="py-4 text-center text-xs" style="color:#9c9c9c;">No items found.</td></tr>';
                } else {
                    $.each(res.items, function (_, item) {
                        rows += '<tr>' +
                            '<td class="py-2 pr-2" style="color:var(--color-black);">' +
                                '<span class="font-medium">' + item.product_name + '</span>' +
                                (item.custom_notes ? '<br><span class="text-xs" style="color:#9c9c9c;">' + item.custom_notes + '</span>' : '') +
                            '</td>' +
                            '<td class="py-2 text-center text-xs font-semibold" style="color:#555;">× ' + item.quantity + '</td>' +
                            '<td class="py-2 text-right font-semibold whitespace-nowrap" style="color:var(--color-primary);">' + fmtRp(item.subtotal) + '</td>' +
                        '</tr>';
                    });
                }

                $('#modal-items-body').html(rows);
                $('#modal-items-loading').addClass('hidden');
                $('#modal-items-table').show();
            },
            error: function () {
                $('#modal-items-body').html(
                    '<tr><td colspan="3" class="py-4 text-center text-xs" style="color:#A6171C;">Failed to load order details.</td></tr>'
                );
                $('#modal-items-loading').addClass('hidden');
                $('#modal-items-table').show();
            }
        });
    });

    // Close modal
    $('#modal-close, #modal-order-detail').on('click', function (e) {
        if (e.target === this) $modal.addClass('hidden');
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') $modal.addClass('hidden');
    });

    //  AJAX Pagination 
    $(document).on('click', '#table-pagination-container a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (!url || url === '#') return;

        // Append date filters so pagination carries the active filter
        var startDate = $('#date-start').val();
        var endDate   = $('#date-end').val();
        if (startDate) url += (url.includes('?') ? '&' : '?') + 'start_date=' + startDate;
        if (endDate)   url += '&end_date=' + endDate;

        // Visual feedback
        $('#transactions-table-body').css('opacity', '0.4');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                var $resp = $(response);
                $('#transactions-table-body').html($resp.find('#transactions-table-body').html()).css('opacity', '1');
                $('#table-pagination-container').html($resp.find('#table-pagination-container').html());
            },
            error: function () {
                $('#transactions-table-body').css('opacity', '1');
            }
        });
    });

});
</script>

@endsection
