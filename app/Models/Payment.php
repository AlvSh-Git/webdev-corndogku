<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasUlid;

// Payment record for an order (tracks the Midtrans status).
class Payment extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = ['order_id', 'payment_method', 'amount', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
