<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Building block for the custom corndog builder (filling / coating / topping).
class Component extends Model
{
    protected $fillable = ['type', 'name', 'price', 'image'];
}
