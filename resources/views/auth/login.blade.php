@extends('layouts.public')

@section('title', 'Role-Based Portal Authentication — Scholar SMS')

@section('styles')
<style>
    .auth-wrapper {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        padding: 100px 0 60px;
        background: radial-gradient(circle at 10% 20%, #f4f2ff 0%, #ffffff 50%, #fbfbff 100%);
    }

    .auth-card {
        border-radius: 26px;
        border: 1px solid rgba(122, 106, 216, 0.12);
        box-shadow: 0 25px 60px rgba(122, 106, 216, 0.14);
        background: #fff;
        overflow: hidden;
    }

    /* Role Selection Tabs */
    .role-nav-tab {
        border: 2px solid #eef0f6;
        border-radius: 16px;
        padding: 12px 14px;
        background: #fafbfe;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        width: 100%;
        color: inherit;
        text-decoration: none;
    }

    .role-nav-tab:hover {
        border-color: #7a6ad8;
        background: #f7f5ff;
        transform: translateY(-2px);
    }

    .role-nav-tab.active {
        border-color: #7a6ad8;
        background: #f0eeff;
        box-shadow: 0 6px 16px rgba(122, 106, 216, 0.15);
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

    .role-nav-tab.active .role-icon-box {
        transform: scale(1.05);
    }

    .seed-vault-card {
        border: 1px solid #edf0f7;
        border-radius: 14px;
        padding: 10px 14px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .seed-vault-card:hover {
        border-color: #7a6ad8;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .brand-hero-panel {
        background: linear-gradient(150deg, #7a6ad8 0%, #513fc4 100%);
        position: relative;
        overflow: hidden;
    }

    .brand-hero-panel::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .persona-preview-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-lg-12">
                <div class="auth-card">
                    <div class="row g-0">
                        
                        <!-- Left Hero Persona Panel -->
                        <div class="col-lg-5 p-4 p-md-5 text-white d-flex flex-column justify-content-between brand-hero-panel">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="p-2 rounded-circle bg-white text-primary shadow-sm" style="width: 44px; height: 44px; display: grid; place-items: center;">
                                        <i class="fa-solid fa-graduation-cap fs-5" style="color: #7a6ad8;"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">Scholar<span>SMS</span></h4>
                                        <small class="opacity-75" style="font-size: 11px;">Unified Academic Portal System</small>
                                    </div>
                                </div>

                                <h3 class="fw-bold mb-2">Role-Based Authentication</h3>
                                <p class="opacity-75 small mb-4">Enterprise access control tailored for Super Administrators, Faculty Instructors, Students, and Guardians.</p>

                                <!-- Dynamic Persona Preview Card -->
                                <div class="persona-preview-box mb-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img id="personaAvatar" src="{{ asset($personas[$activePortal]['user']->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 58px; height: 58px; object-fit: cover;">
                                        <div>
                                            <span id="personaRoleBadge" class="badge bg-{{ $personas[$activePortal]['badge_color'] }} px-2 py-1 mb-1 fw-bold text-uppercase" style="font-size: 10px;">
                                                {{ $personas[$activePortal]['title'] }}
                                            </span>
                                            <h5 id="personaName" class="fw-bold mb-0 text-white">{{ $personas[$activePortal]['user']->name ?? 'Dr. Evelyn Vance' }}</h5>
                                            <small id="personaSubtitle" class="text-white-50" style="font-size: 11px;">{{ $personas[$activePortal]['subtitle'] }}</small>
                                        </div>
                                    </div>
                                    <p id="personaDescription" class="small mb-0 opacity-90" style="font-size: 12px; line-height: 1.5;">
                                        {{ $personas[$activePortal]['description'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Institutional Trust Seals -->
                            <div class="border-top border-white border-opacity-20 pt-3">
                                <div class="row g-2 text-center" style="font-size: 11px;">
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white bg-opacity-10">
                                            <i class="fa-solid fa-shield-halved text-warning d-block mb-1"></i>
                                            <span>SHA-256 Ledger</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white bg-opacity-10">
                                            <i class="fa-solid fa-qrcode text-success d-block mb-1"></i>
                                            <span>Smart QR Check-In</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 rounded bg-white bg-opacity-10">
                                            <i class="fa-solid fa-tower-broadcast text-info d-block mb-1"></i>
                                            <span>Instant Comms</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="opacity-75">Institutional Default Password: <code>password</code></small>
                                </div>
                            </div>
                        </div>

                        <!-- Right Authentication Form Panel -->
                        <div class="col-lg-7 p-4 p-md-5 bg-white d-flex flex-column justify-content-between">
                            <div>
                                <!-- Section Title -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h3 class="fw-bold text-dark mb-0">Select Your Role Portal</h3>
                                        <small class="text-muted">Choose your persona to populate seeded credentials or authenticate manually.</small>
                                    </div>
                                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill fw-bold small">
                                        <i class="fa-solid fa-lock me-1"></i> SSL 256-Bit
                                    </span>
                                </div>

                                <!-- 4-Way Role Portal Selector Buttons -->
                                <div class="row g-2 mb-4">
                                    @foreach($personas as $rKey => $p)
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="role-nav-tab {{ $activePortal === $rKey ? 'active' : '' }}" 
                                                onclick="selectPortal('{{ $rKey }}', '{{ $p['title'] }}', '{{ $p['subtitle'] }}', '{{ $p['user']->name ?? '' }}', '{{ asset($p['user']->avatar ?? 'scholar/assets/images/member-01.jpg') }}', '{{ $p['badge_color'] }}', '{{ $p['email'] }}', '{{ addslashes($p['description']) }}')"
                                                id="tab-btn-{{ $rKey }}">
                                            <div class="role-icon-box bg-{{ $p['badge_color'] }}">
                                                <i class="fa-solid {{ $p['icon'] }}"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <strong class="d-block small text-truncate">{{ $p['title'] }}</strong>
                                                <span class="text-muted text-truncate d-block" style="font-size: 10px;">{{ $p['email'] }}</span>
                                            </div>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show rounded-4 py-2 px-3 small mb-3">
                                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger rounded-4 py-2 px-3 small mb-3">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form action="{{ route('login.post') }}" method="POST" id="authLoginForm">
                                    @csrf
                                    <input type="hidden" name="portal" id="portalRoleInput" value="{{ $activePortal }}">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-muted">Academic Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                            <input type="email" name="email" id="emailInput" class="form-control border-start-0 ps-0" placeholder="e.g. admin@scholar.edu" value="{{ old('email', $personas[$activePortal]['email']) }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-semibold small text-muted mb-0">Password</label>
                                            <a href="javascript:void(0)" class="text-primary small text-decoration-none" onclick="alert('Seeded institutional accounts all use password: password');">Default: password</a>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                            <input type="password" name="password" id="passwordInput" class="form-control border-start-0 border-end-0 ps-0" placeholder="••••••••" value="password" required>
                                            <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()">
                                                <i class="fa-regular fa-eye" id="toggleEyeIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="remember" class="form-check-input" id="rememberMe" checked>
                                            <label class="form-check-label small text-muted" for="rememberMe">Remember my session</label>
                                        </div>
                                        <span class="badge bg-light text-muted border small">
                                            Active Target: <span id="targetRoleLabel" class="fw-bold text-dark text-capitalize">{{ $activePortal }}</span>
                                        </span>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-7">
                                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="background-color: #7a6ad8; border-color: #7a6ad8;">
                                                <i class="fa-solid fa-right-to-bracket me-2"></i> Log In to Portal
                                            </button>
                                        </div>
                                        <div class="col-md-5">
                                            <a href="{{ route('auth.demo.switch', $activePortal) }}" id="instantDemoLink" class="btn btn-outline-dark w-100 rounded-pill py-2 fw-semibold small">
                                                <i class="fa-solid fa-bolt text-warning me-1"></i> 1-Click Instant
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Expandable Seeded Credentials Vault -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-muted small fw-bold text-uppercase mb-0">
                                        <i class="fa-solid fa-users-viewfinder text-primary me-1"></i> Seeded Institutional Accounts Vault
                                    </label>
                                    <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#seedVaultCollapse">
                                        Toggle All 8 Accounts <i class="fa-solid fa-chevron-down ms-1"></i>
                                    </button>
                                </div>

                                <div class="collapse show" id="seedVaultCollapse">
                                    <div class="row g-2" style="font-size: 12px;">
                                        @foreach($allSeedUsers as $u)
                                        <div class="col-md-6 col-12">
                                            <div class="seed-vault-card d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                    <img src="{{ asset($u->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                                    <div class="text-truncate">
                                                        <div class="fw-bold text-truncate text-dark">{{ $u->name }}</div>
                                                        <span class="text-muted font-monospace" style="font-size: 10px;">{{ $u->email }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1 flex-shrink-0">
                                                    <button type="button" class="btn btn-sm btn-light py-0 px-2 border" title="Auto-fill Credentials"
                                                            onclick="fillCredentials('{{ $u->email }}', '{{ $u->role }}')">
                                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                                                    </button>
                                                    <a href="{{ route('auth.demo.switch', $u->role) }}" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 11px;">
                                                        Enter
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectPortal(roleKey, title, subtitle, name, avatar, badgeColor, email, desc) {
        // 1. Update tabs active state
        document.querySelectorAll('.role-nav-tab').forEach(tab => tab.classList.remove('active'));
        const activeTab = document.getElementById('tab-btn-' + roleKey);
        if (activeTab) activeTab.classList.add('active');

        // 2. Update Hidden Portal input
        document.getElementById('portalRoleInput').value = roleKey;
        document.getElementById('targetRoleLabel').textContent = title;

        // 3. Pre-fill Form Credentials
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';

        // 4. Update Instant 1-Click Link
        const instantLink = document.getElementById('instantDemoLink');
        instantLink.href = "{{ url('/auth/demo') }}/" + roleKey;
        instantLink.innerHTML = '<i class="fa-solid fa-bolt text-warning me-1"></i> Instant as ' + roleKey.charAt(0).toUpperCase() + roleKey.slice(1);

        // 5. Update Left Hero Persona Preview
        document.getElementById('personaAvatar').src = avatar;
        document.getElementById('personaName').textContent = name;
        document.getElementById('personaSubtitle').textContent = subtitle;
        document.getElementById('personaDescription').textContent = desc;

        const badge = document.getElementById('personaRoleBadge');
        badge.className = 'badge bg-' + badgeColor + ' px-2 py-1 mb-1 fw-bold text-uppercase';
        badge.textContent = title;
    }

    function fillCredentials(email, role) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = 'password';
        document.getElementById('portalRoleInput').value = role;
        document.getElementById('targetRoleLabel').textContent = role.toUpperCase();
        
        // Also trigger the tab if exists
        const tabBtn = document.getElementById('tab-btn-' + role);
        if (tabBtn) tabBtn.click();
    }

    function togglePasswordVisibility() {
        const passInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('toggleEyeIcon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection