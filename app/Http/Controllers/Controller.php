<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function currentRole(): string
    {
        $segment = request()->segment(1);
        return in_array($segment, ['owner', 'cashier']) ? $segment : 'owner';
    }
}
