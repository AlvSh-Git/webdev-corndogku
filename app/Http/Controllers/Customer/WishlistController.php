<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Customer wishlist (favorites).
class WishlistController extends Controller
{
    // Menampilkan semua produk yang disukai pelanggan
    public function index()
    {
        $wishlistItems = Auth::user()->wishlistProducts()->with('category')->get();
        return view('customer.wishlist', compact('wishlistItems'));
    }

    // Mengontrol tambah/hapus wishlist berbasis AJAX
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $productId = $request->input('product_id');

        // ... (Ini kode logika attach/detach kamu yang sudah ada, biarkan tetap seperti itu) ...
        if ($user->wishlistProducts()->where('product_id', $productId)->exists()) {
            $user->wishlistProducts()->detach($productId);
            $status = 'removed';
            $message = 'Berhasil dihapus dari wishlist!';
        } else {
            $user->wishlistProducts()->attach($productId);
            $status = 'added';
            $message = 'Berhasil ditambahkan ke wishlist!';
        }

        // PASTIKAN BAGIAN RETURN JSON KAMU DI BAWAH INI MEMILIKI 'count'
        return response()->json([
            'success' => true,
            'status'  => $status,
            'message' => $message,
            'count'   => $user->wishlistProducts()->count() // <-- Tambahkan/pastikan baris ini ada!
        ]);
    }
}