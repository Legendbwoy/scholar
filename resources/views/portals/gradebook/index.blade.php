@extends('layouts.portal')

@section('title', 'Academic Gradebook & Assessments — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Academic Gradebook & Assessments</h2>
        <p class="text-muted mb-0">Continuous Evaluations, Grading Sheets & Term Academic Standards</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('gradebook.reports', ['class_id' => $selectedClassId, 'term' => $selectedTerm]) }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold shadow-sm">
            <i class="fa-solid fa-graduation-cap me-1"></i> Class Report Cards
        </a>
        <button type="button" class="btn btn-portal-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createAssessmentModal">
            <i class="fa-solid fa-plus me-1"></i> New Assessment
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0 d-flex align-items-center gap-3">
            <div class="p-3 rounded-4 bg-primary-subtle text-primary" style="width: 50px; height: 50px; display: grid; place-items: center;">
                <i class="fa-solid fa-file-signature fs-4"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Term Assessments</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalAssessments }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0 d-flex align-items-center gap-3">
            <div class="p-3 rounded-4 bg-success-subtle text-success" style="width: 50px; height: 50px; display: grid; place-items: center;">
                <i class="fa-solid fa-square-check fs-4"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Evaluated Marks</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalGradesCount }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0 d-flex align-items-center gap-3">
            <div class="p-3 rounded-4 bg-info-subtle text-info" style="width: 50px; height: 50px; display: grid; place-items: center;">
                <i class="fa-solid fa-users fs-4"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Class Cohort Size</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $classStudentsCount }} Students</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="portal-card p-3 shadow-sm border-0 d-flex align-items-center gap-3">
            <div class="p-3 rounded-4 bg-warning-subtle text-warning" style="width: 50px; height: 50px; display: grid; place-items: center;">
                <i class="fa-solid fa-award fs-4"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Term System</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $selectedTerm }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="portal-card p-3 mb-4 shadow-sm border-0">
    <form action="{{ route('gradebook.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small text-muted fw-bold mb-1">Academic Cohort / Class</label>
            <select name="class_id" class="form-select border-light shadow-sm" onchange="this.form.submit()">
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Section {{ $c->section }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold mb-1">Academic Term</label>
            <select name="term" class="form-select border-light shadow-sm" onchange="this.form.submit()">
                <option value="Term 1" {{ $selectedTerm === 'Term 1' ? 'selected' : '' }}>Term 1 (Fall)</option>
                <option value="Term 2" {{ $selectedTerm === 'Term 2' ? 'selected' : '' }}>Term 2 (Spring)</option>
                <option value="Term 3" {{ $selectedTerm === 'Term 3' ? 'selected' : '' }}>Term 3 (Summer)</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold mb-1">Filter by Subject</label>
            <select name="subject_id" class="form-select border-light shadow-sm" onchange="this.form.submit()">
                <option value="">All Curriculum Subjects</option>
                @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }} ({{ $sub->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="{{ route('gradebook.index') }}" class="btn btn-light border rounded-3 w-100" style="margin-top: 24px;">Reset Filter</a>
        </div>
    </form>
</div>

<!-- Assessments Matrix Grid -->
<div class="row g-3 mb-4">
    @forelse($assessments as $ass)
        @php
            $gradedCount = $ass->grades->count();
            $avg = $ass->averageScore();
            $avgPct = $ass->max_marks > 0 ? round(($avg / $ass->max_marks) * 100, 1) : 0;
            $typeColors = [
                'quiz' => 'info',
                'assignment' => 'primary',
                'midterm' => 'warning text-dark',
                'final' => 'danger'
            ];
            $typeBadge = $typeColors[$ass->type] ?? 'secondary';
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="portal-card h-100 shadow-sm border-0 d-flex flex-column justify-content-between p-4 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-{{ $typeBadge }} text-uppercase fw-bold px-2.5 py-1" style="font-size: 11px;">
                        {{ $ass->type }} &bull; {{ $ass->weightage }}% Weight
                    </span>
                    <span class="badge bg-light text-dark border small fw-semibold">Max: {{ $ass->max_marks }} pts</span>
                </div>

                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1">{{ $ass->title }}</h5>
                    <div class="text-primary small fw-semibold mb-1">
                        <i class="fa-solid fa-book-open me-1"></i> {{ $ass->subject->name ?? 'Subject' }} ({{ $ass->subject->code ?? '' }})
                    </div>
                    <small class="text-muted d-block">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Faculty: {{ $ass->teacher->user->name ?? 'Unassigned' }}
                    </small>
                </div>

                <!-- Grading Progress -->
                <div class="bg-light rounded-3 p-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center small mb-1">
                        <span class="text-muted">Class Submissions Graded:</span>
                        <strong class="text-dark">{{ $gradedCount }} / {{ $classStudentsCount }}</strong>
                    </div>
                    <div class="progress mb-2" style="height: 6px;">
                        @php $progressPct = $classStudentsCount > 0 ? min(100, round(($gradedCount / $classStudentsCount) * 100)) : 0; @endphp
                        <div class="progress-bar bg-success" style="width: {{ $progressPct }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span class="text-muted">Class Average:</span>
                        <span class="badge bg-white text-dark border fw-bold">{{ $avg }} pts ({{ $avgPct }}%)</span>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="d-flex gap-2 align-items-center pt-2 border-top">
                    <a href="{{ route('gradebook.sheet', $ass->id) }}" class="btn btn-primary rounded-pill px-3 py-1.5 fw-bold flex-grow-1 shadow-sm" style="background-color: #009961; border-color: #009961; font-size: 13px;">
                        <i class="fa-solid fa-table-list me-1"></i> Grade Sheet
                    </a>
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('gradebook.assessments.destroy', $ass->id) }}" method="POST" class="d-inline confirm-action"
                          data-confirm-title="Delete Assessment?"
                          data-confirm-text="Are you sure you want to permanently delete '{{ $ass->title }}' and all submitted grades?"
                          data-confirm-btn="Delete Assessment"
                          data-confirm-color="#dc3545">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger rounded-circle p-2" title="Delete Assessment" style="width: 36px; height: 36px; display: grid; place-items: center;">
                            <i class="fa-solid fa-trash" style="font-size: 12px;"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="portal-card p-5 text-center shadow-sm border-0 text-muted">
                <i class="fa-solid fa-file-circle-question fs-1 text-primary opacity-50 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">No Assessments Configured</h5>
                <p class="text-muted mb-3">No continuous assessment or term exams registered for this cohort and term yet.</p>
                <button type="button" class="btn btn-portal-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createAssessmentModal">
                    <i class="fa-solid fa-plus me-1"></i> Create First Assessment
                </button>
            </div>
        </div>
    @endforelse
</div>

@if($assessments->hasPages())
    <div class="p-3 border-top portal-card mb-4">
        {{ $assessments->links() }}
    </div>
@endif

<!-- Create Assessment Modal -->
<div class="modal fade" id="createAssessmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('gradebook.assessments.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-primary text-white shadow-sm" style="width: 44px; height: 44px; display: grid; place-items: center; background-color: #009961 !important;">
                            <i class="fa-solid fa-file-pen fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Create Academic Assessment</h5>
                            <small class="text-white-50">Continuous Assessment, Quiz, Midterm or Final Examination</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-dark">Assessment Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control border-light shadow-sm" placeholder="e.g. Unit 3 Continuous Quiz or Term 1 Final Examination" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Evaluation Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select border-light shadow-sm" required>
                                <option value="quiz">Continuous Quiz</option>
                                <option value="assignment">Assignment / Project</option>
                                <option value="midterm">Midterm Examination</option>
                                <option value="final">Final Examination</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Class Cohort <span class="text-danger">*</span></label>
                            <select name="school_class_id" class="form-select border-light shadow-sm" required>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Section {{ $c->section }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select border-light shadow-sm" required>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Term <span class="text-danger">*</span></label>
                            <select name="term" class="form-select border-light shadow-sm" required>
                                <option value="Term 1" {{ $selectedTerm === 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                <option value="Term 2" {{ $selectedTerm === 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                <option value="Term 3" {{ $selectedTerm === 'Term 3' ? 'selected' : '' }}>Term 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year" class="form-control border-light shadow-sm" value="{{ \App\Models\AcademicYear::current()?->name ?? (date('Y') . '-' . (date('Y') + 1)) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Maximum Points <span class="text-danger">*</span></label>
                            <input type="number" name="max_marks" class="form-control border-light shadow-sm" value="100" min="1" max="500" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Weightage (%) <span class="text-danger">*</span></label>
                            <input type="number" name="weightage" class="form-control border-light shadow-sm" value="30" min="1" max="100" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Exam / Submission Date</label>
                            <input type="date" name="date" class="form-control border-light shadow-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Description & Instructions</label>
                            <input type="text" name="description" class="form-control border-light shadow-sm" placeholder="Curriculum units evaluated, open-book or supervised exam...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: #009961; border-color: #009961;" onclick="Swal.fire({title: 'Creating Assessment...', text: 'Initialising grade sheet in cryptographic ledger...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});">
                        <i class="fa-solid fa-check me-1"></i> Create Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
