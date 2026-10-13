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
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @keyframes scanLaser {
        0% { top: 0%; opacity: 0.8; }
        50% { top: 95%; opacity: 1; }
        100% { top: 0%; opacity: 0.8; }
    }

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

    <style>
        .palette-item { transition: all 0.15s ease; cursor: pointer; }
        .palette-item:hover, .active-palette-item { background-color: #f1f0fa !important; transform: translateX(3px); }
    </style>

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
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 text-muted d-none d-lg-inline-flex align-items-center gap-2 shadow-sm" onclick="openCommandPalette()" title="Press Ctrl + K anytime">
                    <i class="fa-solid fa-magnifying-glass text-primary"></i>
                    <span class="small">Search portal...</span>
                    <kbd class="bg-white border text-dark px-1.5 py-0.5 rounded shadow-sm fw-semibold" style="font-size: 10px;">Ctrl + K</kbd>
                </button>
                @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-none d-md-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#globalQuickQrModal">
                    <i class="fa-solid fa-qrcode"></i> Quick QR Check-In
                </button>
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

    <!-- Global Quick QR Terminal Modal (Admin & Teacher) -->
    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher()))
    <div class="modal fade" id="globalQuickQrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-primary text-white shadow-sm" style="width: 44px; height: 44px; display: grid; place-items: center; background-color: #7a6ad8 !important;">
                            <i class="fa-solid fa-qrcode fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Smart QR Check-In Terminal</h5>
                            <small class="text-white-50">Instant Contactless Punctuality Verification</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Interactive Optical Viewfinder Mockup with Laser -->
                    <div class="position-relative bg-dark rounded-4 p-4 text-center text-white mb-4 overflow-hidden shadow-inner" style="height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center; border: 2px solid rgba(122, 106, 216, 0.5);">
                        <div class="position-absolute" style="top: 0; left: 0; right: 0; height: 3px; background: #00ffcc; box-shadow: 0 0 14px #00ffcc; animation: scanLaser 2s infinite ease-in-out;"></div>
                        <i class="fa-solid fa-qrcode fs-1 text-white-50 mb-2"></i>
                        <small class="text-white-50">Optical Gate Sensor Active • Ready for Token</small>
                        <span class="badge bg-success mt-2 px-3 py-1 rounded-pill"><i class="fa-solid fa-wifi me-1"></i> Checkpoint Gate Online</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Select Student Token to Verify</label>
                        <select id="quickQrStudentSelect" class="form-select border-primary-subtle">
                            <option value="">-- Choose Student Badge by Name / Admission # --</option>
                            @php
                                $qrStudents = \App\Models\Student::with('user', 'schoolClass')->take(20)->get();
                            @endphp
                            @foreach($qrStudents as $qs)
                                <option value="{{ $qs->id }}">{{ $qs->user->name }} ({{ $qs->admission_number }} - {{ $qs->schoolClass->name ?? 'Class' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="quickQrStatusAlert" class="alert alert-white border rounded-3 p-3 small mb-0 text-center text-muted shadow-sm">
                        <i class="fa-solid fa-barcode me-1 text-primary"></i> Select a student badge above and verify arrival.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: #7a6ad8; border-color: #7a6ad8;" onclick="executeQuickQrVerification()">
                        <i class="fa-solid fa-bolt me-1"></i> Verify Check-In
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Global Command Palette Modal (Ctrl + K) -->
    <div class="modal fade" id="commandPaletteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(12px);">
                <div class="p-3 border-bottom d-flex align-items-center gap-3 bg-white">
                    <i class="fa-solid fa-magnifying-glass fs-5 text-primary"></i>
                    <input type="text" id="commandPaletteInput" class="form-control form-control-lg border-0 shadow-none ps-0 fs-6" placeholder="Type to search students, courses, faculty, or actions..." autocomplete="off">
                    <span class="badge bg-light text-muted border px-2 py-1 small">ESC to close</span>
                </div>
                <div class="modal-body p-3" style="max-height: 440px; overflow-y: auto;" id="commandPaletteBody">
                    <!-- Default Actions View -->
                    <div id="commandPaletteDefaults">
                        <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Recommended Quick Actions</div>
                        <div class="list-group list-group-flush rounded-3">
                          @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                          <a href="{{ route('gradebook.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="p-2 rounded-2 bg-primary-subtle text-primary"><i class="fa-solid fa-file-signature"></i></div>
                                  <div>
                                      <div class="fw-semibold text-dark">Academic Gradebook & Matrix</div>
                                      <small class="text-muted">Evaluate students, enter test marks & compute weighted GPAs</small>
                                  </div>
                              </div>
                              <span class="badge bg-primary-subtle text-primary">Grades</span>
                          </a>
                          <a href="{{ route('gradebook.reports') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="p-2 rounded-2 bg-warning-subtle text-warning"><i class="fa-solid fa-award"></i></div>
                                  <div>
                                      <div class="fw-semibold text-dark">Class Report Cards Registry</div>
                                      <small class="text-muted">Print authenticated term transcripts & class standings</small>
                                  </div>
                              </div>
                              <span class="badge bg-warning-subtle text-dark">Report Cards</span>
                          </a>
                          @endif
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('students.create') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-primary-subtle text-primary"><i class="fa-solid fa-user-plus"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Enroll New Student</div>
                                        <small class="text-muted">Create a single academic dossier record</small>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border">Action</span>
                            </a>
                            <a href="{{ route('students.bulk') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-success-subtle text-success"><i class="fa-solid fa-file-excel"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Bulk Excel Student Import</div>
                                        <small class="text-muted">Download template & batch onboard cohorts</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success">Excel</span>
                            </a>
                            <a href="{{ route('audit.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-danger-subtle text-danger"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Cryptographic Audit Ledger</div>
                                        <small class="text-muted">Verify SHA-256 integrity chains</small>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger">Ledger</span>
                            </a>
                            @endif
                            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                            <a href="{{ route('broadcasts.create') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-warning-subtle text-warning"><i class="fa-solid fa-bullhorn"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Compose Institutional Broadcast</div>
                                        <small class="text-muted">Multi-channel SMS, WhatsApp & Email dispatch</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning-subtle text-dark">Broadcast</span>
                            </a>
                            <a href="javascript:void(0)" onclick="bootstrap.Modal.getInstance(document.getElementById('commandPaletteModal')).hide(); new bootstrap.Modal(document.getElementById('globalQuickQrModal')).show();" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-info-subtle text-info"><i class="fa-solid fa-qrcode"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Open QR Check-In Terminal</div>
                                        <small class="text-muted">Verify student attendance contactless badge</small>
                                    </div>
                                </div>
                                <span class="badge bg-info-subtle text-info">Terminal</span>
                            </a>
                            @endif
                            <a href="{{ route('courses.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-2 bg-secondary-subtle text-secondary"><i class="fa-solid fa-book-open"></i></div>
                                    <div>
                                        <div class="fw-semibold text-dark">Browse LMS Syllabi & Materials</div>
                                        <small class="text-muted">Access lecture slide decks and assignments</small>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border">Directory</span>
                            </a>
                        </div>
                    </div>
                    <!-- Live Dynamic Results -->
                    <div id="commandPaletteResults" style="display: none;"></div>
                </div>
                <div class="p-2 px-3 border-top bg-light d-flex justify-content-between align-items-center small text-muted">
                    <span>Navigation: <kbd class="bg-white border text-dark px-1.5 py-0.5 rounded shadow-sm">↑</kbd> <kbd class="bg-white border text-dark px-1.5 py-0.5 rounded shadow-sm">↓</kbd> navigate &bull; <kbd class="bg-white border text-dark px-1.5 py-0.5 rounded shadow-sm">ENTER</kbd> open</span>
                    <span class="text-muted fw-semibold">Scholar Command Center</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('scholar/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('scholar/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Unified SweetAlert2 Toast Stack
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        // 2. Global SweetAlert2 Confirmation Dialogs (Intercepting destructive actions)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const hasConfirmClass = form.classList.contains('confirm-action');
            const hasConfirmAttr = form.getAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm(');

            if (hasConfirmClass || hasConfirmAttr) {
                e.preventDefault();
                
                const oldOnsubmit = form.getAttribute('onsubmit');
                form.removeAttribute('onsubmit');

                const title = form.getAttribute('data-confirm-title') || 'Are you sure?';
                const text = form.getAttribute('data-confirm-text') || 'This record will be permanently updated or archived in the institutional registry.';
                const btnText = form.getAttribute('data-confirm-btn') || 'Yes, proceed';
                const btnColor = form.getAttribute('data-confirm-color') || '#e53e3e';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: btnColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: btnText,
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0',
                        confirmButton: 'rounded-pill px-4 fw-bold',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Updating cryptographic ledger...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        form.submit();
                    } else if (oldOnsubmit) {
                        form.setAttribute('onsubmit', oldOnsubmit);
                    }
                });
            }
        });

        // 3. Quick QR Verification Function
        function executeQuickQrVerification() {
            const select = document.getElementById('quickQrStudentSelect');
            const studentId = select ? select.value : null;
            const alertBox = document.getElementById('quickQrStatusAlert');

            if (!studentId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Student Selected',
                    text: 'Please choose a student badge from the list to verify checkpoint arrival.',
                    confirmButtonColor: '#7a6ad8'
                });
                return;
            }

            alertBox.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-primary"></span> Verifying contactless token...';

            fetch("{{ route('attendance.qr.verify') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ student_id: studentId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertBox.className = 'alert alert-success border rounded-3 p-3 small mb-0 text-center';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i> <strong>' + data.message + '</strong> verified at ' + data.time;

                    Swal.fire({
                        icon: 'success',
                        title: 'Check-In Verified!',
                        html: '<div class="py-2"><strong>' + data.message + '</strong><br><small class="text-muted">Recorded at ' + data.time + ' | Status: <span class="badge bg-success">' + data.status + '</span></small></div>',
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4 shadow-lg border-0' }
                    });
                } else {
                    alertBox.className = 'alert alert-danger border rounded-3 p-3 small mb-0 text-center';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-exclamation text-danger me-1"></i> Verification failed.';
                }
            })
            .catch(err => {
                alertBox.className = 'alert alert-danger border rounded-3 p-3 small mb-0 text-center';
                alertBox.innerHTML = 'Server error during QR check-in verification.';
            });
        }
    
        // 4. Global Command Palette (Ctrl + K) Engine
        let paletteModalInstance = null;
        function openCommandPalette() {
            const modalEl = document.getElementById('commandPaletteModal');
            if (!paletteModalInstance) {
                paletteModalInstance = new bootstrap.Modal(modalEl);
            }
            paletteModalInstance.show();
            setTimeout(() => {
                const input = document.getElementById('commandPaletteInput');
                if (input) {
                    input.value = '';
                    input.focus();
                    document.getElementById('commandPaletteDefaults').style.display = 'block';
                    document.getElementById('commandPaletteResults').style.display = 'none';
                    document.getElementById('commandPaletteResults').innerHTML = '';
                }
            }, 200);
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                openCommandPalette();
            }
        });

        let searchDebounceTimer = null;
        const paletteInput = document.getElementById('commandPaletteInput');
        if (paletteInput) {
            paletteInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                const defaults = document.getElementById('commandPaletteDefaults');
                const results = document.getElementById('commandPaletteResults');

                clearTimeout(searchDebounceTimer);

                if (query.length === 0) {
                    defaults.style.display = 'block';
                    results.style.display = 'none';
                    results.innerHTML = '';
                    return;
                }

                defaults.style.display = 'none';
                results.style.display = 'block';
                results.innerHTML = '<div class="text-center py-4 text-muted small"><span class="spinner-border spinner-border-sm me-2 text-primary"></span> Searching directory...</div>';

                searchDebounceTimer = setTimeout(() => {
                    fetch(`{{ route('api.search') }}?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.results || data.results.length === 0) {
                            results.innerHTML = `<div class="text-center py-4 text-muted small"><i class="fa-solid fa-magnifying-glass me-1"></i> No matching records for "${query}".</div>`;
                            return;
                        }

                        let html = `<div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Search Results (${data.results.length})</div><div class="list-group list-group-flush rounded-3">`;

                        data.results.forEach((item, index) => {
                            html += `
                                <a href="${item.url}" class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center justify-content-between p-2 mb-1 palette-item ${index === 0 ? 'active-palette-item' : ''}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-2 bg-light border text-primary" style="width: 38px; height: 38px; display: grid; place-items: center;">
                                            <i class="${item.icon}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">${item.title}</div>
                                            <small class="text-muted">${item.subtitle}</small>
                                        </div>
                                    </div>
                                    <span class="badge ${item.badge_class || 'bg-light text-dark border'}">${item.badge || item.category}</span>
                                </a>
                            `;
                        });
                        html += '</div>';
                        results.innerHTML = html;
                    })
                    .catch(() => {
                        results.innerHTML = '<div class="text-center py-3 text-danger small">Error searching directory.</div>';
                    });
                }, 250);
            });

            // Keyboard navigation (Arrow keys + Enter)
            paletteInput.addEventListener('keydown', function(e) {
                const resultsContainer = document.getElementById('commandPaletteResults');
                const isSearching = resultsContainer && resultsContainer.style.display !== 'none';
                const items = (isSearching ? resultsContainer : document.getElementById('commandPaletteDefaults')).querySelectorAll('.palette-item');
                
                if (items.length === 0) return;

                let activeIndex = -1;
                items.forEach((item, idx) => {
                    if (item.classList.contains('active-palette-item')) activeIndex = idx;
                });

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (activeIndex >= 0) items[activeIndex].classList.remove('active-palette-item');
                    activeIndex = (activeIndex + 1) % items.length;
                    items[activeIndex].classList.add('active-palette-item');
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeIndex >= 0) items[activeIndex].classList.remove('active-palette-item');
                    activeIndex = (activeIndex - 1 + items.length) % items.length;
                    items[activeIndex].classList.add('active-palette-item');
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeIndex >= 0 && items[activeIndex]) {
                        items[activeIndex].click();
                    } else if (items.length > 0) {
                        items[0].click();
                    }
                }
            });
        }

    </script>

    @yield('scripts')
</body>
</html>
