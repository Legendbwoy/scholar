@extends('layouts.portal')

@section('title', 'Course Dual-Grading Scheme & Assessment Policies — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-scale-balanced text-success me-2"></i>Course Dual-Grading & Assessment Schemes</h2>
        <p class="text-muted mb-0">Multi-Channel Ingestion Policies: Balance Digital LMS Activities vs. Offline In-Person Proctored Courseworks</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('academic.calendar') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-calendar-days me-1"></i> Academic Calendar
        </a>
        <a href="{{ route('gradebook.index') }}" class="btn btn-portal-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-table-list me-1"></i> Gradebook Desk
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Dual / Blended Courses</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-success">{{ $courses->where('grading_mode', 'dual')->count() }} Courses</h4>
                <span class="badge bg-success-subtle text-success border border-success">Blended</span>
            </div>
            <small class="text-muted mt-2 d-block">Synchronized LMS + Offline</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Standard LMS Weight</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-primary">30% Digital</h4>
                <span class="badge bg-light text-dark border">Quizzes & Homework</span>
            </div>
            <small class="text-muted mt-2 d-block">Continuous evaluation</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">Standard Offline Weight</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">70% In-Person</h4>
                <span class="badge bg-light text-dark border">Midterms & Finals</span>
            </div>
            <small class="text-muted mt-2 d-block">Invigilated proctored exams</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0">
            <small class="text-muted fw-bold d-block mb-1">GPA Conversion Standard</small>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-warning">4.0 Scale</h4>
                <span class="badge bg-warning-subtle text-dark border">A+ to F</span>
            </div>
            <small class="text-muted mt-2 d-block">Cryptographically sealed</small>
        </div>
    </div>
</div>

<!-- Grading Policies Table -->
<div class="portal-card shadow-sm border-0 overflow-hidden">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0">Curriculum Course Assessment Distribution</h5>
            <small class="text-muted">Configure weightage allocation per course to determine how terminal report card composite marks are computed.</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="fa-solid fa-calculator text-primary me-1"></i> Constraint: LMS% + Offline% = 100%
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Course Title & Code</th>
                    <th>Cohort / Class</th>
                    <th>Faculty Instructor</th>
                    <th class="text-center">Ingestion Scheme</th>
                    <th>Component Weights Split</th>
                    <th class="text-end">Configure Policy</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $c)
                    @php
                        $lms = $c->lms_weight ?? 30;
                        $off = $c->offline_weight ?? 70;
                        $mode = $c->grading_mode ?? 'dual';
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $c->title }}</div>
                            <span class="badge bg-light text-dark border font-monospace">{{ $c->code }}</span>
                            <span class="badge bg-light text-secondary border small">{{ $c->category }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $c->schoolClass->name ?? 'General Cohort' }}</div>
                            <small class="text-muted">Section {{ $c->schoolClass->section ?? 'A' }}</small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $c->teacher->user->name ?? 'Unassigned' }}</div>
                            <small class="text-muted">{{ $c->teacher->designation ?? 'Faculty' }}</small>
                        </td>
                        <td class="text-center">
                            @if($mode === 'dual')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold">
                                    <i class="fa-solid fa-code-branch me-1"></i> Dual / Blended
                                </span>
                            @elseif($mode === 'offline_only')
                                <span class="badge bg-secondary-subtle text-dark border px-3 py-1.5 rounded-pill fw-bold">
                                    <i class="fa-solid fa-building-columns me-1"></i> Offline Only
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold">
                                    <i class="fa-solid fa-laptop-code me-1"></i> LMS Only
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1" style="min-width: 220px;">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $lms }}%;" title="LMS Online: {{ $lms }}%"></div>
                                    <div class="progress-bar bg-success" style="width: {{ $off }}%;" title="Offline Coursework: {{ $off }}%"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small" style="font-size: 11px;">
                                <span class="text-primary fw-bold"><i class="fa-solid fa-laptop me-1"></i>LMS: {{ $lms }}%</span>
                                <span class="text-success fw-bold"><i class="fa-solid fa-file-pen me-1"></i>Offline: {{ $off }}%</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#policyModal_{{ $c->id }}">
                                <i class="fa-solid fa-sliders me-1"></i> Edit Policy
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Policy Modal -->
                    <div class="modal fade" id="policyModal_{{ $c->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                <form action="{{ route('academic.grading-policies.update', $c->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                                        <h5 class="modal-title fw-bold">Configure Grading Policy: {{ $c->title }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Assessment Delivery & Ingestion Mode</label>
                                            <select name="grading_mode" id="mode_{{ $c->id }}" class="form-select" onchange="toggleWeights({{ $c->id }})" required>
                                                <option value="dual" {{ $mode === 'dual' ? 'selected' : '' }}>Dual / Blended (LMS Activities + Offline Exams)</option>
                                                <option value="offline_only" {{ $mode === 'offline_only' ? 'selected' : '' }}>Offline Coursework Only (100% Proctored Paper Exams)</option>
                                                <option value="lms_only" {{ $mode === 'lms_only' ? 'selected' : '' }}>LMS Digital Only (100% Online Assessments)</option>
                                            </select>
                                        </div>

                                        <div id="weightsRow_{{ $c->id }}" class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">LMS Digital Weight (%)</label>
                                                <div class="input-group">
                                                    <input type="number" name="lms_weight" id="lmsInput_{{ $c->id }}" class="form-control" value="{{ $lms }}" min="0" max="100" oninput="syncOfflineWeight({{ $c->id }})">
                                                    <span class="input-group-text bg-light">%</span>
                                                </div>
                                                <small class="text-muted d-block mt-1">Quizzes, digital tasks</small>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Offline Proctored Weight (%)</label>
                                                <div class="input-group">
                                                    <input type="number" name="offline_weight" id="offInput_{{ $c->id }}" class="form-control" value="{{ $off }}" min="0" max="100" readonly>
                                                    <span class="input-group-text bg-light">%</span>
                                                </div>
                                                <small class="text-muted d-block mt-1">Midterms, final papers</small>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-light rounded-3 border small text-muted">
                                            <i class="fa-solid fa-circle-check text-success me-1"></i>
                                            <strong>Automatic Normalization:</strong> Terminal report cards will evaluate LMS and Offline grade entries independently and calculate the final composite grade using these configured ratios.
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 bg-light">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-portal-primary rounded-pill px-4">Update Scheme</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No courses configured yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleWeights(id) {
        const modeSelect = document.getElementById('mode_' + id);
        const lmsInput = document.getElementById('lmsInput_' + id);
        const offInput = document.getElementById('offInput_' + id);
        const weightsRow = document.getElementById('weightsRow_' + id);

        if (modeSelect.value === 'offline_only') {
            lmsInput.value = 0;
            offInput.value = 100;
            lmsInput.readOnly = true;
        } else if (modeSelect.value === 'lms_only') {
            lmsInput.value = 100;
            offInput.value = 0;
            lmsInput.readOnly = true;
        } else {
            lmsInput.readOnly = false;
            if (lmsInput.value == 0 || lmsInput.value == 100) {
                lmsInput.value = 30;
                offInput.value = 70;
            }
        }
    }

    function syncOfflineWeight(id) {
        const lmsInput = document.getElementById('lmsInput_' + id);
        const offInput = document.getElementById('offInput_' + id);
        let val = parseInt(lmsInput.value) || 0;
        if (val < 0) val = 0;
        if (val > 100) val = 100;
        lmsInput.value = val;
        offInput.value = 100 - val;
    }
</script>
@endsection
