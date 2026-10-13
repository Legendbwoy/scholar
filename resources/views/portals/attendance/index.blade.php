@extends('layouts.portal')

@section('title', 'Attendance Management — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Attendance Management & Records</h2>
        <p class="text-muted mb-0">Daily Register Tracking, Absence Alerts & Biometric/QR Verifications</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.qr') }}" class="btn btn-warning rounded-pill px-4 fw-bold">
            <i class="fa-solid fa-qrcode me-1"></i> QR Terminal Scanner
        </a>
    </div>
</div>

<!-- Date Picker & KPI Bar -->
<div class="portal-card p-4 mb-4">
    <div class="row align-items-center g-3">
        <div class="col-md-4">
            <form action="{{ route('attendance.index') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                <button type="submit" class="btn btn-primary rounded-3 px-3">Filter</button>
            </form>
        </div>
        <div class="col-md-8">
            <div class="row g-2 text-center">
                <div class="col-3">
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <small class="d-block fw-semibold">Present</small>
                        <h4 class="fw-bold mb-0">{{ $stats['present'] }}</h4>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 bg-danger-subtle text-danger rounded-3">
                        <small class="d-block fw-semibold">Absent</small>
                        <h4 class="fw-bold mb-0">{{ $stats['absent'] }}</h4>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 bg-warning-subtle text-warning rounded-3">
                        <small class="d-block fw-semibold">Late</small>
                        <h4 class="fw-bold mb-0">{{ $stats['late'] }}</h4>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 bg-info-subtle text-info rounded-3">
                        <small class="d-block fw-semibold">Excused</small>
                        <h4 class="fw-bold mb-0">{{ $stats['excused'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cohorts Attendance Grid -->
<div class="row g-4">
    @foreach($classes as $c)
        <div class="col-lg-4 col-md-6">
            <div class="portal-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-primary border mb-1">Grade {{ $c->numeric_grade }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $c->name }}</h4>
                        <small class="text-muted">Section {{ $c->section }}</small>
                    </div>
                    <span class="badge bg-primary fs-6">{{ $classAttendance[$c->id]['present'] ?? 0 }} / {{ $c->students_count }} Present</span>
                </div>

                <div class="p-3 bg-light rounded-3 my-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Attendance Standing for {{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                        <strong>{{ $c->students_count > 0 ? round((($classAttendance[$c->id]['present'] ?? 0) / $c->students_count) * 100) : 0 }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $c->students_count > 0 ? (($classAttendance[$c->id]['present'] ?? 0) / $c->students_count) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('attendance.mark', ['classId' => $c->id, 'date' => $date]) }}" class="btn btn-portal-primary rounded-pill w-100 fw-bold">
                        <i class="fa-solid fa-clipboard-user me-1"></i> Open & Seal Register
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection