<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
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
        

        return view('owner.products', compact('role', 'products', 'categories'));
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
            'low_stock'    => $request->filled('min_stock_alert') ? (int) $request->min_stock_alert : null,
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
        $product->low_stock    = $request->filled('min_stock_alert') ? (int) $request->min_stock_alert : null;
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
}
