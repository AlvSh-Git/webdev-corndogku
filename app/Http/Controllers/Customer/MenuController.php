<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $storeInfo = $this->calcStoreStatus();
        return view('customer.menu', compact('storeInfo'));
    }

    public function storeStatus(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->calcStoreStatus());
    }

    public function customize()
    {
        $customProducts = Product::whereHas('category', fn($q) => $q->whereRaw('LOWER(name) = ?', ['custom']))
            ->orderBy('name')
            ->get();

        $isianProducts  = $customProducts->filter(fn($p) => $this->matchesType($p->name, ['sosis', 'mozza']));
        $varianProducts = $customProducts->filter(fn($p) => $this->matchesType($p->name, ['original', 'potato', 'ramen']));
        $sauceProducts  = $customProducts->filter(fn($p) => $this->matchesType($p->name, ['sauce', 'saos', 'ketchup', 'mayo']));

        return view('customer.customize', compact('isianProducts', 'varianProducts', 'sauceProducts'));
    }

    private function matchesType(string $name, array $keywords): bool
    {
        $lower = strtolower($name);
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) return true;
        }
        return false;
    }

    public function catalog(Request $request): \Illuminate\Http\JsonResponse
    {
        $category = $request->input('category', 'Semua');
        $search   = trim((string) $request->input('search', ''));
        $sort     = $request->input('sort', 'default');
        $min      = (int) $request->input('min', 0);
        $max      = $request->input('max');
        $cats     = $request->input('cats', []);

        $q = Product::with('category')
            ->where('is_custom', false)
            ->whereHas('category', fn($c) => $c->whereRaw('LOWER(name) != ?', ['custom']));

        if (!empty($cats)) {
            $q->whereHas('category', fn ($c) => $c->whereIn('name', (array) $cats));
        } elseif ($category && $category !== 'Semua') {
            $q->whereHas('category', fn ($c) => $c->where('name', $category));
        }

        if ($search !== '') {
            $q->where('name', 'like', "%{$search}%");
        }
        if ($min > 0) {
            $q->where('price', '>=', $min);
        }
        if ($max !== null && (int) $max > 0) {
            $q->where('price', '<=', (int) $max);
        }

        match ($sort) {
            'price-asc'  => $q->orderBy('price'),
            'price-desc' => $q->orderByDesc('price'),
            default      => $q->orderBy('category_id')->orderBy('name'),
        };

        $paginated = $q->paginate(15);

        // Ambil daftar ID produk yang di-wishlist oleh user yang sedang login
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = auth()->user()->wishlistProducts()->pluck('products.id')->toArray();
        }

        $data = $paginated->getCollection()->map(fn ($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'description'   => $p->description ?? '',
            'price'         => (int) $p->price,
            'image_url'     => $p->image ? asset($p->image) : asset('assets/img/CA_ORIGINAL.png'),
            'is_available'  => (bool) $p->is_available,
            'stock'         => (int) $p->stock,
            'category'      => ['name' => $p->category?->name ?? ''],
            // Tambahkan pengecekan ini agar frontend tahu status wishlist-nya
            'is_wishlisted' => in_array($p->id, $wishlistProductIds),
        ]);

        return response()->json([
            'data'         => $data->values(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
            'from'         => $paginated->firstItem(),
            'to'           => $paginated->lastItem(),
        ]);
    }
}
