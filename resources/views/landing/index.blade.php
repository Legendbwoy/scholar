@extends('layouts.public')

@section('title', 'Scholar SMS — Comprehensive Modern School Management & Learning Platform')

@section('content')

<!-- Main Hero Banner Section -->
<div class="main-banner" id="top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="owl-carousel owl-banner">
                    <!-- Slide 1 -->
                    <div class="item item-1">
                        <div class="header-text">
                            <span class="category">Premier Academy SMS</span>
                            <h2>Empowering Education with Intelligent Management & Digital LMS</h2>
                            <p>An enterprise-grade School Management System uniting students, faculty, and guardians through seamless multi-channel communications, cryptographic audit integrity, and interactive learning.</p>
                            <div class="buttons">
                                <div class="main-button">
                                    <a href="#contact"><i class="fa-solid fa-file-signature me-1"></i> Apply for Admission</a>
                                </div>
                                <div class="icon-button">
                                    <a href="{{ route('login') }}"><i class="fa-solid fa-door-open"></i> Access Portals</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="item item-2">
                        <div class="header-text">
                            <span class="category">Ultra Modern Comms</span>
                            <h2>Instant Omni-Channel Broadcasts to Teachers, Students & Parents</h2>
                            <p>Keep the entire school community synchronized with targeted SMS, Email, WhatsApp, and in-app automated broadcasts with dynamic variable merging and verified delivery receipts.</p>
                            <div class="buttons">
                                <div class="main-button">
                                    <a href="#courses"><i class="fa-solid fa-book-open-reader me-1"></i> Explore Courses</a>
                                </div>
                                <div class="icon-button">
                                    <a href="{{ route('login') }}"><i class="fa-solid fa-bullhorn"></i> Comms Demo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="item item-3">
                        <div class="header-text">
                            <span class="category">Audit & Compliance</span>
                            <h2>Cryptographically Sealed Audit Trails & Contactless Attendance</h2>
                            <p>Every attendance record, broadcast trigger, and profile modification is permanently sealed into an immutable SHA-256 chained ledger for uncompromised security.</p>
                            <div class="buttons">
                                <div class="main-button">
                                    <a href="#gallery"><i class="fa-solid fa-images me-1"></i> Campus Life Gallery</a>
                                </div>
                                <div class="icon-button">
                                    <a href="{{ route('login') }}"><i class="fa-solid fa-shield-check"></i> Security Specs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Institutional Stats Section -->
<div class="container my-5 py-3">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="stat-box">
                <h2>{{ $stats['students'] }}+</h2>
                <p>Enrolled Students</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="stat-box">
                <h2>{{ $stats['faculty'] }}+</h2>
                <p>Certified Faculty</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="stat-box">
                <h2>{{ $stats['courses'] }}+</h2>
                <p>Academic Courses</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="stat-box">
                <h2>{{ $stats['awards'] }}</h2>
                <p>STEM & Arts Awards</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-12">
            <div class="stat-box" style="border-bottom-color: #f35525;">
                <h2 style="color: #f35525;">{{ $stats['attendance_rate'] }}</h2>
                <p>Average Daily Attendance</p>
            </div>
        </div>
    </div>
</div>

<!-- Services / Capabilities Grid -->
<div class="services section" id="services">
    <div class="container">
        <div class="row mb-5 text-center">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h6>Core Architecture</h6>
                    <h2>Everything A Modern <em>School System</em> Needs</h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-primary">
                    <div class="icon mb-3">
                        <img src="{{ asset('scholar/assets/images/service-01.png') }}" alt="Student Management" style="max-width: 65px;">
                    </div>
                    <div class="main-content">
                        <h4>Student & Staff Records</h4>
                        <p>Complete 360-degree demographic management, academic history, roll numbers, guardian links, department assignments, and credentials tracking.</p>
                        <div class="main-button mt-3">
                            <a href="{{ route('login') }}">Access Registry</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-warning">
                    <div class="icon mb-3">
                        <img src="{{ asset('scholar/assets/images/service-02.png') }}" alt="LMS Course Materials" style="max-width: 65px;">
                    </div>
                    <div class="main-content">
                        <h4>LMS & Course Materials</h4>
                        <p>Curriculum modules, downloadable lecture notes, laboratory PDF worksheets, slide presentations, and video links with version control and tracking.</p>
                        <div class="main-button mt-3">
                            <a href="#courses">Browse Curriculum</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-danger">
                    <div class="icon mb-3">
                        <img src="{{ asset('scholar/assets/images/service-03.png') }}" alt="Broadcast Communications" style="max-width: 65px;">
                    </div>
                    <div class="main-content">
                        <h4>Ultra Modern Comms Hub</h4>
                        <p>Omni-channel broadcast engine spanning SMS, Email, WhatsApp, Push, and In-App delivery with template variable tags and instant absence alerts.</p>
                        <div class="main-button mt-3">
                            <a href="{{ route('login') }}">Test Broadcasts</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-success">
                    <div class="icon mb-3 text-success fs-1">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div class="main-content">
                        <h4>Smart Attendance & QR</h4>
                        <p>Fast register marking with 1-click status toggling, biometric or rotating QR code check-in terminals, and automated immediate parent notifications.</p>
                        <div class="main-button mt-3">
                            <a href="{{ route('login') }}">Check Attendance</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-info">
                    <div class="icon mb-3 text-info fs-1">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="main-content">
                        <h4>Immutable Audit Trail</h4>
                        <p>Every sensitive action creates an immutable, SHA-256 hash-chained ledger row that verifies against tampering for compliance and data integrity.</p>
                        <div class="main-button mt-3">
                            <a href="{{ route('login') }}">Inspect Audit Logs</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-item p-4 rounded-4 shadow-sm bg-white h-100 border-top border-4 border-secondary">
                    <div class="icon mb-3 text-secondary fs-1">
                        <i class="fa-solid fa-network-wired"></i>
                    </div>
                    <div class="main-content">
                        <h4>Extensible REST APIs</h4>
                        <p>Production-ready REST endpoints for third-party integrations, external messaging gateways, biometric card readers, and future mobile PWA clients.</p>
                        <div class="main-button mt-3">
                            <a href="{{ url('/api/v1/health') }}" target="_blank">View API Health</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Courses & LMS Materials Showcase -->
<section class="section courses" id="courses">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12 text-center">
                <div class="section-heading">
                    <h6>Featured Curriculum</h6>
                    <h2>Latest Academic <em>Courses & Materials</em></h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-lg-4 col-md-6">
                    <div class="course-card">
                        <div class="thumb">
                            <img src="{{ asset($course->thumbnail ?? 'scholar/assets/images/course-01.jpg') }}" alt="{{ $course->title }}">
                            <span class="price"><i class="fa-solid fa-layer-group me-1"></i> {{ $course->category }}</span>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge-cat"><i class="fa-solid fa-graduation-cap me-1"></i> {{ $course->schoolClass->name ?? 'All Grades' }}</span>
                                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> {{ $course->total_lessons }} Lessons</small>
                            </div>
                            <h5 class="fw-bold mb-2">{{ $course->title }}</h5>
                            <p class="text-muted small flex-grow-1">{{ Str::limit($course->description, 100) }}</p>

                            <hr class="my-3 opacity-25">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($course->teacher->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                                    <small class="fw-semibold">{{ $course->teacher->user->name ?? 'Faculty Staff' }}</small>
                                </div>
                                <a href="{{ route('landing.course', $course->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View Syllabus <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Campus Life Gallery Section -->
<section class="section gallery-section py-5 bg-light" id="gallery">
    <div class="container">
        <div class="row mb-4 align-items-end">
            <div class="col-lg-8">
                <div class="section-heading mb-0">
                    <h6>Vibrant Campus Life</h6>
                    <h2>State-of-the-Art <em>Facilities & Events</em></h2>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('landing.gallery') }}" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="fa-solid fa-images me-1"></i> View Full Gallery ({{ $galleryItems->count() }})
                </a>
            </div>
        </div>

        <div class="row g-4 mt-2">
            @foreach($galleryItems->take(6) as $item)
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $item->id }}">
                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">
                        <div class="overlay">
                            <span class="badge bg-warning text-dark w-auto align-self-start mb-2">{{ $item->category }}</span>
                            <h5 class="fw-bold mb-1">{{ $item->title }}</h5>
                            <p class="small mb-0 text-light">{{ $item->caption }}</p>
                        </div>
                    </div>

                    <!-- Modal Preview -->
                    <div class="modal fade" id="galleryModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                                <div class="modal-body p-0 position-relative">
                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal"></button>
                                    <img src="{{ asset($item->image_path) }}" class="w-100" style="max-height: 500px; object-fit: cover;">
                                    <div class="p-4 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">{{ $item->category }}</span>
                                            <small class="text-muted">Scholar Academy Media Archive</small>
                                        </div>
                                        <h4 class="fw-bold">{{ $item->title }}</h4>
                                        <p class="text-muted mb-0">{{ $item->caption }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Faculty & Leadership Team -->
<div class="team section" id="team">
    <div class="container">
        <div class="row mb-5 text-center">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h6>World-Class Instructors</h6>
                    <h2>Meet Our <em>Distinguished Faculty</em></h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @foreach($staffMembers as $staff)
                <div class="col-lg-3 col-md-6">
                    <div class="team-member text-center p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="main-image mb-3">
                            <img src="{{ asset($staff->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" alt="{{ $staff->user->name }}" class="rounded-circle shadow-sm" style="width: 140px; height: 140px; object-fit: cover; border: 4px solid #f0f2f8;">
                        </div>
                        <span class="badge bg-light text-primary mb-2">{{ $staff->department->name ?? 'Academics' }}</span>
                        <h5 class="fw-bold mb-1">{{ $staff->user->name }}</h5>
                        <p class="text-muted small mb-2">{{ $staff->designation }}</p>
                        <p class="small text-secondary mb-3">{{ $staff->qualification }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-sm btn-light rounded-circle text-muted"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-sm btn-light rounded-circle text-muted"><i class="fa-regular fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Testimonials & Institutional Events -->
<div class="section events" id="events">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <div class="section-heading">
                    <h6>Upcoming Dates</h6>
                    <h2>Campus Calendar & <em>Institutional Events</em></h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-12 col-md-6">
                <div class="item p-4 bg-white rounded-4 shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <div class="image">
                                <img src="{{ asset('scholar/assets/images/event-01.jpg') }}" alt="" class="rounded-3 w-100" style="height: 160px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <ul>
                                <li><span class="category">STEM Excellence</span><h4>Annual National Science & Robotics Exposition</h4></li>
                                <li><span>Date:</span><h6>28 Oct 2026</h6></li>
                                <li><span>Duration:</span><h6>09:00 - 16:30</h6></li>
                                <li><span>Location:</span><h6>Auditorium A</h6></li>
                            </ul>
                            <a href="{{ route('login') }}"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-6">
                <div class="item p-4 bg-white rounded-4 shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <div class="image">
                                <img src="{{ asset('scholar/assets/images/event-02.jpg') }}" alt="" class="rounded-3 w-100" style="height: 160px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <ul>
                                <li><span class="category">Community</span><h4>Quarterly Parent-Teacher Consultative Assembly</h4></li>
                                <li><span>Date:</span><h6>05 Nov 2026</h6></li>
                                <li><span>Duration:</span><h6>14:00 - 18:00</h6></li>
                                <li><span>Location:</span><h6>Conference Hall</h6></li>
                            </ul>
                            <a href="{{ route('login') }}"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admission & Contact Form -->
<div class="contact-us section py-5" id="contact">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="section-heading mb-4">
                    <h6>Admissions Open</h6>
                    <h2>Begin Your Educational Journey with <em>Scholar SMS</em></h2>
                </div>
                <p class="lead text-muted mb-4">We are currently accepting enrollment inquiries for the 2026-2027 Academic Session. Connect with our admissions counselors or arrange a campus tour today.</p>

                <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm mb-3">
                    <div class="p-3 bg-primary text-white rounded-3 fs-4">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Admissions Advisory Hotline</h6>
                        <p class="text-muted mb-0">+1 (555) 724-6527 (Mon-Fri 8:00AM - 5:00PM)</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm">
                    <div class="p-3 bg-success text-white rounded-3 fs-4">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Campus Tours & Admissions Desk</h6>
                        <p class="text-muted mb-0">100 Scholar Way, Innovation Campus, Tech District</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-lg p-4 p-md-5 bg-white">
                    <h3 class="fw-bold mb-2">Admissions & Inquiry Form</h3>
                    <p class="text-muted mb-4">Submit your details to receive full syllabus brochures and application instructions.</p>

                    <form action="{{ route('landing.inquiry') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Student / Guardian Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg rounded-3" placeholder="e.g. Eleanor Vance" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-3" placeholder="name@domain.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Phone</label>
                                <input type="text" name="phone" class="form-control form-control-lg rounded-3" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Program / Grade Level</label>
                                <select name="interest" class="form-select form-select-lg rounded-3">
                                    <option value="Grade 10 STEM Track">Grade 10 — STEM Alpha</option>
                                    <option value="Grade 11 Honors">Grade 11 — STEM Honors</option>
                                    <option value="Grade 12 College Prep">Grade 12 — Collegiate Prep</option>
                                    <option value="General Inquiry">General Academic Inquiry</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Your Message or Questions *</label>
                                <textarea name="message" rows="4" class="form-control rounded-3" placeholder="Tell us about student interests, previous schooling, or special requirements..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Admissions Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection