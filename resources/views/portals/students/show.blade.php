@extends('layouts.portal')

@section('title', 'Student Profile 360 â€” Scholar SMS')

@section('content')
<div class="mb-4">
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
        <a href="{{ route('students.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Roster</a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
    @endif
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0">Student Profile 360: {{ $student->user->name }}</h2>
            <p class="text-muted mb-0">Admission No: <strong>{{ $student->admission_number }}</strong> â€¢ Cohort: <strong>{{ $student->schoolClass->name ?? 'Enrolled' }}</strong></p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $student->status === 'active' ? 'success' : 'danger' }} fs-6 px-3 py-2 border">
                Status: {{ ucfirst($student->status) }}
            </span>
            <a href="{{ route('gradebook.report-card', $student->id) }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold" target="_blank">
                <i class="fa-solid fa-print me-1"></i> Print Official Report Card
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile
            </a>
            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline confirm-action" 
                  data-confirm-title="Suspend Student Dossier?" 
                  data-confirm-text="Deactivating {{ $student->user->name }} ({{ $student->admission_number }}) will restrict portal login and archive attendance tracking." 
                  data-confirm-btn="Suspend Student" 
                  data-confirm-color="#dc3545">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="fa-solid fa-box-archive me-1"></i> Suspend
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <!-- Identity Card -->
        <div class="portal-card p-4 text-center">
            <img src="{{ $student->user->avatar_url }}" class="rounded-circle shadow-sm mb-3" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #eef0f6;">
            <h4 class="fw-bold mb-1">{{ $student->user->name }}</h4>
            <p class="text-muted small mb-3">{{ $student->user->email }}</p>

            <div class="p-3 bg-light rounded-3 mb-3 text-start small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Admission No:</span>
                    <strong>{{ $student->admission_number }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Cohort:</span>
                    <strong>{{ $student->schoolClass->name ?? 'N/A' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Roll Number:</span>
                    <strong>{{ $student->roll_number }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Gender / Blood:</span>
                    <strong>{{ $student->gender }} ({{ $student->blood_group }})</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Enrollment Date:</span>
                    <strong>{{ $student->admission_date ?? 'N/A' }}</strong>
                </div>
            </div>

            <div class="p-3 bg-success-subtle rounded-3 text-success">
                <small class="d-block fw-semibold">Attendance Standing</small>
                <h3 class="fw-bold mb-0">{{ $student->attendance_rate }}%</h3>
            </div>
        </div>

        <!-- Guardian Info Card -->
        @if($student->guardian)
            <div class="portal-card p-4">
                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-people-roof me-2"></i>Guardian Information</h6>
                <div class="small">
                    <div class="fw-bold">{{ $student->guardian->user->name }} ({{ $student->guardian->relationship }})</div>
                    <div class="text-muted mb-1">{{ $student->guardian->occupation ?? 'Parent' }}</div>
                    <div class="text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $student->guardian->user->phone ?? 'None on record' }}</div>
                    <div class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> {{ $student->guardian->address ?? 'Main Campus District' }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-8">
        <!-- Attendance History Table -->
        <div class="portal-card p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-calendar-check text-success me-2"></i>Verified Attendance Ledger</h5>
            <div class="table-responsive">
                <table class="table table-portal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Verification Method</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $att->status === 'present' ? 'success' : ($att->status === 'late' ? 'warning text-dark' : 'danger') }} text-capitalize">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ ucfirst($att->method) }}</span></td>
                                <td><small class="text-muted">{{ $att->remarks ?? 'On-time standard verification' }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No attendance events recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendances->hasPages())
                <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries</small>
                    <div>
                        {{ $attendances->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
