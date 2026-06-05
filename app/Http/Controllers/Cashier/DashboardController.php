<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ManagesOrderStatus;

    /**
     * Cashier-only: notify the customer over WhatsApp when their order is marked
     * Ready, or Cancelled with a reason. The owner board leaves this a no-op.
     */
    protected function dispatchOrderStatusNotification(Order $order, string $status, ?string $reason): void
    {
        if ($status === 'Ready') {
            $this->notifyOrderStatusViaWhatsApp($order, 'Ready');
        } elseif ($status === 'Cancelled' && filled($reason)) {
            $this->notifyOrderStatusViaWhatsApp($order, 'Cancelled', $reason);
        }
    }

    public function index(Request $request)
    {
        $role        = 'cashier';
        $storeStatus = Cache::get('store_status', 'available');

        $rawDate      = $request->query('date', today()->toDateString());
        $selectedDate = Carbon::parse($rawDate)->min(today())->toDateString();

        $orders = $revenueToday = $revenueGrowth = $totalOrders =
                  $onlineOrders = $cashierOrders = $pendingOrders = null;

        try {
            $dbOrders = Order::with(['user', 'cashier', 'items.product', 'payment'])
                ->whereDate('created_at', $selectedDate)
                ->latest()
                ->get();

            // Revenue counts only Completed orders (money actually realized).
            $revenueToday  = (int) $dbOrders->where('status', 'Completed')->sum('total_price');
            $totalOrders   = $dbOrders->whereNotIn('status', ['Cancelled'])->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->whereNotIn('status', ['Cancelled'])->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->whereNotIn('status', ['Cancelled'])->count();
            $pendingOrders = $dbOrders->where('status', 'Pending')->count();

            $yesterday        = Carbon::parse($selectedDate)->subDay()->toDateString();
            $revenueYesterday = Order::whereDate('created_at', $yesterday)
                ->where('status', 'Completed')
                ->sum('total_price');
            $revenueGrowth = $revenueYesterday > 0
                ? (int) round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100)
                : ($revenueToday > 0 ? 100 : 0);

            // Initial server-rendered table from the already-fetched $dbOrders
            // (no extra query). The on-load AJAX getOrders() is authoritative and
            // applies the active-order carryover.
            if ($dbOrders->isNotEmpty()) {
                $orders = $dbOrders->map(function ($o) {
                    $source       = $o->order_type === 'online' ? 'online' : 'cashier';
                    $payment      = $o->payment?->payment_method ?? 'Cash';
                    $itemsSummary = $o->items->map(
                        fn($i) => $i->quantity . '× ' . ($i->product_name ?? $i->product?->name ?? 'Item')
                    )->implode(', ');

                    $orderItems = $this->mapOrderItems($o);

                    return (object) [
                        'db_id'=> $o->id,
                        'id'  => '#' . $o->order_number,
                        'is_new' => $o->created_at->gte(now()->subMinutes(30)),
                        'customer'=> $o->user?->name ?? 'Walk-in Customer',
                        'phone'=> $o->user?->phone ?? $o->customer_phone,
                        'sub' => $o->user ? 'Customer' : '',
                        'source' => $source,
                        'items'=> $itemsSummary ?: '-',
                        'status' => $o->status,
                        'time' => $o->created_at->format('h:i A'),
                        'date' => $o->created_at->translatedFormat('j M Y'),
                        'total' => $o->total_price,
                        'order_type' => $o->order_type,
                        'payment'=> $payment,
                        'order_items'=> $orderItems,
                        'cashier_name'=> $o->cashier?->name ?? null,
                        'cancellation_reason' => $o->cancellation_reason,
                    ];
                });
            }
        } catch (\Exception $e) {
            // DB not ready — view will use mock stubs
        }

        $chartData = null;

        try {
            $weekStart = Carbon::parse($selectedDate)->startOfWeek(Carbon::MONDAY);
            $weekEnd   = $weekStart->copy()->addDays(6);

            $rawChart = Order::selectRaw('DAYOFWEEK(created_at) as dow, SUM(total_price) as rev')
                ->whereDate('created_at', '>=', $weekStart->toDateString())
                ->whereDate('created_at', '<=', $weekEnd->toDateString())
                ->where('status', 'Completed')
                ->groupByRaw('DAYOFWEEK(created_at)')
                ->pluck('rev', 'dow');

            // MySQL DAYOFWEEK: 1=Sun, 2=Mon, 3=Tue, 4=Wed, 5=Thu, 6=Fri, 7=Sat
            $chartData = [];
            foreach ([['Mon',2],['Tue',3],['Wed',4],['Thu',5],['Fri',6],['Sat',7],['Sun',1]] as [$label, $dow]) {
                $chartData[] = ['label' => $label, 'value' => (int) ($rawChart[$dow] ?? 0)];
            }
        } catch (\Exception $e) {
            // DB not ready — view will use mock stubs
        }

        return view('cashier.dashboard', compact(
            'role', 'storeStatus', 'selectedDate',
            'orders', 'revenueToday', 'revenueGrowth',
            'totalOrders', 'onlineOrders', 'cashierOrders', 'pendingOrders',
            'chartData'
        ));
    }

    public function getOrders(Request $request)
    {
        $status  = $request->query('status', 'all');
        $page    = max(1, (int) $request->query('page', 1));
        $rawDate = $request->query('date', today()->toDateString());
        $date    = Carbon::parse($rawDate)->min(today())->toDateString();

        try {
            $counts = $this->orderTabCounts($date);

            // List = today's orders + still-active carryover (see ordersForDay).
            $query = $this->ordersForDay(
                Order::with(['user', 'cashier', 'items.product', 'payment']),
                $date
            )->latest();

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

                $orderItems = $this->mapOrderItems($o);

                return [
                    'db_id'=> $o->id,
                    'id' => '#' . $o->order_number,
                    'is_new' => $o->created_at->gte(now()->subMinutes(30)),
                    'customer'=> $o->user?->name ?? 'Walk-in Customer',
                    'sub'=> $o->user ? 'Customer' : '',
                    'phone'=> $o->user?->phone ?? $o->customer_phone,
                    'source'  => $source,
                    'items' => $itemsSummary ?: '-',
                    'status' => $o->status,
                    'time' => $o->created_at->format('h:i A'),
                    'date' => $o->created_at->translatedFormat('j M Y'),
                    'total' => $o->total_price,
                    'order_type'          => $o->order_type,
                    'payment'=> $payment,
                    'order_items'=> $orderItems,
                    'cashier_name' => $o->cashier?->name ?? null,
                    'cancellation_reason' => $o->cancellation_reason,
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

            // Revenue & profit count only Completed orders (money actually realized).
            $revenueToday  = (int) $dbOrders->where('status', 'Completed')->sum('total_price');
            $totalOrders   = $dbOrders->whereNotIn('status', ['Cancelled'])->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->whereNotIn('status', ['Cancelled'])->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->whereNotIn('status', ['Cancelled'])->count();
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

    public function getChartData(Request $request)
    {
        $rawDate = $request->query('date', today()->toDateString());
        $date    = Carbon::parse($rawDate)->min(today())->toDateString();

        try {
            $weekStart = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
            $weekEnd   = $weekStart->copy()->addDays(6);

            $rawChart = Order::selectRaw('DAYOFWEEK(created_at) as dow, SUM(total_price) as rev')
                ->whereDate('created_at', '>=', $weekStart->toDateString())
                ->whereDate('created_at', '<=', $weekEnd->toDateString())
                ->where('status', 'Completed')
                ->groupByRaw('DAYOFWEEK(created_at)')
                ->pluck('rev', 'dow');

            $labels = [];
            $values = [];
            foreach ([['Mon',2],['Tue',3],['Wed',4],['Thu',5],['Fri',6],['Sat',7],['Sun',1]] as [$label, $dow]) {
                $labels[] = $label;
                $values[] = (int) ($rawChart[$dow] ?? 0);
            }

            return response()->json(['labels' => $labels, 'values' => $values]);
        } catch (\Exception $e) {
            return response()->json([
                'labels' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                'values' => [0,0,0,0,0,0,0],
            ]);
        }
    }

}
