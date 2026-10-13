@extends('layouts.portal')

@section('title', 'Wards Attendance & Safety — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Wards' Attendance & Safety Oversight</h2>
        <p class="text-muted mb-0">Live Campus Entry Verification, Daily Punctuality & Instant Safety Alerts</p>
    </div>
    <div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6 fw-bold">
            <i class="fa-solid fa-tower-broadcast me-1"></i> SMS Gateway: Active
        </span>
    </div>
</div>

<div class="alert alert-info rounded-4 border-info-subtle shadow-sm mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="fs-3 text-info"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
            <h6 class="fw-bold mb-1">Guardian Safety Protocol Active</h6>
            <p class="mb-0 small text-muted">Whenever a ward arrives on campus, our smart biometric & QR terminals register their status. If an unexplained absence occurs by 8:30 AM, an instant SMS alert is dispatched to your registered phone number.</p>
        </div>
    </div>
</div>

@if($wards->count() > 0)
    @foreach($wards as $ward)
    <div class="portal-card mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light bg-opacity-50">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset($ward->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle border" style="width: 54px; height: 54px; object-fit: cover;">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $ward->user->name }}</h4>
                        <small class="text-muted">
                            Admission #: <strong>{{ $ward->admission_number }}</strong> | 
                            Cohort: <strong>{{ $ward->schoolClass->name ?? 'Unassigned' }}</strong> | 
                            Roll: <strong>{{ $ward->roll_number }}</strong>
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Overall Rate</small>
                        <h4 class="fw-bold mb-0 text-{{ $ward->attendance_rate >= 80 ? 'success' : 'warning' }}">{{ $ward->attendance_rate }}%</h4>
                    </div>
                    <a href="{{ route('students.show', $ward->id) }}" class="btn btn-outline-primary rounded-pill px-3 btn-sm">
                        <i class="fa-solid fa-id-card me-1"></i> View 360 File
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4">
            <h6 class="fw-bold mb-3"><i class="fa-regular fa-calendar-check me-2 text-primary"></i>Recent Punctuality & Verification Ledger</h6>
            <div class="table-responsive">
                <table class="table table-portal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Cohort</th>
                            <th>Verification Checkpoint</th>
                            <th>Faculty Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ward->attendances as $att)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</small>
                                </td>
                                <td>
                                    @if($att->status === 'present')
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">
                                            <i class="fa-solid fa-check me-1"></i> Present
                                        </span>
                                    @elseif($att->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill">
                                            <i class="fa-solid fa-clock me-1"></i> Late Arrival
                                        </span>
                                    @elseif($att->status === 'excused')
                                        <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill">
                                            <i class="fa-solid fa-file-signature me-1"></i> Excused
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Absent
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $ward->schoolClass->name ?? 'Standard Class' }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="fa-solid fa-fingerprint me-1 text-primary"></i> Main Gate Terminal
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $att->remarks ?? 'Verified attendance entry.' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No recent attendance entries recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="alert alert-warning rounded-4 shadow-sm p-4 text-center">
    <i class="fa-solid fa-child-reaching fs-2 mb-2 d-block text-warning"></i>
    <h5>No Linked Wards Found</h5>
    <p class="mb-0 text-muted">Your guardian profile currently has no registered students linked to it. Please reach out to student affairs.</p>
</div>
@endif
@endsection
