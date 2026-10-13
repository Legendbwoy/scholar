@extends('layouts.public')

@section('title', $course->title . ' — Scholar SMS Curriculum')

@section('content')
<div class="py-5 bg-light" style="margin-top: 80px;">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-2">{{ $course->category }} Track</span>
                <span class="badge bg-secondary px-3 py-2 mb-2">{{ $course->semester }}</span>
                <h1 class="fw-bold mt-2 mb-3">{{ $course->title }}</h1>
                <p class="lead text-muted">{{ $course->description }}</p>
                <div class="d-flex flex-wrap gap-4 align-items-center mt-4">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset($course->teacher->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <small class="text-muted d-block">Lead Faculty</small>
                            <strong>{{ $course->teacher->user->name ?? 'Faculty Fellow' }}</strong>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Assigned Cohort</small>
                        <strong>{{ $course->schoolClass->name ?? 'All Grades' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Course Code</small>
                        <span class="badge bg-dark">{{ $course->code }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Lessons</small>
                        <strong>{{ $course->total_lessons }} Units</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="{{ asset($course->thumbnail ?? 'scholar/assets/images/course-01.jpg') }}" class="w-100" style="height: 240px; object-fit: cover;">
                    <div class="p-3 bg-white">
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                            <i class="fa-solid fa-lock me-1"></i> Log in to LMS for Full Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-3"><i class="fa-solid fa-folder-open text-primary me-2"></i>Uploaded LMS Learning Resources</h3>
            <p class="text-muted mb-4">Official syllabus, problem sets, and interactive materials linked to this course.</p>

            @forelse($course->materials as $mat)
                <div class="card border-0 rounded-4 shadow-sm mb-3 p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 {{ $mat->file_type === 'pdf' ? 'bg-danger text-white' : ($mat->file_type === 'slides' ? 'bg-warning text-dark' : 'bg-primary text-white') }} fs-4">
                                <i class="fa-solid {{ $mat->file_type === 'pdf' ? 'fa-file-pdf' : ($mat->file_type === 'slides' ? 'fa-file-powerpoint' : 'fa-file-lines') }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $mat->title }}</h6>
                                <p class="text-muted small mb-0">{{ Str::limit($mat->description, 110) }}</p>
                                <div class="mt-1">
                                    <span class="badge bg-light text-dark"><i class="fa-solid fa-hard-drive me-1"></i> {{ $mat->file_size }}</span>
                                    <span class="badge bg-light text-dark"><i class="fa-solid fa-code-branch me-1"></i> v{{ $mat->version }}</span>
                                    <span class="badge bg-light text-dark"><i class="fa-solid fa-download me-1"></i> {{ $mat->download_count }} Downloads</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            @auth
                                <a href="{{ route('materials.download', $mat->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fa-solid fa-download me-1"></i> Download File
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="fa-solid fa-lock me-1"></i> Login to Access
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info rounded-4">
                    <i class="fa-solid fa-circle-info me-2"></i> Materials currently being compiled by lead instructor.
                </div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Course Instructor</h5>
                <div class="text-center mb-3">
                    <img src="{{ asset($course->teacher->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle mb-2" style="width: 90px; height: 90px; object-fit: cover;">
                    <h5 class="fw-bold mb-0">{{ $course->teacher->user->name }}</h5>
                    <p class="text-muted small mb-1">{{ $course->teacher->designation }}</p>
                    <span class="badge bg-light text-secondary">{{ $course->teacher->qualification }}</span>
                </div>
                <hr>
                <p class="small text-muted mb-0">Specialization: <strong>{{ $course->teacher->specialization ?? 'General Sciences' }}</strong></p>
            </div>

            <div class="card border-0 rounded-4 shadow-sm p-4 bg-primary text-white">
                <h5 class="fw-bold mb-2">Interested in Enrolling?</h5>
                <p class="small opacity-75 mb-3">Submit your admissions inquiry or speak with our academic counselors today.</p>
                <a href="{{ route('home') }}#contact" class="btn btn-light rounded-pill fw-bold text-primary w-100">
                    Apply Now <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection