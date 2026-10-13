<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Scholar SMS Portal')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="{{ asset('scholar/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --portal-primary: #7a6ad8;
            --portal-primary-dark: #5848bb;
            --portal-secondary: #f35525;
            --portal-sidebar-bg: #1a1a2e;
            --portal-sidebar-hover: #262644;
            --portal-bg: #f5f7fb;
            --portal-card-bg: #ffffff;
            --portal-text: #2d3748;
            --portal-border: #e2e8f0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--portal-bg);
            color: var(--portal-text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        #portal-sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--portal-sidebar-bg);
            color: #fff;
            transition: all 0.3s ease;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        #portal-sidebar .brand-header {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        #portal-sidebar .brand-header h4 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
            color: #fff;
        }

        #portal-sidebar .brand-header h4 span {
            color: var(--portal-secondary);
        }

        .user-chip {
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.04);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-chip img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--portal-primary);
        }

        .role-badge-admin { background: #e53e3e; color: #fff; }
        .role-badge-teacher { background: #38a169; color: #fff; }
        .role-badge-student { background: #d69e2e; color: #fff; }
        .role-badge-parent { background: #3182ce; color: #fff; }

        .nav-category {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #718096;
            padding: 18px 24px 6px;
            letter-spacing: 1px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 24px;
            color: #a0aec0;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            color: #fff;
            background-color: var(--portal-sidebar-hover);
            border-left-color: var(--portal-primary);
        }

        .sidebar-nav li a i {
            width: 20px;
            font-size: 15px;
            text-align: center;
        }

        /* Topbar & Main Body */
        #portal-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        #portal-topbar {
            height: 70px;
            background: #fff;
            border-bottom: 1px solid var(--portal-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .main-content {
            padding: 30px;
            flex-grow: 1;
        }

        /* UI Cards & Elements */
        .portal-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--portal-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: box-shadow 0.2s ease;
        }

        .portal-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .kpi-card {
            border-radius: 16px;
            padding: 24px;
            background: #fff;
            border: 1px solid var(--portal-border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .kpi-card .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 24px;
        }

        .kpi-card h3 {
            font-size: 28px;
            font-weight: 800;
            margin: 4px 0 0;
        }

        .kpi-card p {
            color: #718096;
            font-size: 13px;
            font-weight: 500;
            margin: 0;
        }

        .table-portal th {
            background: #f8fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--portal-border);
            padding: 14px 16px;
        }

        .table-portal td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #edf2f7;
        }

        .btn-portal-primary {
            background: var(--portal-primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-portal-primary:hover {
            background: var(--portal-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .demo-switcher-dropdown .dropdown-item {
            font-size: 13px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 991px) {
            #portal-sidebar {
                margin-left: -270px;
            }
            #portal-sidebar.show {
                margin-left: 0;
            }
            #portal-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <div id="portal-sidebar">
        <!-- Brand Header -->
        <div class="brand-header">
            <i class="fa-solid fa-graduation-cap fs-3 text-warning"></i>
            <div>
                <h4>Scholar<span>SMS</span></h4>
                <small class="text-secondary" style="font-size: 11px;">
                    @if(auth()->user()->isAdmin()) Executive Admin Console
                    @elseif(auth()->user()->isTeacher()) Faculty & Academic Desk
                    @elseif(auth()->user()->isStudent()) Student Academic Portal
                    @elseif(auth()->user()->isParent()) Guardian Oversight Portal
                    @endif
                </small>
            </div>
        </div>

        <!-- User Profile Chip with 1-Click Role Switcher -->
        <div class="user-chip">
            <img src="{{ asset(auth()->user()->avatar ?? 'scholar/assets/images/member-01.jpg') }}" alt="{{ auth()->user()->name }}">
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-bold text-truncate small">{{ auth()->user()->name }}</div>
                <div class="dropdown demo-switcher-dropdown">
                    <button class="btn btn-sm p-0 text-white-50 dropdown-toggle small text-capitalize" type="button" data-bs-toggle="dropdown" style="font-size: 11px;">
                        <span class="badge role-badge-{{ auth()->user()->role }} me-1">{{ ucfirst(auth()->user()->role) }}</span> Switch
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-lg">
                        <li><h6 class="dropdown-header small text-uppercase">1-Click Demo Profiles</h6></li>
                        <li><a class="dropdown-item" href="{{ route('auth.demo.switch', 'admin') }}"><i class="fa-solid fa-user-shield text-danger"></i> Super Admin</a></li>
                        <li><a class="dropdown-item" href="{{ route('auth.demo.switch', 'teacher') }}"><i class="fa-solid fa-chalkboard-user text-success"></i> Teacher (HOD)</a></li>
                        <li><a class="dropdown-item" href="{{ route('auth.demo.switch', 'student') }}"><i class="fa-solid fa-user-graduate text-warning"></i> Student</a></li>
                        <li><a class="dropdown-item" href="{{ route('auth.demo.switch', 'parent') }}"><i class="fa-solid fa-people-roof text-info"></i> Parent</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Role-Specific Modular Navigation Menus -->
        <ul class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                @include('layouts.navigation.admin')
            @elseif(auth()->user()->isTeacher())
                @include('layouts.navigation.teacher')
            @elseif(auth()->user()->isStudent())
                @include('layouts.navigation.student')
            @elseif(auth()->user()->isParent())
                @include('layouts.navigation.parent')
            @endif

            <li class="nav-category">System</li>
            <li>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fa-solid fa-globe"></i> <span>Public School Site <i class="fa-solid fa-arrow-up-right-from-square small ms-1"></i></span>
                </a>
            </li>
        </ul>

        <!-- Bottom Session Actions -->
        <div class="p-3 border-top border-white border-opacity-10 text-center">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill">
                    <i class="fa-solid fa-power-off me-1"></i> Sign Out
                </button>
            </form>
        </div>
    </div>

    <!-- Wrapper -->
    <div id="portal-wrapper">
        <!-- Topbar -->
        <div id="portal-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="sidebar-toggle" onclick="document.getElementById('portal-sidebar').classList.toggle('show')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-2">
                    @if(auth()->user()->isAdmin())
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> API: Ready
                        </span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                            <i class="fa-solid fa-shield-halved me-1"></i> Ledger: SHA-256 Chained
                        </span>
                    @elseif(auth()->user()->isTeacher())
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                            <i class="fa-solid fa-id-badge me-1"></i> {{ auth()->user()->staff?->employee_id ?? 'EMP' }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="fa-solid fa-chalkboard me-1"></i> Faculty Standing: Active
                        </span>
                    @elseif(auth()->user()->isStudent())
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 small">
                            <i class="fa-solid fa-id-card me-1"></i> {{ auth()->user()->student?->admission_number ?? 'SCH' }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="fa-solid fa-calendar-check me-1"></i> Attendance: {{ auth()->user()->student?->attendance_rate ?? 100 }}%
                        </span>
                    @elseif(auth()->user()->isParent())
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 small">
                            <i class="fa-solid fa-people-roof me-1"></i> Verified Guardian
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="fa-solid fa-bell me-1"></i> Instant SMS Active
                        </span>
                    @endif
                </div>
            </div>

            <!-- Topbar Right Tools -->
            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <a href="{{ route('broadcasts.create') }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="fa-solid fa-bullhorn"></i> Broadcast Alert
                </a>
                @endif

                <!-- Notification Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative" type="button" data-bs-toggle="dropdown" style="width: 42px; height: 42px;">
                        <i class="fa-regular fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2" style="width: 320px;">
                        <li><h6 class="dropdown-header fw-bold">Recent Institutional Alerts</h6></li>
                        <li>
                            <a class="dropdown-item rounded-3 p-2 small mb-1" href="{{ route('broadcasts.index') }}">
                                <div class="fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Severe Weather Alert</div>
                                <div class="text-muted" style="font-size: 11px;">Campus buses departing at 3:15 PM today.</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 p-2 small mb-1" href="{{ route('broadcasts.index') }}">
                                <div class="fw-bold text-primary"><i class="fa-solid fa-book-open me-1"></i> Midterm Syllabus Posted</div>
                                <div class="text-muted" style="font-size: 11px;">Grade 10 Mathematics review guide online.</div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small text-primary fw-semibold" href="{{ route('broadcasts.index') }}">View All in Comms Hub</a></li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn p-0 border-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <img src="{{ asset(auth()->user()->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                        <div class="text-start d-none d-md-block">
                            <span class="fw-bold small d-block">{{ auth()->user()->name }}</span>
                            <span class="text-muted text-capitalize" style="font-size: 11px;">{{ auth()->user()->role }}</span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li><h6 class="dropdown-header small">{{ auth()->user()->email }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high me-2 text-muted"></i> Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-power-off me-2"></i> Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('scholar/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('scholar/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
