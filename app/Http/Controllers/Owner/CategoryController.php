<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        return $this->createCategory($request);
    }

    public function storeAjax(Request $request)
    {
        return $this->createCategory($request);
    }

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
