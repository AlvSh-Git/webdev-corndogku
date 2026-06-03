<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
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

            $revenueToday  = (int) $dbOrders->whereNotIn('status', ['Cancelled'])->sum('total_price');
            $totalOrders   = $dbOrders->whereNotIn('status', ['Cancelled'])->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->whereNotIn('status', ['Cancelled'])->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->whereNotIn('status', ['Cancelled'])->count();
            $pendingOrders = $dbOrders->where('status', 'Pending')->count();

            $yesterday        = Carbon::parse($selectedDate)->subDay()->toDateString();
            $revenueYesterday = Order::whereDate('created_at', $yesterday)
                ->whereNotIn('status', ['Cancelled'])
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
                        'img'      => $i->product?->image ? asset($i->product->image) : '',
                    ])->values()->toArray();

                    return (object) [
                        'db_id'=> $o->id,
                        'id'  => '#' . $o->order_number,
                        'is_new' => $o->created_at->gte(now()->subMinutes(30)),
                        'customer'=> $o->user?->name ?? 'Walk-in Customer',
                        'phone'=> $o->customer_phone,
                        'sub' => $o->user ? 'Customer' : '',
                        'source' => $source,
                        'items'=> $itemsSummary ?: '-',
                        'status' => $o->status,
                        'time' => $o->created_at->format('h:i A'),
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
                ->whereNotIn('status', ['Cancelled'])
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
            $allRows = Order::whereDate('created_at', $date)->get(['status']);
            $counts  = [
                'all'       => $allRows->count(),
                'Pending'   => $allRows->where('status', 'Pending')->count(),
                'Preparing' => $allRows->where('status', 'Preparing')->count(),
                'Ready'     => $allRows->where('status', 'Ready')->count(),
                'Completed' => $allRows->where('status', 'Completed')->count(),
                'Cancelled' => $allRows->where('status', 'Cancelled')->count(),
            ];

            $query = Order::with(['user', 'cashier', 'items.product', 'payment'])
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
                    'img'      => $i->product?->image ? asset($i->product->image) : '',
                ])->values()->toArray();

                return [
                    'db_id'=> $o->id,
                    'id' => '#' . $o->order_number,
                    'is_new' => $o->created_at->gte(now()->subMinutes(30)),
                    'customer'=> $o->user?->name ?? 'Walk-in Customer',
                    'sub'=> $o->user ? 'Customer' : '',
                    'phone'=> $o->customer_phone,
                    'source'  => $source,
                    'items' => $itemsSummary ?: '-',
                    'status' => $o->status,
                    'time' => $o->created_at->format('h:i A'),
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

            $revenueToday  = (int) $dbOrders->whereNotIn('status', ['Cancelled'])->sum('total_price');
            $totalOrders   = $dbOrders->whereNotIn('status', ['Cancelled'])->count();
            $onlineOrders  = $dbOrders->where('order_type', 'online')->whereNotIn('status', ['Cancelled'])->count();
            $cashierOrders = $dbOrders->whereIn('order_type', ['dine-in', 'takeaway'])->whereNotIn('status', ['Cancelled'])->count();
            $pendingOrders = $dbOrders->where('status', 'Pending')->count();

            $totalCostToday = (int) DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereDate('orders.created_at', $date)
                ->whereNotIn('orders.status', ['Cancelled'])
                ->sum(DB::raw('order_items.quantity * products.cost_price'));
            $profitToday = max(0, (int) ($revenueToday - $totalCostToday));

            $yesterday        = Carbon::parse($date)->subDay()->toDateString();
            $revenueYesterday = (int) Order::whereDate('created_at', $yesterday)
                ->whereNotIn('status', ['Cancelled'])->sum('total_price');
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
                ->whereNotIn('status', ['Cancelled'])
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

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status'              => ['required', 'in:Pending,Preparing,Ready,Completed,Cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Tambahkan 'payment' agar data pembayarannya ikut terpanggil
        $order = Order::with(['items.product', 'payment'])->findOrFail($id);

        $updateData = ['status' => $request->status];

        // Assign the processing cashier the first time a staff member touches this order
        if (!$order->cashier_id) {
            $updateData['cashier_id'] = auth()->id();
        }

        if ($request->status === 'Cancelled') {
            $updateData['cancellation_reason'] = $request->cancellation_reason;

            // --- AWAL LOGIKA REFUND MIDTRANS ---
            $isRefundedViaMidtrans = false;

            // Cek apakah order lunas dibayar online (QRIS/E-Wallet)
            if ($order->payment && $order->payment->status === 'Paid' && in_array(strtolower($order->payment->payment_method), ['qris', 'gopay', 'shopeepay'])) {
                
                $serverKey = config('services.midtrans.server_key');
                $isProduction = config('services.midtrans.is_production', false);
                $baseUrl = $isProduction ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2';

                $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post("{$baseUrl}/{$order->order_number}/refund", [
                        'refund_key' => 'REF-' . $order->order_number . '-' . time(),
                        'amount'     => (int) $order->payment->amount,
                        'reason'     => $request->cancellation_reason ?? 'Dibatalkan oleh Kasir'
                    ]);

                // Batalkan proses Cancel jika Midtrans menolak refund (misal saldo merchant kurang)
                if ($response->failed() && $response->status() !== 200) {
                    \Illuminate\Support\Facades\Log::error('Midtrans Refund Failed', [
                        'order_id' => $order->order_number,
                        'response' => $response->json()
                    ]);

                    return response()->json([
                        'error' => 'refund_failed',
                        'message' => 'Gagal Refund Midtrans: ' . ($response->json('status_message') ?? 'Terjadi kesalahan jaringan.')
                    ], 422);
                }

                // Jika sukses, ubah status payment menjadi Refunded
                $order->payment->update(['status' => 'Refunded']);
                $isRefundedViaMidtrans = true;
            }
            // --- AKHIR LOGIKA REFUND MIDTRANS ---


            // Restore stock for every item in the cancelled order (KODE ASLI)
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->update(['stock' => DB::raw('stock + ' . (int) $item->quantity)]);
                } elseif ($item->custom_notes) {
                    $notes       = json_decode($item->custom_notes, true) ?? [];
                    $ingredients = array_values(array_filter([
                        $notes['isi']    ?? null,
                        $notes['varian'] ?? null,
                    ]));
                    if (!empty($notes['sauces'])) {
                        foreach (array_map('trim', explode(',', $notes['sauces'])) as $sauce) {
                            if ($sauce !== '') {
                                $ingredients[] = $sauce;
                            }
                        }
                    }
                    foreach ($ingredients as $ingredientName) {
                        Product::whereHas('category', fn($c) => $c->whereRaw('LOWER(name) = ?', ['custom']))
                            ->whereRaw('UPPER(name) = ?', [strtoupper(trim($ingredientName))])
                            ->update(['stock' => DB::raw('stock + ' . (int) $item->quantity)]);
                    }
                }
            }

            if (!$isRefundedViaMidtrans) {
                $order->payment()->delete();
            }
        }

        $order->update($updateData);

        // Ensure every completed order has a payment record so the Sales Report
        // can always join on payments without missing rows.
        if ($request->status === 'Completed' && !$order->payment()->exists()) {
            $method = $order->order_type === 'online' ? 'QRIS' : 'Cash';
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $method,
                'amount'         => $order->total_price,
                'status'         => 'Paid',
            ]);
        }

        return response()->json([
            'success'  => true,
            'status'   => $request->status,
            'order_id' => (int) $id,
        ]);
    }
}
