<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserMaintenanceController extends Controller
{
    public function index()
    {
        $users = StaffUser::orderBy('created_at', 'desc')->get();

        return view('owner.users', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:staff_users,username',
            'email' => 'required|email|max:255|unique:staff_users,email',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6|confirmed',
        ]);

        StaffUser::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => 'Employee',
            'position' => 'Cashier',
            'branch' => 'Surabaya',
            'phone' => '0812-3456-7890',
            'password' => Hash::make($validated['password']),
            'active' => $validated['status'] === 'active',
        ]);

        return redirect()
            ->route('owner.users')
            ->with('success', 'User berhasil ditambahkan.');
    }
}
