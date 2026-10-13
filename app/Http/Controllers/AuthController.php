<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditTrail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $activePortal = $request->query('portal', 'admin');
        if (!in_array($activePortal, ['admin', 'teacher', 'student', 'parent'])) {
            $activePortal = 'admin';
        }

        // Institutional Personas Metadata
        $personas = [
            'admin' => [
                'role' => 'admin',
                'title' => 'Super Administrator',
                'subtitle' => 'Executive Console & Academic Registry',
                'user' => User::where('role', 'admin')->first(),
                'badge_color' => 'danger',
                'theme_accent' => '#e53e3e',
                'icon' => 'fa-shield-halved',
                'email' => 'admin@scholar.edu',
                'description' => 'Full administrative command: SIS enrollments, faculty payroll HR, curriculum cohorts, ledger audits, and omni-channel alerts.',
            ],
            'teacher' => [
                'role' => 'teacher',
                'title' => 'Faculty & Teacher',
                'subtitle' => 'Instructional Desk & Digital LMS',
                'user' => User::where('role', 'teacher')->first(),
                'badge_color' => 'success',
                'theme_accent' => '#38a169',
                'icon' => 'fa-chalkboard-user',
                'email' => 'teacher@scholar.edu',
                'description' => 'Manage assigned classroom cohorts, author LMS syllabus units, publish downloadable materials, and mark daily registers.',
            ],
            'student' => [
                'role' => 'student',
                'title' => 'Student Workspace',
                'subtitle' => 'Academic Standing & Course LMS',
                'user' => User::where('role', 'student')->first(),
                'badge_color' => 'warning',
                'theme_accent' => '#d69e2e',
                'icon' => 'fa-user-graduate',
                'email' => 'student@scholar.edu',
                'description' => 'Access personal 360 profile, download learning materials, monitor daily attendance percentages, and review campus bulletins.',
            ],
            'parent' => [
                'role' => 'parent',
                'title' => 'Guardian Oversight',
                'subtitle' => 'Ward Progress & Safety Alerts',
                'user' => User::where('role', 'parent')->first(),
                'badge_color' => 'info',
                'theme_accent' => '#3182ce',
                'icon' => 'fa-people-roof',
                'email' => 'parent@scholar.edu',
                'description' => 'Real-time safety oversight: live attendance verification, syllabus progress, and automated SMS absence alerts for registered wards.',
            ],
        ];

        $allSeedUsers = User::whereIn('email', [
            'admin@scholar.edu',
            'teacher@scholar.edu',
            'teacher2@scholar.edu',
            'student@scholar.edu',
            'student2@scholar.edu',
            'parent@scholar.edu',
            'parent2@scholar.edu',
        ])->get();

        return view('auth.login', compact('personas', 'activePortal', 'allSeedUsers'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'portal' => 'nullable|string|in:admin,teacher,student,parent',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            AuditTrail::log(
                'PORTAL_LOGIN',
                "User {$user->name} ({$user->role}) authenticated successfully from IP: {$request->ip()}.",
                'User',
                $user->id
            );

            $targetPortal = $request->input('portal');
            $flashMsg = "Welcome back, {$user->name}! Secure session established.";

            if ($targetPortal && $targetPortal !== $user->role) {
                $flashMsg = "Welcome, {$user->name}! You have been seamlessly routed to your official " . ucfirst($user->role) . " portal.";
            }

            return redirect()->intended(route('dashboard'))->with('success', $flashMsg);
        }

        AuditTrail::log(
            'FAILED_LOGIN',
            "Failed login attempt for email: " . $request->input('email') . " (IP: " . $request->ip() . ")",
            'User',
            null
        );

        return back()->withErrors([
            'email' => 'Invalid credentials. The provided email or password does not match our institutional directory.',
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

            return redirect()->route('dashboard')->with('success', "Authenticated as " . ucfirst($role) . ": {$targetUser->name}");
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

        return redirect()->route('login')->with('info', 'You have been securely logged out.');
    }
}