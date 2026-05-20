<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $role        = $this->currentRole();
        $view        = $role === 'cashier' ? 'dashboard.employee' : 'dashboard.owner';
        $storeStatus = Cache::get('store_status', 'available');

        return view($view, compact('role', 'storeStatus'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:available,unavailable'],
        ]);

        Cache::put('store_status', $request->status, now()->addHours(24));

        return response()->json(['success' => true, 'status' => $request->status]);
    }
}
