<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A customer order; cashier_id is set when it is rung up at the POS.
class Order extends Model
{
    protected $fillable = ['user_id', 'cashier_id', 'customer_phone', 'order_number', 'total_price', 'status', 'cancellation_reason', 'order_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
