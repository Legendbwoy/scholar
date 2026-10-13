@extends('layouts.portal')

@section('title', 'Student Academic Portal — Scholar SMS')

@section('content')
<div class="portal-card p-4 mb-4 text-white rounded-4" style="background: linear-gradient(135deg, #553c9a 0%, #322659 100%);">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <span class="badge bg-warning text-dark mb-2">Student Digital Identity</span>
            <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
            <p class="mb-0 opacity-75">
                Cohort: <strong>{{ $class->name ?? 'Enrolled' }}</strong> • Admission No: <strong>{{ $student->admission_number ?? 'SCH' }}</strong> • Roll: <strong>{{ $student->roll_number ?? 'R' }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-start">
                <small class="d-block opacity-75">My Attendance Score</small>
                <h3 class="fw-bold mb-0 text-warning">{{ $attendanceRate }}%</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Enrolled Courses & Downloadable Materials -->
    <div class="col-lg-7">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>My Enrolled Courses & LMS</h5>
                <span class="badge bg-light text-dark border">{{ $courses->count() }} Subjects</span>
            </div>
            @forelse($courses as $course)
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $course->title }}</h6>
                            <small class="text-muted"><i class="fa-solid fa-chalkboard-user me-1"></i> {{ $course->teacher->user->name ?? 'Faculty' }} • Code: {{ $course->code }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $course->materials->count() }} Materials</span>
                    </div>

                    @if($course->materials->count() > 0)
                        <div class="mt-3 pt-2 border-top">
                            <small class="fw-bold text-muted d-block mb-2">Downloadable Study Materials:</small>
                            @foreach($course->materials as $mat)
                                <div class="d-flex justify-content-between align-items-center py-1 small">
                                    <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>{{ $mat->title }}</span>
                                    <a href="{{ route('materials.download', $mat->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 11px;">
                                        <i class="fa-solid fa-download me-1"></i> Download ({{ $mat->file_size }})
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="alert alert-info rounded-3">No courses registered for this cohort.</div>
            @endforelse
        </div>
    </div>

    <!-- Recent Attendance Records -->
    <div class="col-lg-5">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-calendar-check text-success me-2"></i>My Attendance Register</h5>
                <span class="badge bg-success-subtle text-success border border-success">{{ $attendanceRate }}% Standing</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Date</th>
                            <th>Status</th>
                            <th>Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                            <tr>
                                <td class="small">{{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $att->status === 'present' ? 'success' : ($att->status === 'late' ? 'warning text-dark' : 'danger') }} text-capitalize">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ ucfirst($att->method) }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No attendance records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Broadcasts Noticeboard -->
<div class="portal-card p-4">
    <h5 class="fw-bold mb-3"><i class="fa-solid fa-bullhorn text-warning me-2"></i>School Announcements & Comms</h5>
    <div class="row g-3">
        @foreach($broadcasts as $b)
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark small">{{ $b->title }}</strong>
                        <small class="text-muted">{{ $b->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="text-muted small mb-0">{{ $b->message }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection