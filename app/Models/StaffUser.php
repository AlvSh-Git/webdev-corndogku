<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffUser extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'position',
        'branch',
        'phone',
        'password',
        'active',
    ];
}
