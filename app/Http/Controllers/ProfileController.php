<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user profile settings dashboard.
     */
    public function show()
    {
        $user = auth()->user();
        return view('portals.profile.index', compact('user'));
    }

    /**
     * Upload / update current user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
        ]);

        $user = auth()->user();
        $oldAvatar = $user->avatar;

        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        // Delete old custom avatar if it was stored locally
        if ($oldAvatar && str_starts_with($oldAvatar, 'storage/avatars/')) {
            $oldPath = str_replace('storage/', '', $oldAvatar);
            Storage::disk('public')->delete($oldPath);
        }

        $user->avatar = 'storage/' . $path;
        $user->save();

        AuditTrail::log(
            'USER_AVATAR_UPDATED',
            "User {$user->name} ({$user->role}) updated their profile photograph",
            'users',
            $user->id
        );

        return redirect()->back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Remove / reset profile avatar to default.
     */
    public function deleteAvatar()
    {
        $user = auth()->user();
        $oldAvatar = $user->avatar;

        if ($oldAvatar && str_starts_with($oldAvatar, 'storage/avatars/')) {
            $oldPath = str_replace('storage/', '', $oldAvatar);
            Storage::disk('public')->delete($oldPath);
        }

        $user->avatar = null;
        $user->save();

        AuditTrail::log(
            'USER_AVATAR_REMOVED',
            "User {$user->name} ({$user->role}) reset their profile photograph to default placeholder",
            'users',
            $user->id
        );

        return redirect()->back()->with('success', 'Profile photo removed and reset to default.');
    }

    /**
     * Update account name, phone, and password.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Current password does not match our records.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->email = $validated['email'];
        $user->save();

        AuditTrail::log(
            'USER_PROFILE_UPDATED',
            "User {$user->name} ({$user->role}) updated profile account details",
            'users',
            $user->id
        );

        return redirect()->back()->with('success', 'Account details updated successfully.');
    }
}
