<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Scholar SMS — Intelligent School Management & Digital LMS')</title>
    <meta name="description" content="Next-generation institutional school management system featuring contactless QR attendance, omni-channel broadcasts, cryptographic audit trails, and interactive gradebooks.">

    <!-- Favicons -->
    <link href="{{ asset('knightone/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('knightone/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('knightone/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('knightone/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('knightone/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('knightone/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('knightone/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- KnightOne Main CSS File -->
    <link href="{{ asset('knightone/css/main.css') }}" rel="stylesheet">

    <style>
        /* Custom KnightOne Enhancements for Scholar SMS */
        :root {
            --accent-color: #009961;
            --accent-hover: #007a4d;
        }
        .header .logo h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header .logo h1 span {
            color: #009961;
        }
        .cta-btn {
            background: #009961 !important;
            color: #ffffff !important;
            border-radius: 50px !important;
            padding: 8px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(0, 153, 97, 0.35) !important;
        }
        .cta-btn:hover {
            background: #007a4d !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 153, 97, 0.5) !important;
        }
        .footer {
            background-color: #0a0a0a;
            color: #aaaaaa;
            padding: 60px 0 30px;
            font-size: 14px;
        }
        .footer h4 {
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .footer a {
            color: #aaaaaa;
            transition: 0.3s;
        }
        .footer a:hover {
            color: #009961;
        }
    </style>
    @yield('styles')
</head>

<body class="index-page">

    <!-- Fixed Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0">
                <i class="fa-solid fa-graduation-cap fs-3" style="color: #009961;"></i>
                <h1 class="sitename">Scholar<span>SMS</span></h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('home') }}#hero" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ route('home') }}#about">About</a></li>
                    <li><a href="{{ route('home') }}#services">Academic Pillars</a></li>
                    <li><a href="{{ route('home') }}#features">Platform Capabilities</a></li>
                    <li><a href="{{ route('landing.gallery') }}" class="{{ request()->routeIs('landing.gallery') ? 'active' : '' }}">Campus Gallery</a></li>
                    <li><a href="{{ route('home') }}#events">Events Calendar</a></li>
                    <li><a href="{{ route('home') }}#stats">Institutional Reach</a></li>
                    <li><a href="{{ route('home') }}#pricing">Cohorts</a></li>
                    <li><a href="{{ route('home') }}#faq">FAQ</a></li>
                    <li><a href="{{ route('home') }}#contact">Admissions</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a class="cta-btn" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> My Portal
                    </a>
                @else
                    <a class="cta-btn" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Access Portals
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer id="footer" class="footer dark-background">
        <div class="container">
            <div class="row gy-4 mb-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="{{ route('home') }}" class="logo d-flex align-items-center mb-3 text-white text-decoration-none">
                        <i class="fa-solid fa-graduation-cap fs-3 me-2" style="color: #009961;"></i>
                        <span class="fs-4 fw-bold">Scholar<span style="color: #009961;">SMS</span></span>
                    </a>
                    <p class="small text-muted mb-3">
                        Scholar SMS is an enterprise-class School Management Platform & Digital Learning Ecosystem uniting faculty, students, and guardians through verified accountability, real-time communications, and academic excellence.
                    </p>
                    <div class="d-flex gap-3 social-links">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: grid; place-items: center;"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: grid; place-items: center;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: grid; place-items: center;"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: grid; place-items: center;"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6 footer-links">
                    <h4>Navigation</h4>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}#hero">Home</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#about">About Academy</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#services">Academic Pillars</a></li>
                        <li class="mb-2"><a href="{{ route('landing.gallery') }}">Campus Life</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#contact">Admissions Desk</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 col-6 footer-links">
                    <h4>Institutional Portals</h4>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'admin') }}">Executive Admin Console</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'teacher') }}">Faculty LMS Workspace</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'student') }}">Student 360 Portal</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'parent') }}">Guardian Oversight Desk</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Role Authentication</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 footer-contact">
                    <h4>Campus Secretariat</h4>
                    <p class="small text-muted mb-2"><i class="bi bi-geo-alt text-success me-2"></i> 100 University Boulevard, Academic District, Suite 500</p>
                    <p class="small text-muted mb-2"><i class="bi bi-telephone text-success me-2"></i> +1 (555) 234-5678 / +1 (555) 234-8900</p>
                    <p class="small text-muted mb-3"><i class="bi bi-envelope text-success me-2"></i> admissions@scholar-academy.edu</p>
                    <div class="p-3 rounded-3 bg-dark border border-secondary border-opacity-25 small">
                        <strong class="text-white d-block mb-1"><i class="fa-solid fa-shield-halved text-success me-1"></i> Security & Cryptographic Audit</strong>
                        <span class="text-white-50" style="font-size: 11px;">All institutional audit chains verified with SHA-256 tamper-proof ledger hashing.</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-top border-secondary border-opacity-25 text-center small text-muted">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>&copy; {{ date('Y') }} <strong>Scholar Academy SMS</strong>. Powered by KnightOne Architecture. All Rights Reserved.</div>
                    <div class="d-flex gap-3">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Enrollment</a>
                        <a href="#">Audit Compliance</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Top Button -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('knightone/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('knightone/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('knightone/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('knightone/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('knightone/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('knightone/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('knightone/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- KnightOne Main JS File -->
    <script src="{{ asset('knightone/js/main.js') }}"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#009961',
            customClass: { popup: 'rounded-4 shadow-lg border-0' }
        });
    </script>
    @endif

    @yield('scripts')
</body>
</html>
