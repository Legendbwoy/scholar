@extends('layouts.portal')

@section('title', 'My Attendance Standing — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">My Attendance Standing</h2>
        <p class="text-muted mb-0">Biometric Verification, Check-In Records & Academic Punctuality Standing</p>
    </div>
    <div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6 fw-bold">
            <i class="fa-solid fa-circle-check me-1"></i> Standing: {{ $student ? ($student->attendance_rate >= 80 ? 'Good Standing' : 'Under Review') : 'N/A' }}
        </span>
    </div>
</div>

@if($student)
<!-- Student Attendance KPI Summary -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Attendance Rate</p>
                <h3 class="text-primary">{{ $student->attendance_rate }}%</h3>
                <small class="text-muted">Academic threshold: 75%</small>
            </div>
            <div class="icon-box bg-primary-subtle text-primary">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Present Days</p>
                <h3 class="text-success">{{ $presentDays }}</h3>
                <small class="text-success">On-time check-ins</small>
            </div>
            <div class="icon-box bg-success-subtle text-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Late Check-ins</p>
                <h3 class="text-warning">{{ $lateDays }}</h3>
                <small class="text-warning">After bell recorded</small>
            </div>
            <div class="icon-box bg-warning-subtle text-warning">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Absences Recorded</p>
                <h3 class="text-danger">{{ $absentDays }}</h3>
                <small class="text-danger">{{ $excusedDays }} Excused</small>
            </div>
            <div class="icon-box bg-danger-subtle text-danger">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Record Log -->
<div class="portal-card">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Daily Verification Ledger</h5>
            <small class="text-muted">Cohort: {{ $student->schoolClass->name ?? 'Unassigned' }} | Roll: {{ $student->roll_number }}</small>
        </div>
        <span class="badge bg-light text-dark border">Total Records: {{ $totalDays }}</span>
    </div>

    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Cohort / Class</th>
                    <th>Verification Source</th>
                    <th>Official Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</small>
                        </td>
                        <td>
                            @if($att->status === 'present')
                                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-check me-1"></i> Present
                                </span>
                            @elseif($att->status === 'late')
                                <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-clock me-1"></i> Late
                                </span>
                            @elseif($att->status === 'excused')
                                <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-file-signature me-1"></i> Excused
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-xmark me-1"></i> Absent
                                </span>
                            @endif
                        </td>
                        <td>{{ $att->schoolClass->name ?? 'Standard Class' }}</td>
                        <td>
                            <span class="badge bg-light text-secondary border">
                                <i class="fa-solid fa-qrcode me-1 text-primary"></i> QR / Biometric Terminal
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $att->remarks ?? 'Standard on-time campus arrival verified.' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-question fs-2 d-block mb-2"></i> No attendance logs recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($attendances, 'links'))
        <div class="p-4 border-top">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@else
<div class="alert alert-warning rounded-4 shadow-sm p-4 text-center">
    <i class="fa-solid fa-triangle-exclamation fs-2 mb-2 d-block text-warning"></i>
    <h5>Student Profile Pending Linking</h5>
    <p class="mb-0 text-muted">Your user account does not have a linked active student profile. Please contact the registrar office.</p>
</div>
@endif
@endsection
