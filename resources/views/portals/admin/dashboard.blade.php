@extends('layouts.portal')

@section('title', 'Institutional Executive Dashboard — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Institutional Administration</h2>
        <p class="text-muted mb-0">Academic Overview, Student Attendance Metrics & Cryptographic System Audit</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('broadcasts.create') }}" class="btn btn-warning rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-bullhorn me-1"></i> Multi-Channel Broadcast
        </a>
        <a href="{{ route('students.create') }}" class="btn btn-portal-primary rounded-pill px-3">
            <i class="fa-solid fa-user-plus me-1"></i> Enroll Student
        </a>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Enrolled Students</p>
                <h3>{{ $stats['total_students'] }}</h3>
                <small class="text-success"><i class="fa-solid fa-arrow-trend-up"></i> Active cohorts</small>
            </div>
            <div class="icon-box bg-primary-subtle text-primary">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Certified Faculty</p>
                <h3>{{ $stats['total_staff'] }}</h3>
                <small class="text-muted">4 Academic Departments</small>
            </div>
            <div class="icon-box bg-success-subtle text-success">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Attendance Today</p>
                <h3>{{ $stats['attendance_rate_today'] }}%</h3>
                <small class="text-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ $stats['absent_today'] }} Absences</small>
            </div>
            <div class="icon-box bg-warning-subtle text-warning">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card">
            <div>
                <p>Immutable Audit Logs</p>
                <h3>{{ $stats['total_audits'] }}</h3>
                <small class="text-success"><i class="fa-solid fa-shield-halved"></i> 100% Sealed Ledger</small>
            </div>
            <div class="icon-box bg-info-subtle text-info">
                <i class="fa-solid fa-database"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Highlights Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="portal-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Cohort Capacity & Student Enrollment</h5>
                <a href="{{ route('academic.classes') }}" class="btn btn-sm btn-link text-decoration-none">Manage Classes</a>
            </div>
            <div class="table-responsive">
                <table class="table table-portal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Class & Section</th>
                            <th>Class Teacher</th>
                            <th class="text-center">Room</th>
                            <th>Enrollment</th>
                            <th class="text-end">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $c)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $c->name }}</div>
                                    <small class="text-muted">Section {{ $c->section }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold">{{ $c->classTeacher->user->name ?? 'Unassigned' }}</div>
                                    <small class="text-muted">{{ $c->classTeacher->designation ?? '' }}</small>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $c->room_number ?? 'TBD' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($c->students_count / max($c->capacity, 1)) * 100 }}%"></div>
                                        </div>
                                        <small class="fw-bold">{{ $c->students_count }}/{{ $c->capacity }}</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('attendance.mark', $c->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-clipboard-check me-1"></i> Register
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Today's Attendance Ratio</h5>
                <span class="badge bg-primary-subtle text-primary">{{ now()->format('M d, Y') }}</span>
            </div>
            <div style="height: 220px; position: relative;">
                <canvas id="attendanceDonutChart"></canvas>
            </div>
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between small mb-2">
                    <span><i class="fa-solid fa-circle text-success me-2"></i>Present / On-Time</span>
                    <strong>{{ $stats['attendance_rate_today'] }}%</strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span><i class="fa-solid fa-circle text-danger me-2"></i>Unexcused Absence</span>
                    <strong class="text-danger">{{ $stats['absent_today'] }} Student(s)</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Broadcasts & Audit Feed -->
<div class="row g-4">
    <!-- Recent Multi-Channel Broadcasts -->
    <div class="col-lg-6">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i>Recent Omni-Channel Broadcasts</h5>
                <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            @forelse($recentBroadcasts as $b)
                <div class="p-3 rounded-3 border mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0 text-dark">{{ $b->title }}</h6>
                        <span class="badge bg-{{ $b->priority === 'urgent' ? 'danger' : 'primary' }} text-uppercase" style="font-size: 10px;">{{ $b->priority }}</span>
                    </div>
                    <p class="text-muted small mb-2">{{ Str::limit($b->message, 120) }}</p>
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <div>
                            @foreach($b->channels ?? [] as $ch)
                                <span class="badge bg-white text-dark border me-1"><i class="fa-solid fa-check text-success me-1"></i>{{ ucfirst($ch) }}</span>
                            @endforeach
                        </div>
                        <small>{{ $b->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="text-muted text-center py-4">No recent broadcasts recorded.</div>
            @endforelse
        </div>
    </div>

    <!-- Cryptographic Audit Ledger -->
    <div class="col-lg-6">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Immutable SHA-256 Audit Trail</h5>
                <a href="{{ route('audit.index') }}" class="btn btn-sm btn-link text-decoration-none">Inspect Ledger</a>
            </div>
            <div class="timeline">
                @foreach($recentAudits as $log)
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                        <div class="p-2 rounded-circle bg-primary-subtle text-primary align-self-start" style="width: 34px; height: 34px; display: grid; place-items: center; font-size: 13px;">
                            <i class="fa-solid fa-shield"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark border small">{{ $log->action }}</span>
                                <small class="text-muted" style="font-size: 11px;">{{ $log->created_at->format('H:i:s') }}</small>
                            </div>
                            <div class="small fw-semibold text-dark mt-1">{{ $log->description }}</div>
                            <div class="text-truncate text-muted" style="font-size: 11px; font-family: monospace;">
                                Hash: {{ substr($log->record_hash ?? '', 0, 24) }}...
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('attendanceDonutChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present / On-Time', 'Absent'],
            datasets: [{
                data: [{{ $stats['attendance_rate_today'] }}, {{ 100 - $stats['attendance_rate_today'] }}],
                backgroundColor: ['#38a169', '#e53e3e'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '72%'
        }
    });
</script>
@endsection