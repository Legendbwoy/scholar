@extends('layouts.public')

@section('title', 'Role-Based Portal Authentication — Scholar SMS')

@section('styles')
<style>
    .auth-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 120px 0 60px;
        background: #060606;
        position: relative;
        overflow: hidden;
    }

    .auth-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('{{ asset("knightone/img/hero-bg.jpg") }}') center center / cover no-repeat;
        opacity: 0.18;
        pointer-events: none;
    }

    .auth-card {
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
        background: #121212;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .role-nav-tab {
        border: 2px solid #222222;
        border-radius: 16px;
        padding: 12px 14px;
        background: #181818;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        width: 100%;
        color: #cccccc;
        text-decoration: none;
    }

    .role-nav-tab:hover {
        border-color: #009961;
        background: #1f1f1f;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .role-nav-tab.active {
        border-color: #009961;
        background: rgba(0, 153, 97, 0.15);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(0, 153, 97, 0.25);
    }

    .role-nav-tab .role-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
        color: #fff;
        flex-shrink: 0;
    }

    .brand-hero-panel {
        background: linear-gradient(145deg, #0f1f17 0%, #060e0a 100%);
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
    }

    .persona-badge-btn {
        background: #1e1e1e;
        border: 1px solid #333333;
        color: #e0e0e0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        text-align: left;
    }

    .persona-badge-btn:hover {
        background: #252525;
        border-color: #009961;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-lg-12">
                <div class="auth-card">
                    <div class="row g-0">
                        <!-- Left Brand Hero & Demo Persona Vault -->
                        <div class="col-lg-5 brand-hero-panel p-4 p-md-5 d-flex flex-column justify-content-between text-white">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="p-2 rounded-3 text-white shadow" style="width: 48px; height: 48px; display: grid; place-items: center; background-color: #009961;">
                                        <i class="fa-solid fa-graduation-cap fs-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px;">Scholar<span style="color: #009961;">SMS</span></h3>
                                        <small class="text-white-50">KnightOne Enterprise Edition</small>
                                    </div>
                                </div>

                                <h4 class="fw-bold mb-3 text-white" style="font-family: 'Raleway', sans-serif;">Institutional Workspace Access</h4>
                                <p class="text-white-50 small mb-4">
                                    Authenticate into your role-specific dashboard. All sessions are cryptographically verified and bound to institutional audit trails.
                                </p>

                                <div class="p-3 rounded-4 mb-4" style="background: rgba(0, 153, 97, 0.1); border: 1px solid rgba(0, 153, 97, 0.25);">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold small">
                                        <i class="bi bi-shield-check fs-5"></i> 1-Click Fast Demo Credentials
                                    </div>
                                    <div class="text-white-50 small mb-3">Click any profile below to instantly populate login fields:</div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="persona-badge-btn" onclick="applyPersona('admin@scholar.edu', 'password', 'admin')">
                                            <span><i class="fa-solid fa-user-shield text-danger me-2"></i> Super Administrator</span>
                                            <span class="badge bg-danger">Admin</span>
                                        </button>
                                        <button type="button" class="persona-badge-btn" onclick="applyPersona('sarah.jenkins@scholar.edu', 'password', 'teacher')">
                                            <span><i class="fa-solid fa-chalkboard-user text-success me-2"></i> Dr. Sarah Jenkins (HOD)</span>
                                            <span class="badge bg-success">Faculty</span>
                                        </button>
                                        <button type="button" class="persona-badge-btn" onclick="applyPersona('alexander.cole@student.scholar.edu', 'password', 'student')">
                                            <span><i class="fa-solid fa-user-graduate text-warning me-2"></i> Alexander Cole (Grade 10)</span>
                                            <span class="badge bg-warning text-dark">Student</span>
                                        </button>
                                        <button type="button" class="persona-badge-btn" onclick="applyPersona('arthur.cole@parent.scholar.edu', 'password', 'parent')">
                                            <span><i class="fa-solid fa-people-roof text-info me-2"></i> Arthur Cole (Guardian)</span>
                                            <span class="badge bg-info text-dark">Parent</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center small text-white-50">
                                <span><i class="fa-solid fa-lock text-success me-1"></i> 256-Bit SSL Enforced</span>
                                <a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-text-white"><i class="bi bi-arrow-left me-1"></i> Public Site</a>
                            </div>
                        </div>

                        <!-- Right Form & Role Navigation -->
                        <div class="col-lg-7 p-4 p-md-5 bg-dark text-white">
                            <div class="mb-4">
                                <span class="text-success text-uppercase fw-bold small" style="letter-spacing: 1px;">Step 1: Choose Your Role</span>
                                <h4 class="fw-bold mt-1 text-white">Select Academic Persona</h4>
                            </div>

                            <!-- Role Selection Tabs -->
                            <div class="row g-2 mb-4">
                                <div class="col-sm-6">
                                    <div class="role-nav-tab active" id="tab-admin" onclick="switchRoleTab('admin', 'admin@scholar.edu')">
                                        <div class="role-icon-box bg-danger"><i class="fa-solid fa-user-shield"></i></div>
                                        <div>
                                            <div class="fw-bold text-white small">Administrator</div>
                                            <small class="text-white-50" style="font-size: 11px;">Operations & Security</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="role-nav-tab" id="tab-teacher" onclick="switchRoleTab('teacher', 'sarah.jenkins@scholar.edu')">
                                        <div class="role-icon-box bg-success"><i class="fa-solid fa-chalkboard-user"></i></div>
                                        <div>
                                            <div class="fw-bold text-white small">Faculty Member</div>
                                            <small class="text-white-50" style="font-size: 11px;">LMS, Grades & Attendance</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="role-nav-tab" id="tab-student" onclick="switchRoleTab('student', 'alexander.cole@student.scholar.edu')">
                                        <div class="role-icon-box bg-warning text-dark"><i class="fa-solid fa-user-graduate"></i></div>
                                        <div>
                                            <div class="fw-bold text-white small">Student Scholar</div>
                                            <small class="text-white-50" style="font-size: 11px;">Courses, Grades & 360</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="role-nav-tab" id="tab-parent" onclick="switchRoleTab('parent', 'arthur.cole@parent.scholar.edu')">
                                        <div class="role-icon-box bg-info text-dark"><i class="fa-solid fa-people-roof"></i></div>
                                        <div>
                                            <div class="fw-bold text-white small">Guardian / Parent</div>
                                            <small class="text-white-50" style="font-size: 11px;">Ward Progress & Reports</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-success text-uppercase fw-bold small" style="letter-spacing: 1px;">Step 2: Sign In</span>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger border-0 rounded-3 small mb-4">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white-50 fw-semibold">Institutional Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-black text-white-50 border-secondary border-opacity-50"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="emailInput" class="form-control bg-black text-white border-secondary border-opacity-50 py-2.5" value="admin@scholar.edu" required autocomplete="username">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small text-white-50 fw-semibold mb-0">Secret Passcode</label>
                                        <a href="javascript:void(0)" class="small text-success text-decoration-none" onclick="Swal.fire({icon: 'info', title: 'Default Password', text: 'All demo accounts are seeded with password: password', confirmButtonColor: '#009961'})">Forgot?</a>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-black text-white-50 border-secondary border-opacity-50"><i class="bi bi-key"></i></span>
                                        <input type="password" name="password" id="passwordInput" class="form-control bg-black text-white border-secondary border-opacity-50 py-2.5" value="password" required autocomplete="current-password">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4 small text-white-50">
                                    <div class="form-check">
                                        <input class="form-check-input bg-black border-secondary" type="checkbox" name="remember" id="rememberMe" checked>
                                        <label class="form-check-label" for="rememberMe">Remember Session</label>
                                    </div>
                                    <span class="badge bg-black border border-secondary text-white-50">Demo Pass: password</span>
                                </div>

                                <button type="submit" class="btn btn-lg w-100 rounded-pill py-2.5 fw-bold shadow-lg text-white" style="background-color: #009961; border: none;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Academic Portal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function switchRoleTab(role, email) {
        document.querySelectorAll('.role-nav-tab').forEach(tab => tab.classList.remove('active'));
        const activeTab = document.getElementById('tab-' + role);
        if (activeTab) activeTab.classList.add('active');

        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';
    }

    function applyPersona(email, password, role) {
        switchRoleTab(role, email);
        document.getElementById('passwordInput').value = password;

        Toast.fire({
            icon: 'success',
            title: 'Selected persona credentials applied!'
        });
    }
</script>
@endsection
