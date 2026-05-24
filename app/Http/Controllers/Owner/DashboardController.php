<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role        = 'owner';
        $storeStatus = Cache::get('store_status', 'available');

        $rawDate      = $request->query('date', today()->toDateString());
        $selectedDate = Carbon::parse($rawDate)->min(today())->toDateString();

        $orders = $revenueToday = $revenueGrowth = $totalOrders =
                  $onlineOrders = $cashierOrders = $pendingOrders = null;

        try {
            $dbOrders = Order::with(['user', 'items.product', 'payment'])
                ->whereDate('created_at', $selectedDate)
                ->latest()
                ->get();

            $revenueToday  = (int) $dbOrders->where('status', 'Completed')->sum('total_price');
            $totalOrders   = $dbOrders->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->count();
            $pendingOrders = $dbOrders->where('status', 'Pending')->count();

            $yesterday        = Carbon::parse($selectedDate)->subDay()->toDateString();
            $revenueYesterday = Order::whereDate('created_at', $yesterday)
                ->where('status', 'Completed')
                ->sum('total_price');
            $revenueGrowth = $revenueYesterday > 0
                ? (int) round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100)
                : ($revenueToday > 0 ? 100 : 0);

            if ($dbOrders->isNotEmpty()) {
                $orders = $dbOrders->map(function ($o) {
                    $source       = $o->order_type === 'online' ? 'online' : 'cashier';
                    $payment      = $o->payment?->payment_method ?? 'Cash';
                    $itemsSummary = $o->items->map(
                        fn($i) => $i->quantity . '× ' . ($i->product_name ?? $i->product?->name ?? 'Item')
                    )->implode(', ');

                    $orderItems = $o->items->map(fn($i) => [
                        'name'     => $i->product_name ?? $i->product?->name ?? 'Item',
                        'variant'  => $i->custom_notes ?? '',
                        'price'    => $i->quantity > 0 ? (int) ($i->subtotal / $i->quantity) : 0,
                        'qty'      => $i->quantity,
                        'subtotal' => $i->subtotal,
                        'img'      => $i->product?->image ?? '',
                    ])->values()->toArray();

                    return (object) [
                        'db_id'       => $o->id,
                        'id'          => '#' . $o->order_number,
                        'is_new'      => $o->created_at->gte(now()->subMinutes(30)),
                        'customer'    => $o->user?->name ?? 'Walk-in Customer',
                        'sub'         => $o->user ? 'Customer' : '',
                        'source'      => $source,
                        'items'       => $itemsSummary ?: '-',
                        'status'      => $o->status,
                        'time'        => $o->created_at->format('h:i A'),
                        'total'       => $o->total_price,
                        'order_type'  => $o->order_type,
                        'payment'     => $payment,
                        'order_items' => $orderItems,
                    ];
                });
            }
        } catch (\Exception $e) {
            // DB not ready — view will use mock stubs
        }

        $chartData   = null;
        $profitToday = null;

        try {
            $weekStart = Carbon::parse($selectedDate)->startOfWeek(Carbon::SUNDAY);
            $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);

            $rawChart = Order::selectRaw('DAYOFWEEK(created_at) as dow, SUM(total_price) as rev')
                ->whereDate('created_at', '>=', $weekStart->toDateString())
                ->whereDate('created_at', '<=', $weekEnd->toDateString())
                ->where('status', 'Completed')
                ->groupByRaw('DAYOFWEEK(created_at)')
                ->pluck('rev', 'dow');

            $chartData = [];
            foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $i => $label) {
                $chartData[] = ['label' => $label, 'value' => (int) ($rawChart[$i + 1] ?? 0)];
            }

            $totalCostToday = isset($dbOrders)
                ? $dbOrders->whereNotIn('status', ['Cancelled'])
                    ->flatMap(fn ($o) => $o->items)
                    ->sum(fn ($item) => $item->quantity * ($item->product?->cost_price ?? 0))
                : 0;
            $profitToday = max(0, (int) ($revenueToday - $totalCostToday));
        } catch (\Exception $e) {
            // DB not ready — view will use mock stubs
        }

        // Low stock: products with stock at or below threshold, ordered ascending so most critical first
        $lowStockItems = [];
        try {
            $lowStockItems = Product::where('is_custom', false)
                ->where('stock', '<=', 10)
                ->orderBy('stock')
                ->take(8)
                ->get(['name', 'stock'])
                ->map(fn($p) => ['name' => $p->name, 'qty' => $p->stock . ' pcs'])
                ->toArray();
        } catch (\Exception $e) {
            // DB not ready — widget shows empty list
        }

        return view('owner.dashboard', compact(
            'role', 'storeStatus', 'selectedDate',
            'orders', 'revenueToday', 'revenueGrowth',
            'totalOrders', 'onlineOrders', 'cashierOrders', 'pendingOrders',
            'chartData', 'profitToday', 'lowStockItems'
        ));
    }

    public function getOrders(Request $request)
    {
        $status  = $request->query('status', 'all');
        $page    = max(1, (int) $request->query('page', 1));
        $rawDate = $request->query('date', today()->toDateString());
        $date    = Carbon::parse($rawDate)->min(today())->toDateString();

        try {
            $allRows = Order::whereDate('created_at', $date)->get(['status']);
            $counts  = [
                'all'       => $allRows->count(),
                'Pending'   => $allRows->where('status', 'Pending')->count(),
                'Preparing' => $allRows->where('status', 'Preparing')->count(),
                'Ready'     => $allRows->where('status', 'Ready')->count(),
                'Completed' => $allRows->where('status', 'Completed')->count(),
                'Cancelled' => $allRows->where('status', 'Cancelled')->count(),
            ];

            $query = Order::with(['user', 'items.product', 'payment'])
                ->whereDate('created_at', $date)
                ->latest();

            if ($status !== 'all') {
                $query->where('status', ucfirst(strtolower($status)));
            }

            $paginated = $query->paginate(10, ['*'], 'page', $page);

            $items = $paginated->getCollection()->map(function ($o) {
                $source       = $o->order_type === 'online' ? 'online' : 'cashier';
                $payment      = $o->payment?->payment_method ?? 'Cash';
                $itemsSummary = $o->items->map(
                    fn($i) => $i->quantity . '× ' . ($i->product_name ?? $i->product?->name ?? 'Item')
                )->implode(', ');

                $orderItems = $o->items->map(fn($i) => [
                    'name'     => $i->product_name ?? $i->product?->name ?? 'Item',
                    'variant'  => $i->custom_notes ?? '',
                    'price'    => $i->quantity > 0 ? (int) ($i->subtotal / $i->quantity) : 0,
                    'qty'      => $i->quantity,
                    'subtotal' => $i->subtotal,
                    'img'      => $i->product?->image ?? '',
                ])->values()->toArray();

                return [
                    'db_id'       => $o->id,
                    'id'          => '#' . $o->order_number,
                    'is_new'      => $o->created_at->gte(now()->subMinutes(30)),
                    'customer'    => $o->user?->name ?? 'Walk-in Customer',
                    'sub'         => $o->user ? 'Customer' : '',
                    'source'      => $source,
                    'items'       => $itemsSummary ?: '-',
                    'status'      => $o->status,
                    'time'        => $o->created_at->format('h:i A'),
                    'total'       => $o->total_price,
                    'order_type'  => $o->order_type,
                    'payment'     => $payment,
                    'order_items' => $orderItems,
                ];
            })->values();

            return response()->json([
                'items'        => $items,
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
                'counts'       => $counts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'items'        => [],
                'current_page' => 1,
                'last_page'    => 1,
                'total'        => 0,
                'counts'       => ['all'=>0,'Pending'=>0,'Preparing'=>0,'Ready'=>0,'Completed'=>0,'Cancelled'=>0],
            ]);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['status' => ['required', 'in:available,unavailable']]);
        Cache::put('store_status', $request->status, now()->addHours(24));
        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function getStats(Request $request)
    {
        $rawDate = $request->query('date', today()->toDateString());
        $date    = Carbon::parse($rawDate)->min(today())->toDateString();

        try {
            $dbOrders = Order::selectRaw('total_price, status, order_type')
                ->whereDate('created_at', $date)
                ->get();

            $revenueToday  = (int) $dbOrders->where('status', 'Completed')->sum('total_price');
            $totalOrders   = $dbOrders->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->count();
            $pendingOrders = $dbOrders->where('status', 'Pending')->count();

            $totalCostToday = (int) DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereDate('orders.created_at', $date)
                ->where('orders.status', 'Completed')
                ->sum(DB::raw('order_items.quantity * products.cost_price'));
            $profitToday = max(0, (int) ($revenueToday - $totalCostToday));

            $yesterday        = Carbon::parse($date)->subDay()->toDateString();
            $revenueYesterday = (int) Order::whereDate('created_at', $yesterday)
                ->where('status', 'Completed')->sum('total_price');
            $revenueGrowth = $revenueYesterday > 0
                ? (int) round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100)
                : ($revenueToday > 0 ? 100 : 0);

            return response()->json([
                'revenue'       => $revenueToday,
                'totalOrders'   => $totalOrders,
                'onlineOrders'  => $onlineOrders,
                'cashierOrders' => $cashierOrders,
                'pendingOrders' => $pendingOrders,
                'profit'        => $profitToday,
                'growth'        => $revenueGrowth,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'revenue'=>0,'totalOrders'=>0,'onlineOrders'=>0,
                'cashierOrders'=>0,'pendingOrders'=>0,'profit'=>0,'growth'=>0,
            ]);
        }
    }
}
