<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\NormalizesPhone;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Customer order history (list, detail, WhatsApp receipt).
class HistoryController extends Controller
{
    use NormalizesPhone;

    // Show the user's orders.
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $statusFilter = $request->query('status');

        $statusMap = [
            'Menunggu'   => 'Pending',
            'Diproses'   => ['Preparing', 'Ready'],
            'Selesai'    => 'Completed',
            'Dibatalkan' => 'Cancelled',
        ];

        $query = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest();

        if ($statusFilter && isset($statusMap[$statusFilter])) {
            $mapped = $statusMap[$statusFilter];
            is_array($mapped)
                ? $query->whereIn('status', $mapped)
                : $query->where('status', $mapped);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.history', compact('orders', 'statusFilter'));
    }

    // Return one of the user's orders as JSON.
    public function show($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $subtotal = (int) round($order->total_price / 1.11);
        $tax      = $order->total_price - $subtotal;

        $orderTypeLabel = match ($order->order_type) {
            'dine-in'  => 'Dine-in',
            'takeaway' => 'Takeaway',
            'online'   => 'Online',
            default    => ucfirst($order->order_type ?? 'Online'),
        };

        return response()->json([
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'date'         => $order->created_at->translatedFormat('d M Y, H:i'),
            'order_type'   => $orderTypeLabel,
            'status'       => $order->status,
            'items'        => $order->items->map(fn($i) => [
                'name'     => $i->product_name,
                'qty'      => $i->quantity,
                'subtotal' => $i->subtotal,
            ]),
            'subtotal'     => $subtotal,
            'tax'          => $tax,
            'total'        => $order->total_price,
        ]);
    }

    // Re-open Midtrans Snap for an unpaid online order so the customer can pay
    // (e.g. after the original QRIS expired). Returns a fresh Snap token.
    public function pay($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan.'], 404);
        }

        // Only an unpaid online order is payable here. A paid order has already
        // advanced past Pending; walk-in orders are settled at the counter.
        if ($order->order_type !== 'online' || $order->status !== 'Pending') {
            return response()->json(['error' => 'Pesanan ini tidak dapat dibayar.'], 422);
        }

        if (!class_exists('\\Midtrans\\Snap') || !config('services.midtrans.server_key')) {
            return response()->json(['error' => 'Pembayaran Midtrans belum dikonfigurasi.'], 503);
        }

        try {
            \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;

            $user = auth()->user();

            // Midtrans requires a globally-unique order_id per transaction, so a
            // retry gets a -R{timestamp} suffix; the webhook strips it back to the
            // real order_number (see PurchaseController::midtransNotification).
            $retryOrderId = $order->order_number . '-R' . now()->timestamp;

            // Rebuild item_details from the saved order. Per-unit prices are
            // re-derived from each line's subtotal; a single adjustment line
            // absorbs tax + any rounding so the item total equals total_price
            // exactly (Midtrans rejects a gross_amount/item_details mismatch).
            $lineItems = $order->items->map(fn ($i) => [
                'id'       => (string) ($i->product_id ?? 'custom'),
                'price'    => $i->quantity > 0 ? (int) round($i->subtotal / $i->quantity) : (int) $i->subtotal,
                'quantity' => (int) $i->quantity,
                'name'     => mb_substr($i->product_name ?? 'Item', 0, 50),
            ])->values()->all();

            $itemsSum = array_sum(array_map(fn ($it) => $it['price'] * $it['quantity'], $lineItems));
            $lineItems[] = [
                'id'       => 'TAX-11',
                'price'    => (int) $order->total_price - $itemsSum,
                'quantity' => 1,
                'name'     => 'Pajak (11%)',
            ];

            $params = [
                'transaction_details' => [
                    'order_id'     => $retryOrderId,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email ?? 'guest@corndog.ku',
                ],
                'enabled_payments' => ['qris', 'gopay', 'shopeepay'],
                'item_details'     => $lineItems,
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            Log::info('Midtrans Snap token re-issued (history)', [
                'order_number' => $order->order_number,
                'retry_id'     => $retryOrderId,
            ]);

            return response()->json([
                'snap_token'   => $snapToken,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap re-issue failed (history)', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Gagal memulai pembayaran. Coba lagi.'], 500);
        }
    }

    // Send the order receipt over WhatsApp.
    public function sendReceipt($id)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $order = Order::with(['user', 'items'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $rawPhone = $order->customer_phone ?? $order->user?->phone ?? null;
            if (!$rawPhone) {
                return response()->json(['success' => false, 'message' => 'Nomor WhatsApp tidak ditemukan.'], 400);
            }
            $targetPhone = $this->normalizePhone($rawPhone);

            $subtotal = (int) round($order->total_price / 1.11);
            $tax      = $order->total_price - $subtotal;

            $msg  = "🌽 *CORNDOG-KU - STRUK PEMBELIAN* 🌽\n";
            $msg .= "--------------------------------------\n";
            $msg .= "No. Pesanan : " . $order->order_number . "\n";
            $msg .= "Tanggal     : " . $order->created_at->format('d M Y, H:i') . "\n";
            $msg .= "Nama        : " . ($order->user?->name ?? 'Walk-in') . "\n";
            $msg .= "--------------------------------------\n";

            foreach ($order->items as $item) {
                $name = $item->product_name ?? $item->product?->name ?? 'Item';
                $msg .= "- " . $item->quantity . "x " . $name . "\n";
                $msg .= "  Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
            }

            $msg .= "--------------------------------------\n";
            $msg .= "Subtotal    : Rp " . number_format($subtotal, 0, ',', '.') . "\n";
            $msg .= "Pajak (11%) : Rp " . number_format($tax, 0, ',', '.') . "\n";
            $msg .= "*Total      : Rp " . number_format($order->total_price, 0, ',', '.') . "*\n";
            $msg .= "--------------------------------------\n";
            $msg .= "Terima kasih sudah mampir! 😊";

            $response = Http::withHeaders(['Authorization' => config('services.fonnte.token')])
                ->timeout(30)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $targetPhone,
                    'message' => $msg,
                ]);

            $body = $response->json();

            Log::info('Fonnte WA receipt (customer)', [
                'order_id' => $id,
                'phone'    => $targetPhone,
                'status'   => $response->status(),
                'body'     => $body,
            ]);

            if ($response->successful() && ($body['status'] ?? false) === true) {
                return response()->json(['success' => true, 'message' => 'Struk teks berhasil dikirim.']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Fonnte Error: ' . ($body['reason'] ?? 'Unknown error'),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Fonnte WA receipt error (customer)', ['order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
