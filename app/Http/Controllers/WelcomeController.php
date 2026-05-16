<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class WelcomeController extends Controller
{
    public function index()
    {
        $products   = Product::with('category')->orderBy('category_id')->get();
        $categories = Category::orderBy('id')->get();

        return view('welcome', compact('products', 'categories'));
    }
}
