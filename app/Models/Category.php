<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUlid;

// Product grouping (e.g. Original, Mozza, Custom).
class Category extends Model
{
    use HasFactory, HasUlid;
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
