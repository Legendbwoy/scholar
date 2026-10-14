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
        .header {
            background: rgba(10, 14, 23, 0.88) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 12px 0 !important;
            transition: all 0.3s ease !important;
        }
        .header .logo h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .header .logo h1 span {
            color: #009961;
        }

        /* Navmenu Items Spacing & Typography */
        @media (min-width: 1200px) {
            .navmenu {
                padding: 0;
            }
            .navmenu > ul > li {
                padding: 12px 9px !important;
                white-space: nowrap;
            }
            .navmenu a, .navmenu a:focus {
                font-size: 14px !important;
                font-weight: 500 !important;
                color: rgba(255, 255, 255, 0.82) !important;
                letter-spacing: 0.2px;
                padding: 0 2px !important;
            }
            .navmenu a:hover, .navmenu .active, .navmenu .active:focus {
                color: #ffffff !important;
            }
            .navmenu > ul > li > a:before {
                bottom: -18px !important;
                height: 3px !important;
                border-radius: 2px !important;
                background-color: #009961 !important;
            }
        }

        @media (min-width: 1200px) and (max-width: 1360px) {
            .navmenu > ul > li {
                padding: 12px 6px !important;
            }
            .navmenu a {
                font-size: 13px !important;
            }
        }

        /* Header CTA Button - Non-collapsing sleek pill */
        .cta-btn {
            background: #009961 !important;
            color: #ffffff !important;
            border-radius: 50px !important;
            padding: 9px 22px !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            min-height: 40px !important;
            box-shadow: 0 4px 14px rgba(0, 153, 97, 0.35) !important;
            transition: all 0.25s ease-in-out !important;
            text-decoration: none !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }
        .cta-btn:hover {
            background: #007a4d !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(0, 153, 97, 0.5) !important;
        }
        .cta-btn i {
            font-size: 15px !important;
            line-height: 0 !important;
        }

        /* Section Title Center Alignment Fix */
        .section-title h2:after {
            left: 0 !important;
            right: 0 !important;
            margin: 0 auto !important;
            height: 3px !important;
            border-radius: 2px !important;
            background: #009961 !important;
        }

        /* Footer Aesthetics & High Contrast Readability */
        .footer {
            background-color: #0a0e17 !important;
            color: #cbd5e1 !important;
            padding: 70px 0 30px !important;
            font-size: 14px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .footer h4 {
            color: #ffffff !important;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            margin-bottom: 22px !important;
        }
        .footer p,
        .footer .footer-about p,
        .footer .footer-contact p {
            color: #cbd5e1 !important;
            line-height: 1.65 !important;
        }
        .footer a {
            color: #94a3b8 !important;
            text-decoration: none !important;
            transition: all 0.25s ease !important;
        }
        .footer a:hover {
            color: #009961 !important;
        }
        .footer .social-links a {
            width: 38px !important;
            height: 38px !important;
            border-radius: 50% !important;
            background: #009961 !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.25s ease !important;
            box-shadow: 0 4px 10px rgba(0, 153, 97, 0.25) !important;
            border: none !important;
        }
        .footer .social-links a:hover {
            background: #007a4d !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 16px rgba(0, 153, 97, 0.45) !important;
        }
        .footer .social-links a i {
            font-size: 16px !important;
            line-height: 0 !important;
        }
        .footer-audit-box {
            background: rgba(0, 153, 97, 0.08) !important;
            border: 1px solid rgba(0, 153, 97, 0.3) !important;
            border-radius: 12px !important;
            padding: 16px !important;
        }

        /* Pricing & Cohort Card Hover Effects */
        .pricing-item {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease !important;
        }
        .pricing-item:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.09) !important;
        }
        .pricing-item.featured:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 22px 50px rgba(0, 153, 97, 0.22) !important;
        }

        /* Scroll Top Button Centering */
        .scroll-top {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            background-color: #009961 !important;
            box-shadow: 0 4px 15px rgba(0, 153, 97, 0.4) !important;
        }
        .scroll-top i {
            font-size: 24px !important;
            line-height: 1 !important;
            color: #ffffff !important;
        }
    </style>
    @yield('styles')
</head>

<body class="index-page">

    <!-- Fixed Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid px-xl-5 px-3 position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0 text-decoration-none">
                <i class="fa-solid fa-graduation-cap fs-3 me-2" style="color: #009961;"></i>
                <h1 class="sitename">Scholar<span>SMS</span></h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('home') }}#hero">Home</a></li>
                    <li><a href="{{ route('home') }}#about">About</a></li>
                    <li><a href="{{ route('home') }}#services">Academics</a></li>
                    <li><a href="{{ route('home') }}#features">Capabilities</a></li>
                    <li><a href="{{ route('landing.gallery') }}" class="{{ request()->routeIs('landing.gallery') ? 'active' : '' }}">Gallery</a></li>
                    <li><a href="{{ route('home') }}#events">Events</a></li>
                    <li><a href="{{ route('home') }}#stats">Reach</a></li>
                    <li><a href="{{ route('home') }}#pricing">Cohorts</a></li>
                    <li><a href="{{ route('home') }}#faq">FAQ</a></li>
                    <li><a href="{{ route('home') }}#contact">Admissions</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <div class="d-flex align-items-center flex-shrink-0 ms-xl-3">
                @auth
                    <a class="cta-btn" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>My Portal</span>
                    </a>
                @else
                    <a class="cta-btn" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Access Portals</span>
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
                    <p class="small mb-3" style="color: #cbd5e1;">
                        Scholar SMS is an enterprise-class School Management Platform & Digital Learning Ecosystem uniting faculty, students, and guardians through verified accountability, real-time communications, and academic excellence.
                    </p>
                    <div class="d-flex gap-2 social-links">
                        <a href="https://twitter.com" target="_blank" rel="noopener" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://facebook.com" target="_blank" rel="noopener" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6 footer-links">
                    <h4>Navigation</h4>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}#hero"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Home</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#about"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> About Academy</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#services"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Academic Pillars</a></li>
                        <li class="mb-2"><a href="{{ route('landing.gallery') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Campus Life</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#contact"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Admissions Desk</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 col-6 footer-links">
                    <h4>Institutional Portals</h4>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'admin') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Admin Console</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'teacher') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Faculty Workspace</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'student') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Student 360</a></li>
                        <li class="mb-2"><a href="{{ route('auth.demo.switch', 'parent') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Guardian Desk</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}"><i class="fa-solid fa-chevron-right me-1 small" style="font-size: 10px; color: #009961;"></i> Role Authentication</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 footer-contact">
                    <h4>Campus Secretariat</h4>
                    <p class="small mb-2" style="color: #cbd5e1;"><i class="bi bi-geo-alt text-success me-2 fs-6"></i> {{ setting('school_address', '100 University Boulevard, Academic District, Suite 500') }}</p>
                    <p class="small mb-2" style="color: #cbd5e1;"><i class="bi bi-telephone text-success me-2 fs-6"></i> {{ setting('school_phone', '+1 (555) 234-5678 / +1 (555) 234-8900') }}</p>
                    <p class="small mb-3" style="color: #cbd5e1;"><i class="bi bi-envelope text-success me-2 fs-6"></i> {{ setting('school_email', 'admissions@scholar-academy.edu') }}</p>
                    <div class="footer-audit-box">
                        <strong class="text-white d-block mb-1 fs-6"><i class="fa-solid fa-shield-halved text-success me-2"></i> Security & Cryptographic Audit</strong>
                        <span style="color: #cbd5e1; font-size: 12px; line-height: 1.5;" class="d-block">All institutional audit chains verified with SHA-256 tamper-proof ledger hashing.</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-top border-secondary border-opacity-25 text-center small" style="color: #94a3b8;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>&copy; {{ date('Y') }} <strong class="text-white">{{ setting('school_name', 'Scholar Academy SMS') }}</strong>. Powered by KnightOne Architecture. All Rights Reserved.</div>
                    <div class="d-flex gap-3">
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-decoration-none" style="color: #94a3b8;">Privacy Policy</a>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-decoration-none" style="color: #94a3b8;">Terms of Enrollment</a>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#auditComplianceModal" class="text-decoration-none" style="color: #94a3b8;">Audit Compliance</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Institutional Policy Modals -->
    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3">
                    <h5 class="modal-title fw-bold text-dark" id="privacyModalLabel">
                        <i class="fa-solid fa-user-shield text-success me-2"></i> Institutional Privacy Policy & FERPA Compliance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3 small text-muted" style="line-height: 1.7; max-height: 60vh; overflow-y: auto;">
                    <h6 class="fw-bold text-dark mb-1">1. Information We Collect & Academic Records</h6>
                    <p>Scholar SMS processes official student registration records, daily roll-call attendance logs, examination performance metrics, and verified guardian emergency contacts strictly for educational stewardship and safety administration.</p>
                    
                    <h6 class="fw-bold text-dark mb-1">2. Data Security & Cryptographic Ledgering</h6>
                    <p>All sensitive academic records, continuous assessment score entries, and administrative mutations are immutably logged with SHA-256 cryptographic hashes. Passwords utilize Bcrypt encryption, and communications are served exclusively over Transport Layer Security (TLS).</p>
                    
                    <h6 class="fw-bold text-dark mb-1">3. Parental Rights & Access Transparency</h6>
                    <p>Guardians have 24/7 self-service visibility into their ward's official academic trajectories, behavioral commendations, attendance standings, and terminal report cards through the Guardian Desk portal.</p>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-dark rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms of Enrollment Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3">
                    <h5 class="modal-title fw-bold text-dark" id="termsModalLabel">
                        <i class="fa-solid fa-file-contract text-primary me-2"></i> Terms of Enrollment & Academic Honor Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3 small text-muted" style="line-height: 1.7; max-height: 60vh; overflow-y: auto;">
                    <h6 class="fw-bold text-dark mb-1">1. Academic Integrity & CBT Examination Conduct</h6>
                    <p>Candidates partaking in digital assessments, midterms, or practice quizzes through the LMS testing terminal agree to uphold the institutional honor code. Multi-window navigation tracking and countdown zero-expiry locks are enforced to preserve fairness.</p>
                    
                    <h6 class="fw-bold text-dark mb-1">2. Daily Attendance Punctuality</h6>
                    <p>Regular roll-call presence is mandatory. Absences and tardiness are automatically logged into the student 360 profile and summarized on end-of-term certification transcripts.</p>
                    
                    <h6 class="fw-bold text-dark mb-1">3. Code of Conduct & Respectful Communications</h6>
                    <p>Platform portals and noticeboards must be utilized solely for curriculum collaboration, institutional notices, and respectful guardian-faculty inquiries.</p>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-dark rounded-pill px-4" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Compliance Modal -->
    <div class="modal fade" id="auditComplianceModal" tabindex="-1" aria-labelledby="complianceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3">
                    <h5 class="modal-title fw-bold text-dark" id="complianceModalLabel">
                        <i class="fa-solid fa-shield-halved text-success me-2"></i> Cryptographic Audit Ledger & Compliance Framework
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3 small text-muted" style="line-height: 1.7; max-height: 60vh; overflow-y: auto;">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
                            <i class="fa-solid fa-link text-success"></i> SHA-256 Chained Hash Verification
                        </div>
                        <div>Each administrative action generates a deterministic cryptographic hash computed from previous record state, user signature, and UTC timestamp, preventing retro-active alteration of grades or records.</div>
                    </div>

                    <h6 class="fw-bold text-dark mb-1">Regulatory Standards Met:</h6>
                    <ul class="bullet-list mb-3">
                        <li><strong>FERPA / GDPR:</strong> Granular role-segregated authorization protecting minor student PII.</li>
                        <li><strong>Academic Auditability:</strong> Full traceability of score overrides, exam submissions, and staff actions.</li>
                        <li><strong>System Uptime & Failover:</strong> Robust relational transactions with automated recovery fail-safes.</li>
                    </ul>
                </div>
                <div class="modal-footer border-top px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('audit.verify') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Launch Audit Ledger Verification
                    </a>
                </div>
            </div>
        </div>
    </div>

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
