@extends('layouts.portal')

@section('title', 'Academic Calendar & Holiday Engine — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-calendar-days text-primary me-2"></i>Academic Calendar & Holiday Engine</h2>
        <p class="text-muted mb-0">Multi-Tier Term Periods, Statutory National Observances, Regional Breaks & Precision Instructional Auditing</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('academic.grading-policies') }}" class="btn btn-outline-success rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-scale-balanced me-1"></i> Dual-Grading Policies
        </a>
        <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#createTermModal">
            <i class="fa-solid fa-plus me-1"></i> Add / Edit Term
        </button>
        <button type="button" class="btn btn-portal-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createHolidayModal">
            <i class="fa-solid fa-calendar-plus me-1"></i> Declare Holiday / Break
        </button>
    </div>
</div>

<!-- Academic Year Selector & Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="portal-card p-3 h-100 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Active Academic Year</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">{{ $selectedYear->name ?? '2026-2027' }}</h4>
                <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1">Active</span>
            </div>
            <small class="text-muted mt-2 d-block">
                {{ $selectedYear ? $selectedYear->start_date->format('M d, Y') : 'Aug 24, 2026' }} &ndash; {{ $selectedYear ? $selectedYear->end_date->format('M d, Y') : 'Jun 18, 2027' }}
            </small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="portal-card p-3 h-100 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Academic Terms</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-primary">{{ $terms->count() }} Terms</h4>
                <span class="badge bg-light text-dark border">{{ $terms->where('is_current', true)->first()->name ?? 'Term 1' }}</span>
            </div>
            <small class="text-muted mt-2 d-block">Multi-Semester continuous cycles</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="portal-card p-3 h-100 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Scheduled Holidays & Recesses</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-danger">{{ $holidays->count() }} Observances</h4>
                <span class="badge bg-danger-subtle text-danger px-2.5 py-1">Verified</span>
            </div>
            <small class="text-muted mt-2 d-block">National, Regional & Institutional</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="portal-card p-3 h-100 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Weekly Operating Schedule</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">{{ $schedules->where('is_instructional', true)->count() }} Days / Wk</h4>
                <span class="badge bg-info-subtle text-info border border-info-subtle">Mon &ndash; Fri</span>
            </div>
            <small class="text-muted mt-2 d-block">08:00 AM &ndash; 03:30 PM</small>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-pills mb-4 gap-2" id="calendarTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-4 fw-bold" id="terms-tab" data-bs-toggle="pill" data-bs-target="#terms-pane" type="button" role="tab">
            <i class="fa-solid fa-timeline me-1"></i> Term Periods & Instructional Days
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 fw-bold" id="holidays-tab" data-bs-toggle="pill" data-bs-target="#holidays-pane" type="button" role="tab">
            <i class="fa-solid fa-umbrella-beach me-1"></i> Holidays & Declared Breaks ({{ $holidays->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 fw-bold" id="schedule-tab" data-bs-toggle="pill" data-bs-target="#schedule-pane" type="button" role="tab">
            <i class="fa-solid fa-business-time me-1"></i> Weekly Instructional Schedule
        </button>
    </li>
</ul>

<div class="tab-content" id="calendarTabsContent">
    <!-- Tab 1: Term Periods & Instructional Targets -->
    <div class="tab-pane fade show active" id="terms-pane" role="tabpanel">
        <div class="row g-4">
            @forelse($terms as $term)
                @php
                    $stats = $termStats[$term->id] ?? ['days_to_date' => 0, 'total_projected' => $term->instructional_days_target, 'progress_pct' => 0];
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="portal-card p-4 h-100 border-top border-4 {{ $term->is_current ? 'border-primary' : 'border-secondary' }} d-flex flex-column justify-content-between shadow-sm">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-dark border">{{ $term->term_code }}</span>
                                @if($term->is_current)
                                    <span class="badge bg-success rounded-pill px-3 py-1"><i class="fa-solid fa-circle-dot me-1"></i> Current Active Term</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-capitalize">{{ $term->status }}</span>
                                @endif
                            </div>
                            <h4 class="fw-bold text-dark mb-1">{{ $term->name }}</h4>
                            <p class="text-muted small mb-3">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $term->start_date->format('M d, Y') }} &ndash; {{ $term->end_date->format('M d, Y') }}
                            </p>

                            <div class="bg-light rounded-3 p-3 mb-3 border">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-muted">Instructional Days Completed:</span>
                                    <strong class="text-dark">{{ $stats['days_to_date'] }} / {{ $stats['total_projected'] }} Days</strong>
                                </div>
                                <div class="progress mb-2" style="height: 7px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $stats['progress_pct'] }}%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span>Target Curriculum Cap:</span>
                                    <span class="fw-bold">{{ $term->instructional_days_target }} Days</span>
                                </div>
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="fa-solid fa-file-signature text-warning me-1"></i> Grade Seal Cutoff: 
                                <strong class="text-dark">{{ $term->grade_submission_deadline ? $term->grade_submission_deadline->format('M d, Y') : 'Not fixed' }}</strong>
                            </div>
                        </div>

                        <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                            <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                Verified by Calendar Engine
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editTermModal_{{ $term->id }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Dates
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit Term Modal -->
                <div class="modal fade" id="editTermModal_{{ $term->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                            <form action="{{ route('academic.calendar.terms.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="academic_year_id" value="{{ $term->academic_year_id }}">
                                <input type="hidden" name="term_code" value="{{ $term->term_code }}">
                                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                                    <h5 class="modal-title fw-bold">Edit Academic Term: {{ $term->name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Term Display Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $term->name }}" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Start Date</label>
                                            <input type="date" name="start_date" class="form-control" value="{{ $term->start_date->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">End Date</label>
                                            <input type="date" name="end_date" class="form-control" value="{{ $term->end_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Instructional Target Days</label>
                                            <input type="number" name="instructional_days_target" class="form-control" value="{{ $term->instructional_days_target }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Grade Deadline</label>
                                            <input type="date" name="grade_submission_deadline" class="form-control" value="{{ $term->grade_submission_deadline ? $term->grade_submission_deadline->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Term Status</label>
                                        <select name="status" class="form-select">
                                            <option value="planned" {{ $term->status === 'planned' ? 'selected' : '' }}>Planned / Upcoming</option>
                                            <option value="active" {{ $term->status === 'active' ? 'selected' : '' }}>Active Term</option>
                                            <option value="grading" {{ $term->status === 'grading' ? 'selected' : '' }}>Grading Cutoff Period</option>
                                            <option value="closed" {{ $term->status === 'closed' ? 'selected' : '' }}>Closed / Sealed</option>
                                        </select>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_current" id="is_current_{{ $term->id }}" value="1" {{ $term->is_current ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="is_current_{{ $term->id }}">Set as Institution Active Current Term</label>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 bg-light">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-portal-primary rounded-pill px-4">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="portal-card p-5 text-center text-muted">
                        <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block"></i> No academic terms configured yet.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tab 2: Calendar Holidays Table -->
    <div class="tab-pane fade" id="holidays-pane" role="tabpanel">
        <div class="portal-card shadow-sm border-0 overflow-hidden">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0">Recognized Institutional & Statutory Observances</h5>
                    <small class="text-muted">Dates where instruction is paused and daily registers are safeguarded against absence penalties.</small>
                </div>
                <button type="button" class="btn btn-sm btn-portal-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createHolidayModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Observance
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-portal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Observance Title & Reason</th>
                            <th class="text-center">Classification</th>
                            <th>Date / Duration</th>
                            <th class="text-center">Applies To</th>
                            <th class="text-center">Attendance Policy</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $h)
                            @php
                                $daysCount = \Carbon\Carbon::parse($h->start_date)->diffInDays(\Carbon\Carbon::parse($h->end_date)) + 1;
                                $typeColors = [
                                    'national_holiday' => 'danger',
                                    'regional_holiday' => 'warning text-dark',
                                    'institutional_holiday' => 'primary',
                                    'recess' => 'info',
                                    'emergency_closure' => 'dark',
                                ];
                                $badgeClass = $typeColors[$h->type] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $h->title }}</div>
                                    <small class="text-muted">{{ Str::limit($h->description, 80) }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badgeClass }} text-uppercase fw-semibold px-2.5 py-1" style="font-size: 11px;">
                                        {{ str_replace('_', ' ', $h->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold small text-dark">
                                        {{ $h->start_date->format('M d, Y') }}
                                        @if($h->start_date != $h->end_date)
                                            &ndash; {{ $h->end_date->format('M d, Y') }}
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $daysCount }} {{ Str::plural('day', $daysCount) }} closure</small>
                                </td>
                                <td class="text-center">
                                    @if(is_array($h->affects_cohorts) && in_array('all', $h->affects_cohorts))
                                        <span class="badge bg-light text-dark border">All Campus Cohorts</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Specific Cohorts</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($h->attendance_policy === 'closed_no_register')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                            <i class="fa-solid fa-lock me-1"></i> Register Frozen
                                        </span>
                                    @elseif($h->attendance_policy === 'excused_auto')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                            <i class="fa-solid fa-check-double me-1"></i> Auto-Excused
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border px-2.5 py-1">Special Session</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('academic.calendar.holidays.destroy', $h->id) }}" method="POST" class="d-inline confirm-action"
                                          data-confirm-title="Remove Holiday Observance?"
                                          data-confirm-text="Are you sure you want to delete '{{ $h->title }}'? Attendance registers will revert to normal scheduling for these dates."
                                          data-confirm-btn="Delete Observance"
                                          data-confirm-color="#dc3545">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Observance" style="width: 34px; height: 34px; display: inline-grid; place-items: center;">
                                            <i class="fa-solid fa-trash" style="font-size: 11px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-calendar-check fs-2 mb-2 d-block text-success"></i> No holidays scheduled. Standard instructional days apply across the academic year.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Weekly Instructional Operating Schedule -->
    <div class="tab-pane fade" id="schedule-pane" role="tabpanel">
        <div class="portal-card p-4 shadow-sm border-0">
            <div class="mb-4">
                <h5 class="fw-bold mb-1"><i class="fa-solid fa-clock text-primary me-2"></i>Weekly Instructional Operating Schedule</h5>
                <p class="text-muted small mb-0">Configure which days of the week are instructional vs weekend days. The attendance percentage engine will only count active instructional days as the denominator.</p>
            </div>

            <form action="{{ route('academic.calendar.schedules.update') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id ?? 1 }}">

                <div class="table-responsive">
                    <table class="table table-portal align-middle mb-4">
                        <thead>
                            <tr>
                                <th style="width: 160px;">Day of Week</th>
                                <th class="text-center" style="width: 180px;">Instructional Status</th>
                                <th>Campus Operating Hours</th>
                                <th>Schedule Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $standardDays = [
                                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
                                    4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'
                                ];
                            @endphp
                            @foreach($standardDays as $dNum => $dName)
                                @php
                                    $sched = $schedules->firstWhere('day_of_week', $dNum);
                                    $isInst = $sched ? $sched->is_instructional : ($dNum <= 5);
                                    $start = $sched ? substr($sched->start_time, 0, 5) : '08:00';
                                    $end = $sched ? substr($sched->end_time, 0, 5) : ($dNum == 5 ? '14:30' : '15:30');
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $dName }}</strong>
                                        <input type="hidden" name="days[{{ $dNum }}][name]" value="{{ $dName }}">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="days[{{ $dNum }}][is_instructional]" id="inst_{{ $dNum }}" value="1" {{ $isInst ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="inst_{{ $dNum }}">
                                                {{ $isInst ? 'Instructional' : 'Weekend / Off' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" style="max-width: 280px;">
                                            <input type="time" name="days[{{ $dNum }}][start_time]" class="form-control form-control-sm" value="{{ $start }}">
                                            <span class="text-muted">&ndash;</span>
                                            <input type="time" name="days[{{ $dNum }}][end_time]" class="form-control form-control-sm" value="{{ $end }}">
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if($isInst)
                                                Registers active; attendance marked into term calculation.
                                            @else
                                                Non-instructional; attendance registers paused.
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background-color: #009961; border-color: #009961;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Operating Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Holiday Modal -->
<div class="modal fade" id="createHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('academic.calendar.holidays.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id ?? 1 }}">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus me-2 text-warning"></i>Declare Academic Observance / Holiday</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Holiday / Observance Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. National Independence Day, Founder's Fall Recess" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Observance Classification</label>
                            <select name="type" class="form-select" required>
                                <option value="national_holiday">National Statutory Holiday</option>
                                <option value="regional_holiday">Regional / Municipal Holiday</option>
                                <option value="institutional_holiday">School-Declared Holiday / Founder's Day</option>
                                <option value="recess">Mid-Term or Semester Vacation Break</option>
                                <option value="emergency_closure">Emergency Campus Closure (Weather / Safety)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Attendance Register Policy</label>
                            <select name="attendance_policy" class="form-select" required>
                                <option value="closed_no_register">Campus Closed (Freeze Register Marking)</option>
                                <option value="excused_auto">Auto-Mark All Students as Excused / Holiday</option>
                                <option value="special_session">Voluntary / Special Session (Optional Register)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Official Description & Notes</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Administrative mandate, proclamation or details..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_recurring_yearly" id="recurring_holiday" value="1">
                        <label class="form-check-label small fw-bold" for="recurring_holiday">Recurs annually on these exact calendar dates</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-portal-primary rounded-pill px-4">Register Observance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Term Modal -->
<div class="modal fade" id="createTermModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('academic.calendar.terms.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id ?? 1 }}">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2 text-warning"></i>Configure Academic Term</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Term Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Term 1 (Fall)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Term Identifier Code</label>
                        <input type="text" name="term_code" class="form-control" placeholder="e.g. T1-2026" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Target Instructional Days</label>
                            <input type="number" name="instructional_days_target" class="form-control" value="85" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Grade Deadline</label>
                            <input type="date" name="grade_submission_deadline" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Initial Status</label>
                        <select name="status" class="form-select">
                            <option value="planned">Planned / Upcoming</option>
                            <option value="active">Active Term</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-portal-primary rounded-pill px-4">Create Term</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
