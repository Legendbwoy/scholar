<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditTrail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            AuditTrail::log(
                'LOGIN',
                "User " . Auth::user()->name . " (" . Auth::user()->role . ") logged in successfully.",
                'User',
                Auth::id()
            );

            return redirect()->intended(route('dashboard'));
        }

        AuditTrail::log(
            'FAILED_LOGIN',
            "Failed login attempt for email: " . $request->input('email'),
            'User',
            null
        );

        return back()->withErrors([
            'email' => 'The provided credentials do not match our institutional records.',
        ])->onlyInput('email');
    }

    public function switchRole($role)
    {
        $allowed = ['admin', 'teacher', 'student', 'parent'];
        if (!in_array($role, $allowed)) {
            abort(400, 'Invalid role');
        }

        $targetUser = User::where('role', $role)->first();
        if ($targetUser) {
            Auth::login($targetUser);
            session()->regenerate();

            AuditTrail::log(
                'ROLE_SWITCH',
                "Direct demo session switch to role [{$role}] as user: {$targetUser->name}",
                'User',
                $targetUser->id
            );

            return redirect()->route('dashboard')->with('success', "Switched to demo {$role} profile: {$targetUser->name}");
        }

        return redirect()->route('login')->with('error', 'Demo user for role not found.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditTrail::log(
                'LOGOUT',
                "User " . Auth::user()->name . " logged out.",
                'User',
                Auth::id()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}