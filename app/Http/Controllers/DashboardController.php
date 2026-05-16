<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $role = $this->currentRole();
        $view = $role === 'cashier' ? 'dashboard.employee' : 'dashboard.owner';
        return view($view, compact('role'));
    }
}
