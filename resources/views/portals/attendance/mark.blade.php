@extends('layouts.portal')

@section('title', 'Mark Class Attendance — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Attendance Dashboard</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Attendance Register: {{ $class->name }}</h2>
            <p class="text-muted mb-0">Date: <strong>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</strong> • Section: <strong>{{ $class->section }}</strong> • Room: <strong>{{ $class->room_number ?? '101' }}</strong></p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(isset($calendarCheck) && !$calendarCheck['is_instructional'])
                <span class="badge bg-danger px-3 py-2 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Non-Instructional / Holiday</span>
            @else
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-semibold"><i class="fa-solid fa-circle-check me-1"></i> Valid Instructional Day</span>
            @endif

            @if(!isset($calendarCheck) || $calendarCheck['is_instructional'] || !($calendarCheck['holiday'] && $calendarCheck['holiday']->attendance_policy === 'closed_no_register'))
            <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-bold" onclick="markAll('present')">
                <i class="fa-solid fa-check-double me-1"></i> Mark All Present
            </button>
            @endif
        </div>
    </div>
</div>

@if(isset($calendarCheck) && !$calendarCheck['is_instructional'])
<div class="alert alert-danger rounded-4 shadow-sm mb-4 border-danger">
    <div class="d-flex align-items-center gap-3">
        <div class="p-2 bg-danger text-white rounded-circle" style="width: 42px; height: 42px; display: grid; place-items: center;">
            <i class="fa-solid fa-calendar-xmark fs-5"></i>
        </div>
        <div>
            <strong class="d-block text-danger fs-6">Academic Calendar Observance: {{ $calendarCheck['reason'] }}</strong>
            <small class="text-muted">
                @if(isset($calendarCheck['holiday']) && $calendarCheck['holiday']->attendance_policy === 'closed_no_register')
                    Campus operations are officially suspended. New daily attendance registers cannot be recorded for this date to protect student attendance percentages.
                @else
                    This date is marked as an observance in the institutional calendar.
                @endif
            </small>
        </div>
    </div>
</div>
@endif

<div class="alert alert-warning rounded-4 shadow-sm mb-4">
    <div class="d-flex align-items-center gap-3">
        <i class="fa-solid fa-bell fs-3 text-warning"></i>
        <div>
            <strong class="d-block">Instant Parental Notification Policy:</strong>
            <small>Marking any student as <strong>Absent</strong> will automatically dispatch an immediate SMS & portal broadcast notice to their verified guardian phone record.</small>
        </div>
    </div>
</div>

<div class="portal-card">
    <form action="{{ route('attendance.mark.store') }}" method="POST">
        @csrf
        <input type="hidden" name="school_class_id" value="{{ $class->id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="table-responsive">
            <table class="table table-portal align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">Roll #</th>
                        <th>Student Name</th>
                        <th>Guardian Contact</th>
                        <th class="text-center" style="width: 320px;">Attendance Status</th>
                        <th>Remarks / Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($class->students as $s)
                        @php
                            $currentStatus = $existing[$s->id]->status ?? 'present';
                            $currentRemarks = $existing[$s->id]->remarks ?? '';
                        @endphp
                        <tr>
                            <td class="text-center"><span class="badge bg-light text-dark border">{{ $s->roll_number }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($s->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $s->user->name }}</strong>
                                        <small class="text-muted d-block">{{ $s->admission_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="d-block">{{ $s->guardian->user->name ?? 'None' }}</small>
                                <small class="text-muted">{{ $s->guardian->user->phone ?? 'Unlinked' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check status-present" name="attendances[{{ $s->id }}]" id="pres_{{ $s->id }}" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success btn-sm px-3" for="pres_{{ $s->id }}">Present</label>

                                    <input type="radio" class="btn-check" name="attendances[{{ $s->id }}]" id="late_{{ $s->id }}" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning btn-sm px-3" for="late_{{ $s->id }}">Late</label>

                                    <input type="radio" class="btn-check" name="attendances[{{ $s->id }}]" id="abs_{{ $s->id }}" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger btn-sm px-3" for="abs_{{ $s->id }}">Absent</label>

                                    <input type="radio" class="btn-check" name="attendances[{{ $s->id }}]" id="exc_{{ $s->id }}" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary btn-sm px-3" for="exc_{{ $s->id }}">Excused</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="remarks[{{ $s->id }}]" class="form-control form-control-sm" placeholder="Notes (optional)..." value="{{ $currentRemarks }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No students enrolled in this cohort.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">Cryptographic audit record will be generated upon submission.</span>
            @if(isset($calendarCheck) && !$calendarCheck['is_instructional'] && isset($calendarCheck['holiday']) && $calendarCheck['holiday']->attendance_policy === 'closed_no_register')
            <button type="button" class="btn btn-secondary rounded-pill px-5 fw-bold" disabled title="Register marking frozen during declared campus closure">
                <i class="fa-solid fa-lock me-2"></i> Register Frozen (Campus Closed)
            </button>
            @else
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save & Seal Attendance Register
            </button>
            @endif
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function markAll(status) {
        document.querySelectorAll('.status-' + status).forEach(radio => {
            radio.checked = true;
        });
    }
</script>
@endsection