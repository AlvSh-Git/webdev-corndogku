<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUlid;

// Building block for the custom corndog builder (filling / coating / topping).
class Component extends Model
{
    use HasUlid;

    protected $fillable = ['type', 'name', 'price', 'image'];
}
