@extends('layouts.portal')

@section('title', 'Parent & Guardian Portal — Scholar SMS')

@section('content')
<div class="portal-card p-4 mb-4 text-white rounded-4" style="background: linear-gradient(135deg, #2c5282 0%, #1a365d 100%);">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <span class="badge bg-warning text-dark mb-2">Guardian Oversight Portal</span>
            <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
            <p class="mb-0 opacity-75">Linked Wards / Students: <strong>{{ $children->count() }}</strong> • Direct Academy Contact Verified</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-success px-3 py-2"><i class="fa-solid fa-bell me-1"></i> SMS & Email Alerts Active</span>
        </div>
    </div>
</div>

<!-- Linked Children Cards -->
<div class="row g-4 mb-4">
    @forelse($children as $child)
        <div class="col-lg-6">
            <div class="portal-card p-4 h-100 border-top border-4 border-primary">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset($child->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $child->user->name }}</h5>
                        <small class="text-muted">Admission No: <strong>{{ $child->admission_number }}</strong> • Cohort: <strong>{{ $child->schoolClass->name ?? 'Enrolled' }}</strong></small>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Attendance Standing</small>
                            <strong class="text-success fs-5">{{ $child->attendance_rate }}%</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Class Teacher</small>
                            <strong class="text-dark small">{{ $child->schoolClass->classTeacher->user->name ?? 'Advisor' }}</strong>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold small mb-2">Recent Attendance Days:</h6>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($child->attendances->take(5) as $att)
                        <span class="badge bg-{{ $att->status === 'present' ? 'success' : ($att->status === 'late' ? 'warning text-dark' : 'danger') }} p-2">
                            {{ \Carbon\Carbon::parse($att->date)->format('M d') }}: {{ ucfirst($att->status) }}
                        </span>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                        <i class="fa-solid fa-calendar-days me-1"></i> Full Attendance Log
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info rounded-3">No student profiles currently linked to this guardian account.</div>
        </div>
    @endforelse
</div>

<!-- Broadcast Notifications Sent to Parent -->
<div class="portal-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i>Institutional Parent Advisories</h5>
        <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Noticeboard <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-3">
        @forelse($broadcasts as $b)
            <div class="col-md-6">
                <a href="{{ route('broadcasts.show', $b->id) }}" class="text-decoration-none">
                    <div class="p-3 bg-light rounded-3 border h-100 hover-shadow transition">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark small">{{ $b->title }}</strong>
                            <span class="badge bg-{{ $b->priority === 'urgent' ? 'danger' : 'primary' }}" style="font-size: 10px;">{{ $b->priority }}</span>
                        </div>
                        <p class="text-muted small mb-2">{{ Str::limit($b->message, 120) }}</p>
                        <small class="text-muted d-block" style="font-size: 11px;"><i class="fa-regular fa-clock me-1"></i> {{ $b->created_at->format('M d, Y H:i') }}</small>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="p-3 bg-light rounded-3 text-center text-muted small">
                    No active advisories issued for guardians at this time.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection