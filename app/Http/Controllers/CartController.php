<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $status = $this->calcStoreStatus();
        if (!$status['is_open']) {
            $reopen = ($status['reopen_day'] && $status['reopen_time'])
                ? ' Toko akan buka kembali pada ' . $status['reopen_day'] . ' pukul ' . $status['reopen_time'] . '.'
                : '';
            return response()->json([
                'success'    => false,
                'error'      => 'store_closed',
                'message'    => 'Maaf, toko sedang tutup.' . $reopen,
                'store_info' => $status,
            ], 422);
        }

        $productId = (string) $request->input('product_id');

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += (int) $request->input('qty', 1);
        } else {
            $cart[$productId] = [
                'id'          => $productId,
                'name'        => $request->input('name'),
                'price'       => (int) $request->input('price'),
                'qty'         => (int) $request->input('qty', 1),
                'image'       => $request->input('image'),
                'description' => $request->input('description', ''),
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item ditambahkan ke keranjang.',
            'count'   => count($cart),
        ]);
    }

    public function remove(Request $request)
    {
        $productId = (string) $request->input('product_id');

        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang.',
            'count'   => count($cart),
        ]);
    }

    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan.',
            'count'   => 0,
        ]);
    }
}
