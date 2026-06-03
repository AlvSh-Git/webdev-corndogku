<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        // Jika produk sudah di-wishlist, hapus (detach). Jika belum, tambahkan (attach).
        $isAttached = $user->wishlistProducts()->toggle($productId);
        
        $exists = in_array($productId, $isAttached['attached']);
        $status = $exists ? 'added' : 'removed';
        $message = $exists ? 'Produk berhasil ditambahkan ke wishlist.' : 'Produk dihapus dari wishlist.';

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'count' => $user->wishlistProducts()->count()
        ]);
    }
}