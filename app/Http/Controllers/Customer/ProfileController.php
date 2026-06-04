<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('customer.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required_with:new_password', 'string'],
            'new_password'     => ['nullable', 'string', 'min:8', 'same:confirm_password'],
        ]);

        // Verify current password before allowing a change
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak sesuai.',
                ], 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->phone = $request->phone ?? null;

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil disimpan.',
            'name'    => $user->name,
            'phone'   => $user->phone,
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete the old photo file if one exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile_photos', 'public');

        $user->profile_photo = $path;
        $user->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Foto profil berhasil diperbarui.',
            'photo_url' => Storage::url($path),
        ]);
    }
}
