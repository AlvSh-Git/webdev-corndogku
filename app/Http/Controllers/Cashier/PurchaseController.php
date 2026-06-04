<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\NormalizesPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;

class PurchaseController extends Controller
{
    use NormalizesPhone;

    public function index()
    {
        $role      = $this->currentRole();
        $storeInfo = $this->calcStoreStatus();

        try {
            $categories = Category::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }

        return view('cashier.purchase', compact('role', 'categories', 'storeInfo'));
    }

    public function searchCustomer(Request $request)
    {
        $q     = trim($request->query('q', ''));
        $phone = trim($request->query('phone', ''));

        $term = $phone ?: $q;

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        try {
            $users = User::where('role', 'customer')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                          ->orWhere('username', 'like', '%' . $term . '%');
                })
                ->limit(8)
                ->get(['id', 'name', 'username']);

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getProducts(Request $request)
    {
        $category = $request->query('category', 'all');
        $search   = trim($request->query('search', ''));
        $page     = max(1, (int) $request->query('page', 1));

        try {
            $query = Product::with('category')->where('is_custom', false);

            if ($category !== 'all') {
                $query->whereHas('category', fn($q) => $q->where('name', $category));
            }

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $paginated = $query->orderBy('category_id')->orderBy('name')
                               ->paginate(12, ['*'], 'page', $page);

            if ($paginated->total() === 0 && !$search && $category === 'all') {
                $mock = [
                    ['id'=>1,'name'=>'Corndog Original',        'price'=>16000,'image'=>asset('assets/img/CA_ORIGINAL.png'),                  'category'=>'Corndog Asin','stock'=>50,'is_available'=>true],
                    ['id'=>2,'name'=>'Corndog Full Mozza',       'price'=>17000,'image'=>asset('assets/img/CA_FULL_MOZZA.png'),                  'category'=>'Corndog Asin','stock'=>30,'is_available'=>true],
                    ['id'=>3,'name'=>'Corndog Squid Mozza',      'price'=>16000,'image'=>asset('assets/img/CA_SQUID_ORI.png'),                   'category'=>'Corndog Asin','stock'=>0,'is_available'=>false],
                    ['id'=>4,'name'=>'Corndog Mozza Potato',     'price'=>20000,'image'=>asset('assets/img/CA_MOZZA_POTATO.png'),                'category'=>'Corndog Asin','stock'=>20,'is_available'=>true],
                    ['id'=>5,'name'=>'Corndog Ramen Mix',        'price'=>18000,'image'=>asset('assets/img/CA_RAMEN_MIX.png'),                   'category'=>'Corndog Asin','stock'=>18,'is_available'=>true],
                    ['id'=>6,'name'=>'Corndog Choco Crunch',     'price'=>20000,'image'=>asset('assets/img/CM_CHOCO_CHRUNCH_CHEESE.png'),        'category'=>'Corndog Manis','stock'=>25,'is_available'=>true],
                    ['id'=>7,'name'=>'Corndog Matcha Biskuit',   'price'=>20000,'image'=>asset('assets/img/CM_GREENTEA_CHRUNCHY_BISKUIT.png'),   'category'=>'Corndog Manis','stock'=>22,'is_available'=>true],
                    ['id'=>8,'name'=>'Corndog Tiramisu Biskuit', 'price'=>20000,'image'=>asset('assets/img/CM_TIRAMISU_BISKUIT.png'),            'category'=>'Corndog Manis','stock'=>15,'is_available'=>true],
                ];
                return response()->json(['products'=>$mock,'current_page'=>1,'last_page'=>1,'total'=>count($mock)]);
            }

            $products = $paginated->getCollection()->map(fn($p) => [
                'id'           => $p->id,
                'name'         => $p->name,
                'price'        => $p->price,
                'image'        => $p->image ? asset($p->image) : asset('assets/img/CA_ORIGINAL.png'),
                'category'     => $p->category?->name ?? '',
                'stock'        => (int) $p->stock,
                'is_available' => (bool) $p->is_available,
            ]);

            return response()->json([
                'products'     => $products,
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['products'=>[],'current_page'=>1,'last_page'=>1,'total'=>0]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => ['required', 'string', 'max:100'],
            'order_type'     => ['required', 'in:dine-in,takeaway'],
            'payment_method' => ['required', 'in:Cash,QRIS,Debit'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
            'items.*.price'      => ['required', 'integer', 'min:0'],
        ]);

        try {
            $storeInfo = $this->calcStoreStatus();
            if (!$storeInfo['is_open']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko sedang tutup. Tidak dapat memproses pesanan.',
                ], 403);
            }

            // Validate stock and build line items in a single pass
            $subtotal  = 0;
            $lineItems = [];
            foreach ($request->items as $item) {
                $product = Product::find((int) $item['product_id']);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak ditemukan (ID: ' . (int) $item['product_id'] . ').',
                    ], 422);
                }
                if ((int) $item['qty'] > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok ' . $product->name . ' tidak mencukupi! '
                                   . 'Sisa stok hanya ' . $product->stock . ' pcs.',
                    ], 422);
                }

                $lineTotal   = (int) $item['price'] * (int) $item['qty'];
                $subtotal   += $lineTotal;
                $lineItems[] = [
                    'product_id'   => (int) $item['product_id'],
                    'product_name' => $product->name,
                    'quantity'     => (int) $item['qty'],
                    'subtotal'     => $lineTotal,
                    'custom_notes' => $item['notes'] ?? null,
                ];
            }

            $userId = null;

            if ($request->filled('customer_id')) {
                $user   = User::find((int) $request->customer_id);
                $userId = $user?->id;
            } elseif ($request->filled('customer_phone')) {
                $phone = trim($request->customer_phone);

                $user = User::where('username', $phone)
                            ->where('role', 'customer')
                            ->first();

                if (!$user) {
                    $baseEmail = $phone . '@walkin.pos';
                    $email     = User::where('email', $baseEmail)->exists()
                        ? $phone . '.' . time() . '@walkin.pos'
                        : $baseEmail;

                    $user = User::create([
                        'name'     => $request->customer_name,
                        'username' => $phone,
                        'email'    => $email,
                        'password' => Str::random(16),
                        'role'     => 'customer',
                        'status'   => 'active',
                    ]);
                }

                $userId = $user->id;
            }

            $tax        = (int) round($subtotal * 0.11);
            $totalPrice = $subtotal + $tax;
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'user_id'        => $userId,
                'cashier_id'     => auth()->id(),
                'customer_phone' => $request->customer_phone ? $this->normalizePhone(trim($request->customer_phone)) : null,
                'order_number'   => $orderNumber,
                'total_price'    => $totalPrice,
                'status'         => 'Pending',
                'order_type'     => $request->order_type,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create($line);
                Product::where('id', $line['product_id'])
                       ->update(['stock' => \DB::raw('GREATEST(stock - ' . (int) $line['quantity'] . ', 0)')]);
            }

            $receiptData = [
                'success'        => true,
                'order_id'       => $order->id,
                'order_number'   => $orderNumber,
                'customer'       => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? null,
                'order_type'     => $request->order_type,
                'payment'        => $request->payment_method,
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $totalPrice,
                'items'          => collect($lineItems)->map(fn($l) => [
                    'name'     => $l['product_name'],
                    'qty'      => $l['quantity'],
                    'subtotal' => $l['subtotal'],
                ]),
            ];

            if ($request->payment_method === 'QRIS') {
                $order->payment()->create([
                    'payment_method' => 'QRIS',
                    'amount'         => $totalPrice,
                    'status'         => 'Unpaid',
                ]);

                $snapToken = null;
                if (class_exists('\\Midtrans\\Snap') && config('services.midtrans.server_key')) {
                    try {
                        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
                        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
                        \Midtrans\Config::$isSanitized  = true;
                        \Midtrans\Config::$is3ds        = true;

                        $mtItems = array_merge(
                            array_map(fn($l) => [
                                'id'       => (string) $l['product_id'],
                                'price'    => (int) ($l['subtotal'] / max(1, $l['quantity'])),
                                'quantity' => (int) $l['quantity'],
                                'name'     => mb_substr($l['product_name'], 0, 50),
                            ], $lineItems),
                            [['id' => 'TAX-11', 'price' => $tax, 'quantity' => 1, 'name' => 'Pajak (11%)']]
                        );

                        $params = [
                            'transaction_details' => [
                                'order_id'     => $orderNumber,
                                'gross_amount' => $totalPrice,
                            ],
                            'customer_details' => [
                                'first_name' => $request->customer_name,
                                'email'      => 'walkin@corndog.ku',
                            ],
                            'enabled_payments' => ['other_qris', 'gopay', 'shopeepay'],
                            'item_details'     => $mtItems,
                        ];

                        $snapToken = \Midtrans\Snap::getSnapToken($params);
                        Log::info('Midtrans Snap token generated (cashier)', ['order_number' => $orderNumber]);
                    } catch (\Exception $e) {
                        Log::error('Midtrans Snap token failed (cashier)', [
                            'error'        => $e->getMessage(),
                            'order_number' => $orderNumber,
                        ]);
                    }
                }

                return response()->json(array_merge($receiptData, ['snap_token' => $snapToken]));
            }

            $order->payment()->create([
                'payment_method' => $request->payment_method,
                'amount'         => $totalPrice,
                'status'         => 'Paid',
            ]);

            return response()->json($receiptData);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markQrisPaid($id)
    {
        $order = Order::with(['items', 'payment', 'user'])->find((int) $id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        $order->payment()->update(['status' => 'Paid']);

        $subtotalInt = (int) round($order->total_price / 1.11);
        $taxInt      = $order->total_price - $subtotalInt;

        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'customer'       => $order->user?->name ?? 'Walk-in',
            'customer_phone' => $order->customer_phone,
            'order_type'     => $order->order_type,
            'payment'        => 'QRIS',
            'subtotal'       => $subtotalInt,
            'tax'            => $taxInt,
            'total'          => $order->total_price,
            'items'          => $order->items->map(fn($i) => [
                'name'     => $i->product_name,
                'qty'      => $i->quantity,
                'subtotal' => $i->subtotal,
            ]),
        ]);
    }

    // ── Midtrans server-to-server notification (webhook) ─────────────
    //
    // Midtrans POSTs here whenever a transaction changes state. Because it is a
    // server call (no session/cookie) the route is public and CSRF-exempt; we
    // authenticate it instead with the SHA-512 signature_key. This guarantees the
    // DB is updated even if the cashier closes the browser before onSuccess fires.
    public function midtransNotification(Request $request)
    {
        $serverKey         = config('services.midtrans.server_key');
        $orderId           = $request->input('order_id');
        $statusCode        = $request->input('status_code');
        $grossAmount       = $request->input('gross_amount');
        $signatureKey      = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus       = $request->input('fraud_status');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Verify signature: sha512(order_id + status_code + gross_amount + server_key)
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expected, (string) $signatureKey)) {
            Log::warning('Midtrans notification signature mismatch', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Map Midtrans transaction_status → our payment status enum
        $paymentStatus = match ($transactionStatus) {
            'capture'    => ($fraudStatus === 'challenge') ? 'Unpaid' : 'Paid',
            'settlement' => 'Paid',
            'pending'    => 'Unpaid',
            'deny', 'cancel', 'expire' => 'Failed',
            default      => 'Unpaid',
        };

        // updateOrCreate (scoped to this order via the hasOne relation) keeps the
        // webhook idempotent — Midtrans may deliver the same notification twice.
        $order->payment()->updateOrCreate([], [
            'payment_method' => 'QRIS',
            'amount'         => $order->total_price,
            'status'         => $paymentStatus,
        ]);

        // Advance the order out of Pending once paid; cancel it if the charge failed.
        if ($paymentStatus === 'Paid' && $order->status === 'Pending') {
            $order->update(['status' => 'Preparing']);
        } elseif ($paymentStatus === 'Failed' && $order->status === 'Pending') {
            $order->update(['status' => 'Cancelled']);
        }

        Log::info('Midtrans notification processed', [
            'order_number'       => $orderId,
            'transaction_status' => $transactionStatus,
            'payment_status'     => $paymentStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    // ── Send WhatsApp receipt via Fonnte API ─────────────────
    public function sendWhatsAppReceipt($id)
    {
        $order = Order::with(['items', 'user'])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        // Prefer the phone captured at order time; fall back to the user's current profile phone
        $rawPhone = $order->customer_phone ?? $order->user?->phone ?? null;

        if (!$rawPhone || !preg_match('/\d/', $rawPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp tidak tersedia untuk pesanan ini.',
            ], 422);
        }

        $phone = $this->normalizePhone($rawPhone);

        // Reconstruct subtotal from total (total = subtotal * 1.11)
        $subtotalInt = (int) round($order->total_price / 1.11);
        $taxInt      = $order->total_price - $subtotalInt;

        $itemLines = $order->items->map(function ($item) {
            $name = $item->product_name ?? $item->product?->name ?? 'Item';
            return $item->quantity . '× ' . $name
                . ' — Rp ' . number_format($item->subtotal, 0, ',', '.');
        })->implode("\n");

        $orderTypeLbl = match ($order->order_type) {
            'dine-in'  => 'Dine-in',
            'takeaway' => 'Takeaway',
            'online'   => 'Online',
            default    => ucfirst($order->order_type),
        };

        $message  = "🌽 *CORNDOG-KU — Struk Pembelian*\n";
        $message .= $this->storeAddress() . "\n\n";
        $message .= "No. Pesanan : *{$order->order_number}*\n";
        $message .= "Pelanggan   : *" . ($order->user?->name ?? 'Walk-in') . "*\n";
        $message .= "Tipe Order  : *{$orderTypeLbl}*\n";
        $message .= "─────────────────────\n";
        $message .= $itemLines . "\n";
        $message .= "─────────────────────\n";
        $message .= "Subtotal    : Rp " . number_format($subtotalInt, 0, ',', '.') . "\n";
        $message .= "Pajak (11%) : Rp " . number_format($taxInt, 0, ',', '.') . "\n";
        $message .= "*Total Bayar : Rp " . number_format($order->total_price, 0, ',', '.') . "*\n\n";
        $message .= "Terima kasih sudah mampir! Sampai jumpa 🌽";

        $token = config('services.fonnte.token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'FONNTE_TOKEN belum dikonfigurasi di server.',
            ], 500);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $phone,
                    'message' => $message,
                ]);

            $body = $response->json();

            Log::info('Fonnte WA receipt (cashier)', [
                'order_id' => $id,
                'phone'    => $phone,
                'status'   => $response->status(),
                'body'     => $body,
            ]);

            if (($body['status'] ?? false) === true) {
                return response()->json(['success' => true]);
            }

            return response()->json([
                'success' => false,
                'message' => $body['reason'] ?? 'Fonnte menolak pengiriman.',
            ], 422);

        } catch (\Exception $e) {
            Log::error('Fonnte WA receipt error (cashier)', ['order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Fonnte: ' . $e->getMessage(),
            ], 500);
        }
    }

}
