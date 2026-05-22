<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;

class PurchaseController extends Controller
{
    public function index()
    {
        $role      = $this->currentRole();
        $storeInfo = $this->calcStoreStatus();

        try {
            $categories = Category::orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }

        return view('purchase.index', compact('role', 'categories', 'storeInfo'));
    }

    /** AJAX: search customer by name/username OR by phone (treated as username) */
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

    /** AJAX: paginated product grid (12 per page) with category + search filter */
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

            /* Fallback mock data when DB is empty and no filters applied */
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

    /** AJAX: create order + items + payment */
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
            /* ── Guard: store must be open ──────────────────────────── */
            $storeInfo = $this->calcStoreStatus();
            if (!$storeInfo['is_open']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko sedang tutup. Tidak dapat memproses pesanan.',
                ], 403);
            }

            /* ── Guard: check stock availability for every item ─────── */
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
            }

            /* ── Resolve or persist customer ────────────────────────── */
            $userId = null;

            if ($request->filled('customer_id')) {
                /* Cashier picked a known customer from the AJAX dropdown */
                $user   = User::find((int) $request->customer_id);
                $userId = $user?->id;

            } elseif ($request->filled('customer_phone')) {
                $phone = trim($request->customer_phone);

                /* username field stores phone number in this system */
                $user = User::where('username', $phone)
                            ->where('role', 'customer')
                            ->first();

                if (!$user) {
                    /* First-time customer — persist so future searches find them */
                    $baseEmail = $phone . '@walkin.pos';
                    $email     = User::where('email', $baseEmail)->exists()
                        ? $phone . '.' . time() . '@walkin.pos'
                        : $baseEmail;

                    $user = User::create([
                        'name'     => $request->customer_name,
                        'username' => $phone,
                        'email'    => $email,
                        'password' => Str::random(16),  /* auto-hashed by model cast */
                        'role'     => 'customer',
                        'status'   => 'active',
                    ]);
                }

                $userId = $user->id;
            }

            $subtotal  = 0;
            $lineItems = [];
            foreach ($request->items as $item) {
                $lineTotal   = (int) $item['price'] * (int) $item['qty'];
                $subtotal   += $lineTotal;
                $lineItems[] = [
                    'product_id'   => (int) $item['product_id'],
                    'quantity'     => (int) $item['qty'],
                    'subtotal'     => $lineTotal,
                    'custom_notes' => $item['notes'] ?? null,
                ];
            }

            $tax        = (int) round($subtotal * 0.11);
            $totalPrice = $subtotal + $tax;

            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'user_id'      => $userId,
                'order_number' => $orderNumber,
                'total_price'  => $totalPrice,
                'status'       => 'Pending',
                'order_type'   => $request->order_type,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create($line);
                // Decrement stock, flooring at 0 to prevent negative inventory
                Product::where('id', $line['product_id'])
                       ->update(['stock' => \DB::raw('GREATEST(stock - ' . (int) $line['quantity'] . ', 0)')]);
            }

            $order->payment()->create([
                'payment_method' => $request->payment_method,
                'amount'         => $totalPrice,
                'status'         => 'Paid',
            ]);

            return response()->json([
                'success'        => true,
                'order_number'   => $orderNumber,
                'customer'       => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? null,
                'order_type'     => $request->order_type,
                'payment'        => $request->payment_method,
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $totalPrice,
                'items'          => collect($lineItems)->map(fn($l) => [
                    'name'     => Product::find($l['product_id'])?->name ?? 'Item',
                    'qty'      => $l['quantity'],
                    'subtotal' => $l['subtotal'],
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
