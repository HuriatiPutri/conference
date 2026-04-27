<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Show the member login form.
     */
    public function showMemberLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Show the admin login form.
     */
    public function showAdminLoginForm(): Response
    {
        return Inertia::render('Auth/LoginAdmin');
    }

    /**
     * Handle member authentication attempt.
     */
    public function loginMember(Request $request): RedirectResponse
    {
        return $this->loginByRole($request, 'user');
    }

    /**
     * Handle admin authentication attempt.
     */
    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->loginByRole($request, 'admin');
    }

    private function loginByRole(Request $request, string $role): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (!$user || optional($user->role)->name !== $role) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => $role === 'admin'
                        ? 'Akun ini bukan akun admin.'
                        : 'Akun ini bukan akun member.',
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.member');
    }
}
