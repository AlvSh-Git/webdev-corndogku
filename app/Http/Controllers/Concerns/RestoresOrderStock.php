<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait RestoresOrderStock
{
    /**
     * Return each item's quantity to stock — to the product for catalog items,
     * or to the matching custom-component products for a custom corndog
     * (isi + varian + each sauce). Used when an order is cancelled/expired.
     */
    protected function restoreStockForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)
                    ->update(['stock' => DB::raw('stock + ' . (int) $item->quantity)]);
                continue;
            }

            if (!$item->custom_notes) {
                continue;
            }

            $notes       = json_decode($item->custom_notes, true) ?? [];
            $ingredients = array_values(array_filter([
                $notes['isi']    ?? null,
                $notes['varian'] ?? null,
            ]));
            if (!empty($notes['sauces'])) {
                foreach (array_map('trim', explode(',', $notes['sauces'])) as $sauce) {
                    if ($sauce !== '') {
                        $ingredients[] = $sauce;
                    }
                }
            }

            foreach ($ingredients as $ingredientName) {
                Product::whereHas('category', fn ($c) => $c->whereRaw('LOWER(name) = ?', ['custom']))
                    ->whereRaw('UPPER(name) = ?', [strtoupper(trim($ingredientName))])
                    ->update(['stock' => DB::raw('stock + ' . (int) $item->quantity)]);
            }
        }
    }
}
