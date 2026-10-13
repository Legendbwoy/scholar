<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Scholar — Next-Gen School Management System & Digital LMS')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('scholar/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Scholar Template CSS -->
    <link rel="stylesheet" href="{{ asset('scholar/assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('scholar/assets/css/templatemo-scholar.css') }}">
    <link rel="stylesheet" href="{{ asset('scholar/assets/css/owl.css') }}">
    <link rel="stylesheet" href="{{ asset('scholar/assets/css/animate.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --scholar-primary: #7a6ad8;
            --scholar-secondary: #f35525;
            --scholar-dark: #1e1e1e;
            --scholar-light: #f7f9fc;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            color: #4a4a4a;
        }
        .header-area {
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            background-color: #fff;
        }
        .header-area .main-nav .logo h1 {
            color: var(--scholar-primary);
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-area .main-nav .logo h1 span {
            color: var(--scholar-secondary);
        }
        .nav-portal-btn {
            background: linear-gradient(135deg, var(--scholar-primary), #5b48cb) !important;
            color: #fff !important;
            border-radius: 25px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(122, 106, 216, 0.35);
            transition: all 0.3s ease;
        }
        .nav-portal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 106, 216, 0.5);
            color: #fff !important;
        }
        .section-heading h2 em {
            color: var(--scholar-primary);
            font-style: normal;
        }
        .section-heading h6 {
            color: var(--scholar-secondary);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .badge-cat {
            background: rgba(122, 106, 216, 0.12);
            color: var(--scholar-primary);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .course-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(122, 106, 216, 0.18);
        }
        .course-card .thumb {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        .course-card .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .course-card:hover .thumb img {
            transform: scale(1.06);
        }
        .course-card .price {
            position: absolute;
            right: 18px;
            top: 18px;
            background: var(--scholar-secondary);
            color: #fff;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(243, 85, 37, 0.4);
        }
        .gallery-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            cursor: pointer;
        }
        .gallery-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-card:hover img {
            transform: scale(1.08);
        }
        .gallery-card .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(30,30,30,0.85) 0%, rgba(30,30,30,0.1) 70%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
            color: #fff;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .gallery-card:hover .overlay {
            opacity: 1;
        }
        .stat-box {
            background: #fff;
            border-radius: 18px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            border-bottom: 4px solid var(--scholar-primary);
            transition: transform 0.3s ease;
        }
        .stat-box:hover {
            transform: translateY(-5px);
        }
        .stat-box h2 {
            font-size: 42px;
            font-weight: 800;
            color: var(--scholar-primary);
            margin-bottom: 8px;
        }
        .stat-box p {
            font-size: 15px;
            color: #777;
            font-weight: 500;
            margin: 0;
        }
        footer {
            background: #191825;
            color: #a0a0b0;
            padding: 60px 0 30px;
            margin-top: 80px;
        }
        footer h5 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 24px;
        }
        footer a {
            color: #b8b8cc;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        footer a:hover {
            color: var(--scholar-primary);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 40px;
            padding-top: 25px;
            text-align: center;
            font-size: 14px;
        }
        .filter-btn {
            background: #f0f2f8;
            border: none;
            padding: 8px 22px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            color: #555;
            margin: 4px;
            transition: all 0.25s ease;
        }
        .filter-btn.active, .filter-btn:hover {
            background: var(--scholar-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(122, 106, 216, 0.3);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Area -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- Logo -->
                        <a href="{{ route('home') }}" class="logo">
                            <h1><i class="fa-solid fa-graduation-cap"></i> Scholar<span>SMS</span></h1>
                        </a>

                        <!-- Navigation Menu -->
                        <ul class="nav">
                            <li><a href="{{ route('home') }}#top" class="active">Home</a></li>
                            <li><a href="{{ route('home') }}#services">Capabilities</a></li>
                            <li><a href="{{ route('home') }}#courses">Courses & LMS</a></li>
                            <li><a href="{{ route('home') }}#gallery">Campus Life</a></li>
                            <li><a href="{{ route('home') }}#team">Faculty</a></li>
                            <li><a href="{{ route('home') }}#events">Events</a></li>
                            <li><a href="{{ route('home') }}#contact">Admissions</a></li>
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="nav-portal-btn"><i class="fa-solid fa-gauge-high me-1"></i> My Portal ({{ ucfirst(auth()->user()->role) }})</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="nav-portal-btn"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Portal Login</a></li>
                            @endauth
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="container mt-4 pt-5">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container mt-4 pt-5">
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Content Slot -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5><i class="fa-solid fa-graduation-cap text-warning me-2"></i>Scholar SMS</h5>
                    <p class="mb-3">Enterprise Institutional School Management System engineered with Laravel modular monolith architecture, real-time message broadcasting, tamper-evident audit trails, and student-first learning tools.</p>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success"><i class="fa-solid fa-shield-halved me-1"></i> SHA-256 Ledger Active</span>
                        <span class="badge bg-primary"><i class="fa-solid fa-signal me-1"></i> Multi-Channel Hub Live</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Institutional Portals</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('login') }}"><i class="fa-solid fa-angle-right me-1"></i> Admin Portal</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}"><i class="fa-solid fa-angle-right me-1"></i> Faculty Desk</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}"><i class="fa-solid fa-angle-right me-1"></i> Student Portal</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}"><i class="fa-solid fa-angle-right me-1"></i> Parent Access</a></li>
                        <li class="mb-2"><a href="{{ route('landing.gallery') }}"><i class="fa-solid fa-angle-right me-1"></i> Photo Gallery</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Academic Departments</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#courses"><i class="fa-solid fa-atom me-1"></i> Science & Robotics</a></li>
                        <li class="mb-2"><a href="#courses"><i class="fa-solid fa-square-root-variable me-1"></i> Advanced Mathematics</a></li>
                        <li class="mb-2"><a href="#courses"><i class="fa-solid fa-book me-1"></i> World Literature</a></li>
                        <li class="mb-2"><a href="#courses"><i class="fa-solid fa-laptop-code me-1"></i> Computer Science</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Campus & Contact</h5>
                    <p class="mb-1"><i class="fa-solid fa-location-dot me-2 text-warning"></i> 100 Scholar Way, Innovation Campus</p>
                    <p class="mb-1"><i class="fa-solid fa-phone me-2 text-warning"></i> +1 (555) 724-6527</p>
                    <p class="mb-3"><i class="fa-solid fa-envelope me-2 text-warning"></i> admissions@scholarsms.edu</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">© 2026 Scholar School Management System. All Rights Reserved. Built with Laravel 11 & Bootstrap 5.3 UI.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('scholar/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('scholar/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('scholar/assets/js/owl-carousel.js') }}"></script>
    <script src="{{ asset('scholar/assets/js/counter.js') }}"></script>
    <script src="{{ asset('scholar/assets/js/custom.js') }}"></script>
    @yield('scripts')
</body>
</html>