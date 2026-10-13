@extends('layouts.portal')

@section('title', 'Faculty Desk — Scholar SMS')

@section('content')
<div class="portal-card p-4 mb-4 text-white rounded-4" style="background: linear-gradient(135deg, #2b6cb0 0%, #1a365d 100%);">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <span class="badge bg-white text-primary mb-2">{{ $staff->department->name ?? 'Academics' }}</span>
            <h2 class="fw-bold mb-1">Welcome, {{ $user->name }}</h2>
            <p class="mb-0 opacity-75">{{ $staff->designation ?? 'Faculty Instructor' }} • Employee ID: <strong>{{ $staff->employee_id ?? 'EMP' }}</strong></p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Active Term: Fall 2026</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cohorts & Class Teacher Duties -->
    <div class="col-lg-6">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-users-rectangle text-primary me-2"></i>My Assigned Cohorts</h5>
                <span class="badge bg-light text-dark border">Form Master</span>
            </div>
            @forelse($myClasses as $c)
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark">{{ $c->name }} (Section {{ $c->section }})</h6>
                        <span class="badge bg-primary">{{ $c->students->count() }} Students</span>
                    </div>
                    <p class="text-muted small mb-3">Room: {{ $c->room_number ?? 'Main Wing' }} • Capacity: {{ $c->capacity }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('attendance.mark', $c->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="fa-solid fa-clipboard-check me-1"></i> Mark Today's Register
                        </a>
                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Attendance History
                        </a>
                    </div>
                </div>
            @empty
                <div class="alert alert-info rounded-3">No direct class master cohorts assigned.</div>
            @endforelse
        </div>
    </div>

    <!-- My Courses & LMS Hub -->
    <div class="col-lg-6">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-book-open-reader text-success me-2"></i>My Active Courses</h5>
                <a href="{{ route('courses.index') }}" class="btn btn-sm btn-link text-decoration-none">All Courses</a>
            </div>
            @forelse($myCourses as $course)
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-3 bg-white">
                    <div>
                        <div class="fw-bold text-dark">{{ $course->title }}</div>
                        <small class="text-muted">{{ $course->code }} • {{ $course->materials->count() }} LMS Materials</small>
                    </div>
                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        LMS Desk <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            @empty
                <div class="text-muted text-center py-4">No active courses assigned.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Uploaded Materials & Broadcast Feed -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="portal-card p-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-arrow-up text-warning me-2"></i>My Recently Published Materials</h5>
            @forelse($recentMaterials as $mat)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div>
                        <div class="fw-semibold small text-dark">{{ $mat->title }}</div>
                        <small class="text-muted">{{ strtoupper($mat->file_type) }} • {{ $mat->file_size }} • {{ $mat->download_count }} Downloads</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <a href="{{ route('materials.preview', $mat->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5" style="font-size: 12px; border-color: #009961; color: #009961;" title="Review Material">
                            <i class="fa-solid fa-eye me-1"></i> Review
                        </a>
                        <a href="{{ route('materials.download', $mat->id) }}" class="btn btn-sm btn-light rounded-circle p-2" title="Download Asset">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-muted text-center py-3">No materials uploaded yet.</div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-6">
        <div class="portal-card p-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-bullhorn text-danger me-2"></i>Campus Notices & Broadcasts</h5>
            @foreach($broadcasts as $b)
                <div class="p-3 bg-light rounded-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="small text-dark">{{ $b->title }}</strong>
                        <span class="badge bg-{{ $b->priority === 'urgent' ? 'danger' : 'primary' }}">{{ $b->priority }}</span>
                    </div>
                    <p class="text-muted small mb-0">{{ Str::limit($b->message, 100) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection