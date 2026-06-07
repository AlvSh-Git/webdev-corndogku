<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A single line on an order; custom_notes (JSON) holds the custom corndog parts.
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'quantity', 'subtotal', 'custom_notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
