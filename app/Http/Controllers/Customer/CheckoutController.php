<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$this->calcStoreStatus()['is_open']) {
            return redirect()->route('cart')->with('error', 'Toko sedang tutup. Checkout akan diaktifkan kembali saat toko buka.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart');
        }

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
        $tax      = (int) round($subtotal * 0.11);
        $total    = $subtotal + $tax;

        $orderId = 'CKKU-' . strtoupper(substr(uniqid(), -6)) . '-' . now()->format('ymd');

        $snapToken = null;
        $clientKey = config('services.midtrans.client_key', '');

        if (class_exists('\\Midtrans\\Snap') && config('services.midtrans.server_key')) {
            try {
                \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
                \Midtrans\Config::$isSanitized  = true;
                \Midtrans\Config::$is3ds        = true;

                $user = auth()->user();

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => $total,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email'      => $user->email ?? 'guest@corndog.ku',
                    ],
                    'enabled_payments' => ['qris', 'gopay', 'shopeepay'],
                    'item_details'     => array_merge(
                        array_values(array_map(fn($item) => [
                            'id'       => (string) $item['id'],
                            'price'    => (int) $item['price'],
                            'quantity' => (int) $item['qty'],
                            'name'     => mb_substr($item['name'], 0, 50),
                        ], $cart)),
                        [['id' => 'TAX-11', 'price' => $tax, 'quantity' => 1, 'name' => 'Pajak (11%)']]
                    ),
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                Log::info('Midtrans Snap token generated', ['order_id' => $orderId]);
            } catch (\Exception $e) {
                Log::error('Midtrans Snap token failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            }
        }

        return view('customer.checkout', compact('cart', 'subtotal', 'tax', 'total', 'snapToken', 'clientKey', 'orderId'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (!$this->calcStoreStatus()['is_open']) {
            return response()->json([
                'error'   => 'store_closed',
                'message' => 'Toko sedang tutup. Silakan checkout saat toko sudah buka kembali.',
            ], 403);
        }

        $cart = session()->get('cart', []);

        $orderNumber = (string) $request->input('order_number', 'CKKU-' . strtoupper(substr(uniqid(), -6)) . '-' . now()->format('ymd'));

        // Idempotency guard — if this checkout's order already exists (double-click,
        // or a retry after returning from the payment app) just let the client
        // re-open Snap for the same order instead of creating a duplicate.
        if (Order::where('order_number', $orderNumber)->exists()) {
            return response()->json(['success' => true, 'order_number' => $orderNumber]);
        }

        if (empty($cart)) {
            return response()->json(['redirect' => route('history')]);
        }

        $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
        $tax      = (int) round($subtotal * 0.11);
        $total    = $subtotal + $tax;

        // Pre-checkout stock guard — abort before touching the DB if any item is insufficient
        foreach ($cart as $item) {
            $isCustom  = !empty($item['is_custom']);
            $productId = (!$isCustom && is_numeric($item['id'])) ? (int) $item['id'] : null;
            if ($productId) {
                $product = Product::find($productId);
                $qty     = (int) ($item['qty'] ?? 1);
                if ($product && $product->stock < $qty) {
                    return response()->json([
                        'error'   => 'stock_insufficient',
                        'message' => "Stok '{$product->name}' tidak mencukupi. Tersisa: {$product->stock}.",
                    ], 422);
                }
            }
        }

        $createdOrderId = null;

        DB::transaction(function () use ($cart, $orderNumber, $total, &$createdOrderId) {
            $user  = auth()->user();
            // Only store a real phone number; never fall back to the username
            // (which is derived from the email and is not a phone).
            $phone = $user->phone ?: null;

            $order = Order::create([
                'user_id'        => $user->id,
                'customer_phone' => $phone,
                'order_number'   => $orderNumber,
                'total_price'    => $total,
                'status'         => 'Pending',
                'order_type'     => 'online',
            ]);

            foreach ($cart as $item) {
                $isCustom  = !empty($item['is_custom']);
                $productId = (!$isCustom && is_numeric($item['id'])) ? (int) $item['id'] : null;

                $customNotes = null;
                if ($isCustom) {
                    $customNotes = json_encode([
                        'isi'    => $item['isi']    ?? null,
                        'varian' => $item['varian'] ?? null,
                        'sauces' => $item['sauces'] ?? null,
                    ], JSON_UNESCAPED_UNICODE);
                }

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'product_name' => mb_substr($item['name'], 0, 100),
                    'quantity'     => (int) $item['qty'],
                    'subtotal'     => (int) ($item['price'] * $item['qty']),
                    'custom_notes' => $customNotes,
                ]);

                if ($productId) {
                    // Deduct stock for regular (non-custom) products
                    Product::where('id', $productId)
                           ->update(['stock' => DB::raw('GREATEST(stock - ' . (int) $item['qty'] . ', 0)')]);
                } elseif ($isCustom) {
                    // Deduct ingredient stock for custom corndog from the hidden "custom" category
                    $ingredients = array_values(array_filter([
                        $item['isi']    ?? null,
                        $item['varian'] ?? null,
                    ]));
                    if (!empty($item['sauces'])) {
                        foreach (array_map('trim', explode(',', $item['sauces'])) as $sauce) {
                            if ($sauce !== '') {
                                $ingredients[] = $sauce;
                            }
                        }
                    }
                    $qty = (int) ($item['qty'] ?? 1);
                    foreach ($ingredients as $ingredientName) {
                        Product::whereHas('category', fn($c) => $c->whereRaw('LOWER(name) = ?', ['custom']))
                            ->whereRaw('UPPER(name) = ?', [strtoupper(trim($ingredientName))])
                            ->update(['stock' => DB::raw('GREATEST(stock - ' . $qty . ', 0)')]);
                    }
                }
            }

            // Payment starts Unpaid; it is confirmed by the Midtrans webhook
            // (and the client confirm() fallback) once the customer actually pays.
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'QRIS',
                'amount'         => $total,
                'status'         => 'Unpaid',
            ]);

            $createdOrderId = $order->id;
        });

        // Cart is cleared now that the order exists — the order is the source of
        // truth from here on, so a dropped payment callback can't lose it.
        session()->forget('cart');

        return response()->json(['success' => true, 'order_number' => $orderNumber]);
    }

    /**
     * Mark a just-paid order as paid from the Snap onSuccess callback. This is a
     * fast-path for UX; the Midtrans server-to-server webhook is the authoritative
     * confirmation and works even when the mobile callback never fires. Idempotent.
     */
    public function confirm(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $orderNumber = (string) $request->input('order_number', '');

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->first();

        if ($order) {
            $order->payment()->updateOrCreate([], [
                'payment_method' => 'QRIS',
                'amount'         => $order->total_price,
                'status'         => 'Paid',
            ]);

            if ($order->status === 'Pending') {
                $order->update(['status' => 'Preparing']);
            }

            session()->flash('show_receipt_for_order', $order->id);
        }

        return response()->json(['redirect' => route('history')]);
    }
}
