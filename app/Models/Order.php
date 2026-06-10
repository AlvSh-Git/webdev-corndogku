<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUlid;

// A customer order; cashier_id is set when it is rung up at the POS.
class Order extends Model
{
    use HasUlid;

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

    /**
     * Orders that belong on the staff (cashier/owner) boards.
     *
     * Walk-in POS orders (dine-in/takeaway) are always shown — the cashier
     * created them and a cash sale is paid on the spot. Online (customer
     * self-checkout) orders only surface once their payment is actually
     * confirmed (Paid, or Refunded after a paid cancellation). An online order
     * that was never paid — abandoned, or its QRIS expired — stays Unpaid and is
     * deliberately hidden, so the boards never show a "received" order the
     * customer hasn't paid for.
     */
    public function scopeStaffVisible($query)
    {
        return $query->where(function ($q) {
            $q->where('order_type', '!=', 'online')
              ->orWhereHas('payment', fn ($p) => $p->whereIn('status', ['Paid', 'Refunded']));
        });
    }
}
