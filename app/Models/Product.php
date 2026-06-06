<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Sellable menu item; is_custom flags the build-your-own template.
class Product extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock', 'low_stock', 'cost_price', 'image', 'is_custom', 'is_available'];

    protected $casts = [
        'is_custom'    => 'boolean',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
}
