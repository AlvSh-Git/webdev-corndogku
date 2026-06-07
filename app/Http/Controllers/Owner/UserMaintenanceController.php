<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Owner management of cashier accounts (CRUD).
class UserMaintenanceController extends Controller
{
    // List all cashier accounts.
    public function index()
    {
        // User Maintenance manages cashier accounts. Match both the canonical
        // 'cashier' role and the legacy 'employee' alias so every cashier the
        // store has ever had shows up here.
        $users = User::whereIn('role', ['cashier', 'employee'])
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('owner.users', compact('users'));
    }

    // Create a cashier account.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:15',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'status'   => 'required|in:active,inactive',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            // Use the canonical 'cashier' role so the new account passes the
            // cashier route middleware (role:cashier,...) and can log in to
            // the cashier view without registering first.
            'role'     => 'cashier',
            'status'   => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('owner.users')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // Update a cashier account.
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:15',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'status'   => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'status'   => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('owner.users')
            ->with('success', 'User berhasil diupdate.');
    }

    // Delete a cashier account.
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('owner.users')
            ->with('success', 'User berhasil dihapus.');
    }
}
