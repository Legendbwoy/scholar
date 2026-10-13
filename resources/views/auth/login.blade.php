@extends('layouts.public')

@section('title', 'Institutional Portal Login — Scholar SMS')

@section('styles')
<style>
    .login-container {
        min-height: calc(100vh - 160px);
        display: flex;
        align-items: center;
        padding: 120px 0 60px;
        background: radial-gradient(circle at top right, #f2f0fc 0%, #ffffff 60%);
    }
    .login-card {
        border-radius: 24px;
        border: none;
        box-shadow: 0 20px 50px rgba(122, 106, 216, 0.12);
        background: #fff;
        overflow: hidden;
    }
    .demo-role-card {
        cursor: pointer;
        border: 2px solid #eef0f6;
        border-radius: 14px;
        padding: 12px 14px;
        transition: all 0.25s ease;
        background: #fafbfe;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        color: inherit;
    }
    .demo-role-card:hover {
        border-color: var(--scholar-primary);
        background: rgba(122, 106, 216, 0.05);
        transform: translateY(-2px);
        color: var(--scholar-primary);
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="login-card">
                    <div class="row g-0">
                        <!-- Left Hero Info Panel -->
                        <div class="col-lg-5 p-4 p-md-5 text-white d-flex flex-column justify-content-between" style="background: linear-gradient(145deg, #7a6ad8 0%, #513fc4 100%);">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="fa-solid fa-graduation-cap fs-2 text-warning"></i>
                                    <h3 class="fw-bold mb-0">Scholar SMS</h3>
                                </div>
                                <h4 class="fw-bold mb-3">Enterprise School Management & LMS</h4>
                                <p class="opacity-75 small">Secure institutional access for School Administrators, Faculty Fellows, Enrolled Students, and Guardians.</p>
                            </div>

                            <div class="my-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="p-2 rounded-circle bg-white text-primary" style="width: 36px; height: 36px; display: grid; place-items: center;">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                    <small>Tamper-Evident SHA-256 Ledger</small>
                                </div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="p-2 rounded-circle bg-white text-primary" style="width: 36px; height: 36px; display: grid; place-items: center;">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <small>Omni-Channel Instant Comms</small>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-circle bg-white text-primary" style="width: 36px; height: 36px; display: grid; place-items: center;">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </div>
                                    <small>Contactless QR Smart Attendance</small>
                                </div>
                            </div>

                            <div class="pt-3 border-top border-white border-opacity-25">
                                <small class="opacity-75">Demo password for all seed accounts: <code>password</code></small>
                            </div>
                        </div>

                        <!-- Right Form Panel -->
                        <div class="col-lg-7 p-4 p-md-5 bg-white">
                            <div class="mb-4">
                                <h3 class="fw-bold text-dark mb-1">Institutional Sign In</h3>
                                <p class="text-muted small">Enter your academic email and credentials or choose a 1-click demo role below.</p>
                            </div>

                            <!-- 1-Click Quick Demo Switcher -->
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1">
                                    <i class="fa-solid fa-bolt text-warning me-1"></i> Quick 1-Click Demo Profiles:
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('auth.demo.switch', 'admin') }}" class="demo-role-card">
                                            <div class="p-2 rounded-circle bg-primary text-white"><i class="fa-solid fa-user-shield"></i></div>
                                            <div>
                                                <strong class="d-block small">Super Admin</strong>
                                                <span class="text-muted" style="font-size: 11px;">Dr. Vance</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('auth.demo.switch', 'teacher') }}" class="demo-role-card">
                                            <div class="p-2 rounded-circle bg-success text-white"><i class="fa-solid fa-chalkboard-user"></i></div>
                                            <div>
                                                <strong class="d-block small">Teacher / HOD</strong>
                                                <span class="text-muted" style="font-size: 11px;">Prof. Sterling</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('auth.demo.switch', 'student') }}" class="demo-role-card">
                                            <div class="p-2 rounded-circle bg-warning text-dark"><i class="fa-solid fa-user-graduate"></i></div>
                                            <div>
                                                <strong class="d-block small">Student</strong>
                                                <span class="text-muted" style="font-size: 11px;">Alexander Cole</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('auth.demo.switch', 'parent') }}" class="demo-role-card">
                                            <div class="p-2 rounded-circle bg-info text-white"><i class="fa-solid fa-people-roof"></i></div>
                                            <div>
                                                <strong class="d-block small">Parent</strong>
                                                <span class="text-muted" style="font-size: 11px;">Arthur Cole</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="position-relative my-4 text-center">
                                <hr class="text-muted">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-semibold">or log in manually</span>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger rounded-3 py-2 px-3 small mb-3">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Academic Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" id="emailInput" class="form-control border-start-0 ps-0" placeholder="e.g. admin@scholar.edu" value="{{ old('email', 'admin@scholar.edu') }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold small mb-0">Password</label>
                                        <a href="#" class="text-muted small text-decoration-none" onclick="alert('Demo password is: password'); return false;">Forgot?</a>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                        <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="••••••••" value="password" required>
                                    </div>
                                </div>

                                <div class="mb-4 form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe" checked>
                                    <label class="form-check-label small text-muted" for="rememberMe">Remember my institutional session</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-right-to-bracket me-2"></i> Enter Secure Portal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection