<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Owner financial report (figures from Completed orders only).
class ReportController extends Controller
{
    // Shared date-range parser.
    private function parseDateRange(): array
    {
        // HTML <input type="date"> always submits Y-m-d; str_replace handles
        // any slash-separated variant that might arrive from other clients.
        $startInput = str_replace('/', '-', request('start_date', Carbon::now()->startOfMonth()->toDateString()));
        $endInput   = str_replace('/', '-', request('end_date',   Carbon::now()->endOfMonth()->toDateString()));

        return [
            Carbon::parse($startInput)->startOfDay(),
            Carbon::parse($endInput)->endOfDay(),
        ];
    }

    // Shared base query: Completed orders with an optional payment join.
    private function completedOrders(Carbon $start, Carbon $end)
    {
        return DB::table('orders')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.status', 'Completed')
            ->whereBetween('orders.created_at', [$start, $end]);
    }

    // Show the report page.
    public function index()
    {
        $role = $this->currentRole();

        [$startDate, $endDate] = $this->parseDateRange();

        // Summary metrics
        $aggregate = $this->completedOrders($startDate, $endDate)
            ->selectRaw('SUM(orders.total_price) as total_revenue, COUNT(orders.id) as total_orders')
            ->first();

        $totalRevenue  = (int) ($aggregate->total_revenue ?? 0);
        $totalOrders   = (int) ($aggregate->total_orders  ?? 0);
        $avgOrderValue = $totalOrders > 0 ? intdiv($totalRevenue, $totalOrders) : 0;

        // Payment method breakdown (default unknown methods to Cash)
        $paymentSplit = $this->completedOrders($startDate, $endDate)
            ->selectRaw("COALESCE(payments.payment_method, 'Cash') as payment_method, SUM(orders.total_price) as revenue")
            ->groupByRaw("COALESCE(payments.payment_method, 'Cash')")
            ->pluck('revenue', 'payment_method');

        $revenueCash  = (int) ($paymentSplit->get('Cash',  0));
        $revenueQris  = (int) ($paymentSplit->get('QRIS',  0));
        $revenueDebit = (int) ($paymentSplit->get('Debit', 0));

        // Daily revenue — every day in range present (zero-filled)
        $dailyRevenues = $this->completedOrders($startDate, $endDate)
            ->selectRaw('DATE(orders.created_at) as date, SUM(orders.total_price) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date')
            ->map(fn($v) => (int) $v)
            ->all();

        // Daily order counts — zero-filled for every day in range
        $dailyOrderCounts = $this->completedOrders($startDate, $endDate)
            ->selectRaw('DATE(orders.created_at) as date, COUNT(orders.id) as order_count')
            ->groupBy('date')
            ->pluck('order_count', 'date')
            ->map(fn($v) => (int) $v)
            ->all();

        $chartLabels    = [];
        $chartData      = [];
        $chartOrderData = [];
        $chartDates     = [];

        foreach (\Carbon\CarbonPeriod::create($startDate->toDateString(), $endDate->toDateString()) as $day) {
            $chartLabels[]    = $day->format('d/m');
            $chartData[]      = $dailyRevenues[$day->format('Y-m-d')]    ?? 0;
            $chartOrderData[] = $dailyOrderCounts[$day->format('Y-m-d')] ?? 0;
            $chartDates[]     = $day->format('d M Y');
        }

        // Paginated transaction list
        $transactions = $this->completedOrders($startDate, $endDate)
            ->selectRaw("
                orders.id,
                orders.order_number,
                orders.total_price,
                orders.created_at,
                COALESCE(payments.payment_method, 'Cash') as payment_method,
                (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as items_count
            ")
            ->orderByDesc('orders.created_at')
            ->paginate(10)
            ->appends(request()->except('page'));

        $startDateStr = $startDate->toDateString();
        $endDateStr   = $endDate->toDateString();

        return view('owner.reports', compact(
            'role',
            'startDateStr',
            'endDateStr',
            'totalRevenue',
            'totalOrders',
            'avgOrderValue',
            'revenueCash',
            'revenueQris',
            'revenueDebit',
            'chartLabels',
            'chartData',
            'chartOrderData',
            'chartDates',
            'transactions'
        ));
    }

    // Return the revenue chart data as JSON.
    public function getChartData()
    {
        [$startDate, $endDate] = $this->parseDateRange();

        $daily = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->groupBy('date')
            ->pluck('revenue', 'date')
            ->map(fn($v) => (int) $v)
            ->all();

        $labels = [];
        $values = [];
        foreach (\Carbon\CarbonPeriod::create($startDate->toDateString(), $endDate->toDateString()) as $day) {
            $labels[] = $day->format('d/m');
            $values[] = $daily[$day->format('Y-m-d')] ?? 0;
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    // Export the report as a CSV file.
    public function export()
    {
        [$startDate, $endDate] = $this->parseDateRange();

        $filename = 'laporan_penjualan_corndogku_'
            . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $orders = $this->completedOrders($startDate, $endDate)
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.status',
                'orders.total_price',
                'orders.created_at',
                DB::raw("COALESCE(payments.payment_method, 'Cash') as payment_method"),
                DB::raw("COALESCE(users.name, 'Walk-in') as customer_name")
            )
            ->orderByDesc('orders.created_at')
            ->get();

        $orderIds     = $orders->pluck('id')->all();
        $itemsByOrder = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->select('order_id', 'product_name', 'quantity', 'subtotal', 'custom_notes')
            ->get()
            ->groupBy('order_id');

        $totalRevenue = $orders->sum('total_price');

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        $callback = function () use ($orders, $itemsByOrder, $totalRevenue, $startDate, $endDate) {
            $out = fopen('php://output', 'w');

            fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            fputcsv($out, ['LAPORAN PENJUALAN CORNDOG-KU']);
            fputcsv($out, ['Periode', $startDate->format('d/m/Y') . ' – ' . $endDate->format('d/m/Y')]);
            fputcsv($out, ['Dicetak', Carbon::now()->format('d/m/Y H:i')]);
            fputcsv($out, []);
            fputcsv($out, ['No', 'Tanggal', 'ID Pesanan', 'Pelanggan', 'Rincian Item', 'Metode Bayar', 'Status', 'Total']);

            $row = 1;
            foreach ($orders as $order) {
                $items = $itemsByOrder->get($order->id, collect());

                $itemSummary = $items->map(function ($item) {
                    $label = ($item->product_name ?? '—') . ' ×' . $item->quantity;
                    if (!empty($item->custom_notes)) {
                        $cn    = json_decode($item->custom_notes, true);
                        $parts = array_filter([
                            !empty($cn['isi'])    ? 'Isi: '    . $cn['isi']    : null,
                            !empty($cn['varian']) ? 'Varian: ' . $cn['varian'] : null,
                            !empty($cn['sauces']) ? 'Saos: '   . $cn['sauces'] : null,
                        ]);
                        if ($parts) {
                            $label .= ' [' . implode(', ', $parts) . ']';
                        }
                    }
                    return $label;
                })->implode(' | ');

                fputcsv($out, [
                    $row++,
                    Carbon::parse($order->created_at)->format('d/m/Y H:i'),
                    $order->order_number,
                    $order->customer_name,
                    $itemSummary ?: '-',
                    $order->payment_method,
                    $order->status,
                    $order->total_price,
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['', '', '', '', '', '', 'TOTAL PENDAPATAN', $totalRevenue]);
            fputcsv($out, ['', '', '', '', '', '', 'TOTAL TRANSAKSI',  $orders->count()]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Return a single order's details as JSON.
    public function orderDetail($id)
    {
        $order = DB::table('orders')
            ->leftJoin('users',    'orders.user_id', '=', 'users.id')
            ->leftJoin('payments', 'orders.id',      '=', 'payments.order_id')
            ->where('orders.id', $id)
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.total_price',
                'orders.status',
                'orders.order_type',
                'orders.created_at',
                DB::raw("COALESCE(users.name, 'Walk-in Customer') as customer_name"),
                DB::raw("COALESCE(payments.payment_method, 'Cash') as payment_method"),
                'payments.amount as paid_amount'
            )
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $items = DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $id)
            ->select(
                DB::raw("COALESCE(order_items.product_name, products.name, 'Custom Corndog') as product_name"),
                'order_items.quantity',
                'order_items.subtotal',
                'order_items.custom_notes'
            )
            ->get();

        return response()->json(['order' => $order, 'items' => $items]);
    }
}
