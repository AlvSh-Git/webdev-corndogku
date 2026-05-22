<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $role       = $this->currentRole();
        $products   = Product::with('category')->where('is_custom', false)->orderBy('category_id')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('owner.product.index', compact('role', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'integer', 'exists:categories,id'],
            'price'           => ['required', 'integer', 'min:0'],
            'cost_price'      => ['required', 'integer', 'min:0'],
            'stock'           => ['required', 'integer', 'min:0'],
            'description'     => ['required', 'string'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'image'           => ['nullable', 'image', 'max:2048'],
        ]);

        $stock       = (int) $request->stock;
        $isAvailable = $stock > 0 ? (bool) $request->input('is_available', true) : false;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = 'storage/' . $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'description'  => $request->description,
            'price'        => $request->price,
            'cost_price'   => $request->cost_price,
            'stock'        => $stock,
            'image'        => $imagePath,
            'is_custom'    => false,
            'is_available' => $isAvailable,
        ]);

        return redirect()->route('owner.products')->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'integer', 'exists:categories,id'],
            'price'           => ['required', 'integer', 'min:0'],
            'cost_price'      => ['required', 'integer', 'min:0'],
            'stock'           => ['required', 'integer', 'min:0'],
            'description'     => ['required', 'string'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'image'           => ['nullable', 'image', 'max:2048'],
        ]);

        $product->category_id  = $request->category_id;
        $product->name         = $request->name;
        $product->description  = $request->description;
        $product->price        = $request->price;
        $product->cost_price   = $request->cost_price;
        $product->stock        = (int) $request->input('stock', 0);
        $product->is_available = ($product->stock <= 0) ? false : $request->boolean('is_available', true);

        if ($request->hasFile('image')) {
            $product->image = 'storage/' . $request->file('image')->store('products', 'public');
        }

        $product->save();

        return redirect()->route('owner.products')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('owner.products')->with('success', 'Product deleted.');
    }

    public function catalog(Request $request): \Illuminate\Http\JsonResponse
    {
        $category = $request->input('category', 'Semua');
        $search   = trim((string) $request->input('search', ''));
        $sort     = $request->input('sort', 'default');
        $min      = (int) $request->input('min', 0);
        $max      = $request->input('max');
        $cats     = $request->input('cats', []);

        $q = Product::with('category')->where('is_custom', false);

        // Category filter — checkbox multi-select overrides tab selection
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

        // Map each product to include a pre-resolved image_url
        $data = $paginated->getCollection()->map(fn ($p) => [
            'id'           => $p->id,
            'name'         => $p->name,
            'description'  => $p->description ?? '',
            'price'        => (int) $p->price,
            'image_url'    => $p->image
                                 ? asset($p->image)
                                 : asset('assets/img/CA_ORIGINAL.png'),
            'is_available' => (bool) $p->is_available,
            'stock'        => (int) $p->stock,
            'category'     => ['name' => $p->category?->name ?? ''],
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
