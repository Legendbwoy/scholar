@extends('layouts.public')

@section('title', $course->title . ' — Scholar SMS Curriculum')

@section('content')
<!-- Page Title Header -->
<div class="page-title dark-background position-relative py-5 text-center" style="background: url('{{ asset("knightone/img/hero-bg.jpg") }}') center center / cover no-repeat; margin-top: 70px;">
    <div class="container position-relative py-5">
        <span class="badge rounded-pill px-3 py-1.5 mb-2 text-uppercase fw-bold" style="background-color: rgba(0, 153, 97, 0.25); border: 1px solid #009961; color: #4ade80;">{{ $course->code }} &bull; {{ $course->category ?? 'Curriculum Track' }}</span>
        <h1 class="fw-bolder text-white mb-2" style="font-family: 'Raleway', sans-serif;">{{ $course->title }}</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 680px;">Assigned to Cohort: <strong>{{ $course->schoolClass->name ?? 'General Division' }}</strong> &bull; Lead Faculty: <strong>{{ $course->teacher->user->name ?? 'Faculty Fellow' }}</strong></p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Main Curriculum Details -->
        <div class="col-lg-8">
            <div class="p-4 bg-white rounded-4 shadow-sm border mb-4">
                <h4 class="fw-bold text-dark mb-3">Course Syllabus & Description</h4>
                <p class="text-muted leading-relaxed">{{ $course->description }}</p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">Curriculum Learning Modules</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <i class="bi bi-journal-code text-success fs-4 d-block mb-1"></i>
                            <strong class="text-dark d-block">Theoretical Foundations</strong>
                            <small class="text-muted">Rigorous conceptual analysis, textbook readings & weekly seminars.</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <i class="bi bi-cpu text-success fs-4 d-block mb-1"></i>
                            <strong class="text-dark d-block">Experiential Laboratory Work</strong>
                            <small class="text-muted">Hands-on experimentation, computational simulations & lab reports.</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold"><i class="bi bi-award text-warning me-1"></i> Continuous Assessment Weighted (4.0 GPA)</span>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" style="background-color: #009961; border: none;">
                        <i class="bi bi-download me-1"></i> Access Course Materials in LMS
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Snapshot -->
        <div class="col-lg-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border mb-4 text-center">
                <img src="{{ asset('knightone/img/services.jpg') }}" class="rounded-4 mb-3 w-100" style="height: 180px; object-fit: cover;">
                <h5 class="fw-bold text-dark mb-1">{{ $course->title }}</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 mb-3">{{ $course->code }}</span>

                <div class="text-start border-top pt-3 small">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Assigned Faculty:</span>
                        <strong class="text-dark">{{ $course->teacher->user->name ?? 'Faculty' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Cohort Division:</span>
                        <strong class="text-dark">{{ $course->schoolClass->name ?? 'General' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Academic Term:</span>
                        <strong class="text-primary">{{ $course->semester ?? 'Fall Semester' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Total Units:</span>
                        <strong class="text-dark">{{ $course->total_lessons ?? 12 }} Modules</strong>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('login') }}" class="btn btn-success rounded-pill fw-bold" style="background-color: #009961; border: none;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Student LMS Login
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Back to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
