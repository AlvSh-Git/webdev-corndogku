<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

// Owner category creation.
class CategoryController extends Controller
{
    // Create a category from a form submit.
    public function store(Request $request)
    {
        return $this->createCategory($request);
    }

    // Create a category from the product modal (AJAX).
    public function storeAjax(Request $request)
    {
        return $this->createCategory($request);
    }

    // Save a new category and return it as JSON.
    private function createCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
        ]);

        $category = Category::create(['name' => trim($request->name)]);

        return response()->json([
            'success' => true,
            'id'      => $category->id,
            'name'    => $category->name,
        ]);
    }
}
