<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function profile()
    {
        return Inertia::render('User/Profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = Auth::user();
        $user->fill($data);
        $user->save();

        return Redirect::back()->with('success', 'Profile updated');
    }

    public function settings()
    {
        return Inertia::render('User/Settings', [
            'user' => Auth::user(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        // Placeholder for settings. Currently no additional settings stored.
        return Redirect::back()->with('success', 'Settings saved');
    }

    public function changePassword()
    {
        return Inertia::render('User/ChangePassword');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return Redirect::back()->withErrors(['current_password' => 'Current password does not match']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return Redirect::back()->with('success', 'Password updated');
    }
}
