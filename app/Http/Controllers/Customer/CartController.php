<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        return view('customer.cart', compact('cartItems'));
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

        $cart = session()->get('cart', []);

        if ($request->boolean('is_custom')) {
            $isi    = $request->input('isi', '');
            $varian = $request->input('varian', '');
            $sauces = $request->input('sauces', '');

            $key = 'custom_' . md5($isi . '|' . $varian . '|' . $sauces);

            if (isset($cart[$key])) {
                $cart[$key]['qty'] += (int) $request->input('qty', 1);
            } else {
                $cart[$key] = [
                    'id'           => $key,
                    'name'         => 'Custom Corndog',
                    'price'        => (int) $request->input('price'),
                    'qty'          => (int) $request->input('qty', 1),
                    'image'        => $request->input('image', ''),
                    'varian_image' => $request->input('varian_image', ''),
                    'sauce_image'  => $request->input('sauce_image', ''),
                    'description'  => $request->input('description', ''),
                    'is_custom'    => true,
                    'isi'          => $isi,
                    'varian'       => $varian,
                    'sauces'       => $sauces,
                ];
            }
        } else {
            $productId = (string) $request->input('product_id');

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
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item ditambahkan ke keranjang.',
            'count'   => count($cart),
        ]);
    }

    public function update(Request $request)
    {
        $productId = (string) $request->input('product_id');
        $qty       = (int) $request->input('qty', 1);

        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
        }

        if ($qty < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['qty'] = $qty;
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
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
