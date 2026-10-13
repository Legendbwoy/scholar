@extends('layouts.public')

@section('title', 'Scholar SMS — Comprehensive Modern School Management & Learning Platform')

@section('content')

<!-- Hero Section -->
<section id="hero" class="hero section dark-background">
    <img src="{{ asset('knightone/img/hero-bg.jpg') }}" alt="Scholar SMS Campus Hero" data-aos="fade-in">

    <div class="container d-flex flex-column align-items-center text-center">
        <div class="badge px-3 py-1.5 mb-3 fw-bold text-uppercase rounded-pill" style="background-color: rgba(0, 153, 97, 0.25); border: 1px solid #009961; color: #4ade80; font-size: 12px; letter-spacing: 1px;" data-aos="fade-down">
            <i class="fa-solid fa-graduation-cap me-1"></i> Premier Academy Management & Digital LMS
        </div>
        <h2 data-aos="fade-up" data-aos-delay="100" class="fw-bolder" style="font-family: 'Raleway', sans-serif; font-size: 46px; max-width: 850px; line-height: 1.2;">
            Empowering Education with Intelligent Management & Digital LMS
        </h2>
        <p data-aos="fade-up" data-aos-delay="200" class="text-white-50 mt-3 mb-4" style="font-size: 18px; max-width: 720px;">
            An enterprise-grade School Management System uniting students, faculty, and guardians through seamless multi-channel communications, cryptographic audit integrity, and interactive learning.
        </p>
        <div data-aos="fade-up" data-aos-delay="300" class="d-flex align-items-center gap-3 flex-wrap justify-content-center">
            <a href="#contact" class="btn btn-lg rounded-pill px-4 fw-bold shadow-lg" style="background-color: #009961; color: #fff; border: none; font-size: 15px;">
                <i class="bi bi-file-earmark-person me-1"></i> Apply for Admission
            </a>
            <a href="{{ route('login') }}" class="btn btn-lg btn-outline-light rounded-pill px-4 fw-bold" style="font-size: 15px;">
                <i class="bi bi-box-arrow-in-right me-1"></i> Access Role Portals
            </a>
            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn ms-2" title="Watch Virtual Campus Tour"></a>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about section">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="section-title text-start mb-3">
                    <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Institutional Excellence</span>
                    <h2 class="fw-bold mt-1 text-dark">Nurturing Tomorrow's Visionaries with Pedagogical & Technological Precision</h2>
                </div>
                <p class="text-muted">
                    Founded on the principles of scholarly rigor and ethical leadership, Scholar Academy provides an inclusive, world-class educational environment. Our integrated digital management ecosystem eliminates administrative friction so educators can focus on what matters most: inspiring students.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <i class="bi bi-shield-check fs-3 text-success d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Cryptographic Ledger</h6>
                            <small class="text-muted">Immutable SHA-256 audit logs guarantee verified academic authenticity.</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <i class="bi bi-broadcast fs-3 text-success d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Omni-Channel Comms</h6>
                            <small class="text-muted">Real-time SMS, Email, and WhatsApp dispatch to guardians & teachers.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="position-relative overflow-hidden rounded-4 shadow-lg border">
                    <img src="{{ asset('knightone/img/services.jpg') }}" class="img-fluid w-100" style="height: 400px; object-fit: cover;" alt="About Scholar Academy">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                        <h5 class="fw-bold mb-1">Dr. Evelyn Vance, Ph.D.</h5>
                        <small class="text-white-50">Head of School & Dean of Academic Affairs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Pillars / Services Section -->
<section id="services" class="services section light-background">
    <div class="container section-title text-center" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Integrated Architecture</span>
        <h2 class="fw-bold text-dark">Core Institutional Modules</h2>
        <p class="text-muted">Seamlessly engineered capabilities empowering administration, teaching faculty, learners, and families.</p>
    </div>

    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item item-cyan position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Academic Gradebook & Matrix</h5>
                    <p class="text-muted small mb-0">Continuous assessments, automated weighted term grading (Quizzes 20%, Midterms 30%, Finals 50%), and official printable PDF transcripts with authenticity QR codes.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-item item-orange position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-qr-code-scan"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Smart QR Check-In Terminal</h5>
                    <p class="text-muted small mb-0">Contactless punctuality tracking via optical camera badges, live attendance rate gauges, and automated parent late-arrival SMS notifications.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-item item-teal position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-book-half"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Digital LMS & Learning Assets</h5>
                    <p class="text-muted small mb-0">Publish lecture slides, lab worksheets, and homework assignments with single-click downloads and student engagement analytics.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="service-item item-red position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-shield-lock"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Cryptographic Audit Ledger</h5>
                    <p class="text-muted small mb-0">Every grade change, attendance mark, and broadcast trigger is cryptographically hashed with SHA-256 for immutable legal compliance.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="service-item item-indigo position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-megaphone"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Omni-Channel Broadcasts</h5>
                    <p class="text-muted small mb-0">Targeted broadcast alerts dispatched across SMS, WhatsApp, and email with live character counters, dynamic merging, and delivery audit logs.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="service-item item-pink position-relative p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="icon mb-3" style="color: #009961; font-size: 32px;"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Bulk Excel Onboarding</h5>
                    <p class="text-muted small mb-0">One-click standard template downloads and bulk student import with automatic parent account provisioning and password generation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section id="call-to-action" class="call-to-action section dark-background">
    <img src="{{ asset('knightone/img/cta-bg.jpg') }}" alt="Call To Action">
    <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
            <div class="col-xl-9 text-center">
                <h3 class="fw-bold text-white mb-3" style="font-size: 34px;">Empowering Educational Leadership Through Data-Driven Precision</h3>
                <p class="text-white-50 mb-4" style="font-size: 16px;">
                    Whether you are an administrator orchestrating campus operations, a teacher grading assessments, or a parent monitoring your child's scholastic trajectory, Scholar SMS delivers frictionless access.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a class="cta-btn py-3 px-5 fs-6" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Access Dedicated Portal
                    </a>
                    <a class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold" href="#contact">
                        <i class="bi bi-envelope me-1"></i> Request Institution Tour
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Institutional Stats Section -->
<section id="stats" class="stats section dark-background">
    <img src="{{ asset('knightone/img/stats-bg.jpg') }}" alt="Institutional Stats">
    <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 justify-content-center text-center">
            <div class="col-lg-3 col-md-6">
                <div class="stats-item p-3">
                    <span data-purecounter-start="0" data-purecounter-end="{{ $stats['students'] }}" data-purecounter-duration="1.5" class="purecounter fw-bold display-4 text-white"></span>
                    <p class="text-success fw-bold text-uppercase mt-2 small">Enrolled Scholars</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-item p-3">
                    <span data-purecounter-start="0" data-purecounter-end="{{ $stats['faculty'] }}" data-purecounter-duration="1.5" class="purecounter fw-bold display-4 text-white"></span>
                    <p class="text-success fw-bold text-uppercase mt-2 small">Accredited Faculty</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-item p-3">
                    <span data-purecounter-start="0" data-purecounter-end="{{ $stats['courses'] }}" data-purecounter-duration="1.5" class="purecounter fw-bold display-4 text-white"></span>
                    <p class="text-success fw-bold text-uppercase mt-2 small">LMS Syllabi & Courses</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-item p-3">
                    <span class="fw-bold display-4 text-white">{{ $stats['attendance_rate'] }}</span>
                    <p class="text-success fw-bold text-uppercase mt-2 small">Verified Attendance Rate</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Campus Life / Portfolio Gallery Section -->
<section id="portfolio" class="portfolio section">
    <div class="container section-title text-center" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Vibrant Campus Life</span>
        <h2 class="fw-bold text-dark">Scholar Academy Photographic Gallery</h2>
        <p class="text-muted">A glimpse into our state-of-the-art laboratories, athletic fields, creative studios, and student community.</p>
    </div>

    <div class="container">
        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
            <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
                <li data-filter="*" class="filter-active">All Highlights</li>
                <li data-filter=".filter-campus">Campus Architecture</li>
                <li data-filter=".filter-labs">Laboratories & STEM</li>
                <li data-filter=".filter-sports">Athletics</li>
            </ul>

            <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-campus">
                    <div class="portfolio-content h-100 rounded-4 overflow-hidden border shadow-sm position-relative">
                        <img src="{{ asset('knightone/img/portfolio/portfolio-1.jpg') }}" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="Main Academic Quadrangle">
                        <div class="portfolio-info p-3 bg-white">
                            <h6 class="fw-bold mb-1">Main Academic Quadrangle</h6>
                            <p class="text-muted small mb-0">Historic architecture meeting cutting-edge digital learning spaces.</p>
                            <a href="{{ asset('knightone/img/portfolio/portfolio-1.jpg') }}" title="Main Academic Quadrangle" data-gallery="portfolio-gallery" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-labs">
                    <div class="portfolio-content h-100 rounded-4 overflow-hidden border shadow-sm position-relative">
                        <img src="{{ asset('knightone/img/portfolio/portfolio-2.jpg') }}" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="Robotics & Biotechnology Lab">
                        <div class="portfolio-info p-3 bg-white">
                            <h6 class="fw-bold mb-1">Robotics & Biotechnology Lab</h6>
                            <p class="text-muted small mb-0">Advanced laboratory protocols and experiential STEM research.</p>
                            <a href="{{ asset('knightone/img/portfolio/portfolio-2.jpg') }}" title="Robotics & Biotechnology Lab" data-gallery="portfolio-gallery" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-sports">
                    <div class="portfolio-content h-100 rounded-4 overflow-hidden border shadow-sm position-relative">
                        <img src="{{ asset('knightone/img/portfolio/portfolio-3.jpg') }}" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="Championship Aquatic Center">
                        <div class="portfolio-info p-3 bg-white">
                            <h6 class="fw-bold mb-1">Championship Aquatic Center</h6>
                            <p class="text-muted small mb-0">Olympic-standard athletic training and varsity swimming programs.</p>
                            <a href="{{ asset('knightone/img/portfolio/portfolio-3.jpg') }}" title="Championship Aquatic Center" data-gallery="portfolio-gallery" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('landing.gallery') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                    <i class="bi bi-images me-1"></i> Explore Complete Institutional Gallery
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Academic Cohorts / Programs Section -->
<section id="pricing" class="pricing section light-background">
    <div class="container section-title text-center" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Curriculum Pathways</span>
        <h2 class="fw-bold text-dark">Academic Cohorts & Programs</h2>
        <p class="text-muted">Structured curriculum stages designed to transition scholars from foundational mastery to collegiate research.</p>
    </div>

    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="pricing-item p-4 bg-white rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-light text-dark border mb-2 px-3 py-1">Junior Secondary</span>
                        <h4 class="fw-bold text-dark">Foundational Division</h4>
                        <div class="my-3">
                            <span class="fs-4 fw-bold text-success">Grades 7 — 9</span>
                            <span class="text-muted small"> / Core Literacy & STEM</span>
                        </div>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Comprehensive Sciences & Applied Algebra</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> World Literature & Rhetoric Fundamentals</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Introductory Coding & Computational Logic</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Daily Biometric / QR Attendance Logging</li>
                        </ul>
                    </div>
                    <a href="#contact" class="btn btn-outline-success rounded-pill w-100 fw-bold mt-4">Inquire Admissions</a>
                </div>
            </div>

            <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="pricing-item featured p-4 bg-white rounded-4 border border-2 shadow h-100 d-flex flex-column justify-content-between position-relative" style="border-color: #009961 !important;">
                    <span class="badge position-absolute top-0 start-50 translate-middle rounded-pill px-3 py-1.5 fw-bold" style="background-color: #009961; color: #fff;">MOST POPULAR</span>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-2 px-3 py-1">Senior Division</span>
                        <h4 class="fw-bold text-dark">Science & Technology Honors</h4>
                        <div class="my-3">
                            <span class="fs-4 fw-bold text-success">Grades 10 — 12</span>
                            <span class="text-muted small"> / Pre-Collegiate Honors</span>
                        </div>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Advanced Mathematics (MTH-201) & Modern Physics</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> University-Level Computer Science (CSC-301)</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Weighted Continuous Assessment Gradebook (4.0 GPA)</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Instant Multi-Channel Guardian Broadcast Sync</li>
                        </ul>
                    </div>
                    <a href="#contact" class="btn btn-success rounded-pill w-100 fw-bold mt-4" style="background-color: #009961; border: none;">Apply for Cohort</a>
                </div>
            </div>

            <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="pricing-item p-4 bg-white rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-light text-dark border mb-2 px-3 py-1">Advanced Diploma</span>
                        <h4 class="fw-bold text-dark">International Baccalaureate</h4>
                        <div class="my-3">
                            <span class="fs-4 fw-bold text-success">AP & IB Prep</span>
                            <span class="text-muted small"> / Global Matriculation</span>
                        </div>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Independent Senior Thesis & Lab Fellowships</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Cryptographic Verified Transcripts for Universities</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Dedicated Academic Advisor & Faculty Mentorship</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Full Access to Digital LMS Resource Library</li>
                        </ul>
                    </div>
                    <a href="#contact" class="btn btn-outline-success rounded-pill w-100 fw-bold mt-4">Inquire Admissions</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Frequently Asked Questions Section -->
<section id="faq" class="faq section">
    <div class="container section-title text-center" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Common Queries</span>
        <h2 class="fw-bold text-dark">Frequently Asked Questions</h2>
        <p class="text-muted">Answers regarding enrollment, security compliance, guardian access, and continuous grading.</p>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item bg-white border rounded-3 mb-2 shadow-sm">
                        <h2 class="accordion-header" id="faq1Heading">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How does the Contactless Smart QR Check-In Terminal operate?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Each enrolled scholar is issued an encoded security badge containing a secure token. Upon arrival at campus gates or homeroom registers, the badge is scanned via webcam or optical scanner, instantaneously recording punctuality into the ledger and calculating real-time attendance rates.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white border rounded-3 mb-2 shadow-sm">
                        <h2 class="accordion-header" id="faq2Heading">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How are continuous assessments weighted into the official report card?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Our academic gradebook operates on a weighted term matrix: Continuous Quizzes contribute 20%, Midterm Exams contribute 30%, and Final Term Examinations contribute 50%. The engine computes weighted GPAs on a 4.0 scale, determines class percentile rankings, and generates printable A4 PDF transcripts.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white border rounded-3 mb-2 shadow-sm">
                        <h2 class="accordion-header" id="faq3Heading">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What makes the Cryptographic Audit Ledger tamper-proof?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Each administrative mutation—including grade adjustments, enrollment status changes, and disciplinary notices—is cryptographically hashed with SHA-256 incorporating the previous record's hash signature. This establishes an unbroken chain of custody preventing retro-active record falsification.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item bg-white border rounded-3 mb-2 shadow-sm">
                        <h2 class="accordion-header" id="faq4Heading">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Can parents view their children's report cards online?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Yes. Verified guardians receive a dedicated Guardian Oversight Desk login where they can inspect their ward's 360 profile, download official signed report cards, receive instant broadcast alerts, and review daily attendance logs.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Admissions Inquiry & Contact Section -->
<section id="contact" class="contact section light-background">
    <div class="container section-title text-center" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: 1.5px;">Admissions & Enrollment</span>
        <h2 class="fw-bold text-dark">Connect with Our Admissions Office</h2>
        <p class="text-muted">Inquire about enrollment cohorts, schedule an on-campus tour, or connect with our academic directors.</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
            <div class="col-lg-5">
                <div class="info-wrap p-4 bg-white rounded-4 shadow-sm border h-100">
                    <div class="info-item d-flex align-items-center mb-4">
                        <div class="p-3 bg-light rounded-3 text-success me-3 fs-4" style="width: 50px; height: 50px; display: grid; place-items: center;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Campus Location:</h6>
                            <p class="text-muted small mb-0">100 University Boulevard, Academic District, Suite 500</p>
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-center mb-4">
                        <div class="p-3 bg-light rounded-3 text-success me-3 fs-4" style="width: 50px; height: 50px; display: grid; place-items: center;">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Admissions Secretariat:</h6>
                            <p class="text-muted small mb-0">+1 (555) 234-5678 / +1 (555) 234-8900</p>
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-center mb-4">
                        <div class="p-3 bg-light rounded-3 text-success me-3 fs-4" style="width: 50px; height: 50px; display: grid; place-items: center;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Electronic Mail:</h6>
                            <p class="text-muted small mb-0">admissions@scholar-academy.edu</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-success-subtle border border-success-subtle small text-success">
                        <strong class="d-block mb-1"><i class="bi bi-clock-history me-1"></i> Admissions Hours</strong>
                        Monday — Friday: 8:00 AM – 4:30 PM &bull; Saturday by appointment.
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <form action="{{ route('landing.inquiry') }}" method="POST" class="p-4 bg-white rounded-4 shadow-sm border">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Guardian / Applicant Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-light shadow-sm" placeholder="e.g. Eleanor Vance" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control border-light shadow-sm" placeholder="e.g. eleanor@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Contact Phone</label>
                            <input type="text" name="phone" class="form-control border-light shadow-sm" placeholder="e.g. +1 (555) 987-6543">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Academic Program of Interest</label>
                            <select name="interest" class="form-select border-light shadow-sm">
                                <option value="Science & Technology Honors (Grade 10)">Science & Technology Honors (Grade 10)</option>
                                <option value="Foundational Secondary (Grades 7 - 9)">Foundational Secondary (Grades 7 - 9)</option>
                                <option value="Pre-University AP / IB Diploma">Pre-University AP / IB Diploma</option>
                                <option value="General Curriculum Inquiry">General Curriculum Inquiry</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Inquiry Message / Scholastic Background <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control border-light shadow-sm" rows="4" placeholder="Detail any prior academic coursework, extracurricular goals, or schedule requirements..." required></textarea>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm" style="background-color: #009961; border: none;">
                                <i class="bi bi-send me-1"></i> Submit Admissions Inquiry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
