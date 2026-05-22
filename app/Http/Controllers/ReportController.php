<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $role = $this->currentRole();

        // Normalise date input: handle both YYYY-MM-DD and DD/MM/YYYY
        $startInput = str_replace('/', '-', request('start_date', Carbon::now()->startOfMonth()->toDateString()));
        $endInput   = str_replace('/', '-', request('end_date',   Carbon::now()->endOfMonth()->toDateString()));

        $startDate = Carbon::parse($startInput)->startOfDay();
        $endDate   = Carbon::parse($endInput)->endOfDay();

        // Top-level aggregates: all paid orders in range (pay-first model)
        $aggregate = DB::table('orders')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'Paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(orders.total_price) as total_revenue, COUNT(orders.id) as total_orders')
            ->first();

        $totalRevenue  = (int) ($aggregate->total_revenue ?? 0);
        $totalOrders   = (int) ($aggregate->total_orders  ?? 0);
        $avgOrderValue = $totalOrders > 0 ? intdiv($totalRevenue, $totalOrders) : 0;

        // Payment split: group by method, sum revenue
        $paymentSplit = DB::table('orders')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'Paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('payments.payment_method, SUM(orders.total_price) as revenue')
            ->groupBy('payments.payment_method')
            ->pluck('revenue', 'payment_method');

        $revenueCash  = (int) ($paymentSplit->get('Cash',  0));
        $revenueQris  = (int) ($paymentSplit->get('QRIS',  0));
        $revenueDebit = (int) ($paymentSplit->get('Debit', 0));

        // Chart: daily revenue for all paid orders
        $chartRaw = DB::table('orders')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'Paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(orders.created_at) as date, SUM(orders.total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $chartRaw->map(fn($r) => Carbon::parse($r->date)->format('d/m'))->values()->toArray();
        $chartData   = $chartRaw->map(fn($r) => (int) $r->revenue)->values()->toArray();

        // Transaction history: paginated, 10 per page
        $transactions = DB::table('orders')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'Paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                orders.id,
                orders.order_number,
                orders.total_price,
                orders.created_at,
                payments.payment_method,
                (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as items_count
            ')
            ->orderByDesc('orders.created_at')
            ->paginate(10)
            ->appends(request()->except('page'));

        // Pass date strings back to the view for the filter inputs
        $startDateStr = $startDate->toDateString();
        $endDateStr   = $endDate->toDateString();

        return view('reports.index', compact(
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
            'transactions'
        ));
    }

    public function orderDetail($id)
    {
        $order = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.id', $id)
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.total_price',
                'orders.status',
                'orders.order_type',
                'orders.created_at',
                'users.name as customer_name',
                'payments.payment_method',
                'payments.amount as paid_amount'
            )
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $items = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $id)
            ->select(
                'products.name as product_name',
                'order_items.quantity',
                'order_items.subtotal',
                'order_items.custom_notes'
            )
            ->get();

        return response()->json(['order' => $order, 'items' => $items]);
    }
}
