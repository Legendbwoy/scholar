@extends('layouts.portal')

@section('title', 'Grading Sheet: ' . $assessment->title . ' — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('gradebook.index', ['class_id' => $assessment->school_class_id, 'term' => $assessment->term]) }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Assessments Matrix
    </a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-uppercase px-2.5 py-1">{{ $assessment->type }}</span>
                <span class="badge bg-light text-dark border">{{ $assessment->term }} &bull; {{ $assessment->academic_year }}</span>
                <span class="badge bg-warning text-dark border fw-bold">{{ $assessment->weightage }}% Weight</span>
            </div>
            <h2 class="fw-bold mb-0 text-dark">{{ $assessment->title }}</h2>
            <p class="text-muted mb-0">
                Cohort: <strong>{{ $assessment->schoolClass->name }} (Section {{ $assessment->schoolClass->section }})</strong> &bull; 
                Subject: <strong>{{ $assessment->subject->name }} ({{ $assessment->subject->code }})</strong> &bull; 
                Maximum Points: <strong>{{ $assessment->max_marks }} pts</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border rounded-pill px-3 small" onclick="fillMeanScores()">
                <i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i> Auto-Fill Sample Marks
            </button>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="submitGradeSheet()">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save & Seal Grades
            </button>
        </div>
    </div>
</div>

<!-- Interactive Grade Entry Matrix -->
<div class="portal-card shadow-sm border-0 overflow-hidden mb-4">
    <form id="gradeSheetForm" action="{{ route('gradebook.grades.batch', $assessment->id) }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-portal align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">Roll</th>
                        <th>Student Candidate</th>
                        <th class="text-center" style="width: 130px;">Admission #</th>
                        <th class="text-center" style="width: 170px;">Marks Scored (Max: {{ $assessment->max_marks }})</th>
                        <th class="text-center" style="width: 90px;">% Score</th>
                        <th class="text-center" style="width: 80px;">Grade</th>
                        <th>Instructor Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $existingGrade = $st->grades->first();
                            $score = $existingGrade ? $existingGrade->score : '';
                            $pct = ($existingGrade && $assessment->max_marks > 0) ? round(($existingGrade->score / $assessment->max_marks) * 100, 1) : 0;
                            $letter = $existingGrade ? $existingGrade->letterGrade() : '--';
                            $remarks = $existingGrade ? $existingGrade->remarks : '';
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-muted border fw-bold">{{ $st->roll_number ?? $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset($st->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle shadow-sm" style="width: 38px; height: 38px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $st->user->name }}</div>
                                        <small class="text-muted">{{ $st->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $st->admission_number }}</span>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           step="0.5" 
                                           min="0" 
                                           max="{{ $assessment->max_marks }}" 
                                           name="grades[{{ $st->id }}][score]" 
                                           value="{{ $score }}" 
                                           class="form-control fw-bold text-center border-primary-subtle grade-score-input" 
                                           data-student-id="{{ $st->id }}"
                                           data-max="{{ $assessment->max_marks }}"
                                           placeholder="0.0"
                                           oninput="recalculateRow(this)">
                                    <span class="input-group-text bg-light text-muted small">/ {{ $assessment->max_marks }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span id="pct_badge_{{ $st->id }}" class="badge bg-light text-dark border fw-bold">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td class="text-center">
                                <span id="letter_badge_{{ $st->id }}" class="badge bg-{{ in_array($letter, ['A+', 'A']) ? 'success' : (in_array($letter, ['B', 'C']) ? 'primary' : ($letter === 'D' ? 'warning text-dark' : 'danger')) }} rounded-pill px-2.5 py-1">
                                    {{ $letter }}
                                </span>
                            </td>
                            <td>
                                <input type="text" 
                                       name="grades[{{ $st->id }}][remarks]" 
                                       id="remarks_input_{{ $st->id }}"
                                       value="{{ $remarks }}" 
                                       class="form-control form-control-sm border-light" 
                                       placeholder="Optional assessment remark...">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-2 d-block mb-2"></i> No students enrolled in this cohort yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                <i class="fa-solid fa-shield-halved text-success me-1"></i> Changes will be verified and recorded in the cryptographic audit ledger.
            </div>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="submitGradeSheet()">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save & Seal Grades
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function computeLetter(pct) {
        if (pct >= 90) return 'A+';
        if (pct >= 80) return 'A';
        if (pct >= 70) return 'B';
        if (pct >= 60) return 'C';
        if (pct >= 50) return 'D';
        return 'F';
    }

    function getLetterBadgeClass(letter) {
        if (letter === 'A+' || letter === 'A') return 'badge bg-success rounded-pill px-2.5 py-1';
        if (letter === 'B' || letter === 'C') return 'badge bg-primary rounded-pill px-2.5 py-1';
        if (letter === 'D') return 'badge bg-warning text-dark rounded-pill px-2.5 py-1';
        return 'badge bg-danger rounded-pill px-2.5 py-1';
    }

    function recalculateRow(input) {
        const studentId = input.dataset.studentId;
        const max = parseFloat(input.dataset.max) || 100;
        const val = parseFloat(input.value);

        const pctBadge = document.getElementById('pct_badge_' + studentId);
        const letterBadge = document.getElementById('letter_badge_' + studentId);
        const remarksInput = document.getElementById('remarks_input_' + studentId);

        if (isNaN(val) || val < 0) {
            pctBadge.innerText = '0%';
            letterBadge.innerText = '--';
            letterBadge.className = 'badge bg-secondary rounded-pill px-2.5 py-1';
            return;
        }

        const pct = Math.min(100, Math.round((val / max) * 100 * 10) / 10);
        const letter = computeLetter(pct);

        pctBadge.innerText = pct + '%';
        letterBadge.innerText = letter;
        letterBadge.className = getLetterBadgeClass(letter);

        if (!remarksInput.value) {
            if (pct >= 90) remarksInput.value = 'Distinction honors';
            else if (pct >= 75) remarksInput.value = 'Commendable performance';
            else if (pct >= 60) remarksInput.value = 'Competent';
        }
    }

    function fillMeanScores() {
        const inputs = document.querySelectorAll('.grade-score-input');
        const max = {{ $assessment->max_marks }};
        
        inputs.forEach((input, index) => {
            const basePct = 0.86 - (index * 0.03);
            const score = Math.round(max * Math.max(0.65, basePct) * 10) / 10;
            input.value = score;
            recalculateRow(input);
        });

        Toast.fire({
            icon: 'info',
            title: 'Sample marks populated across all students.'
        });
    }

    function submitGradeSheet() {
        Swal.fire({
            title: 'Seal Assessment Grades?',
            text: 'Save and index all marks in the institutional gradebook with cryptographic audit checksum?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-floppy-disk me-1"></i> Confirm & Seal',
            confirmButtonColor: '#198754',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 fw-bold',
                cancelButton: 'rounded-pill px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sealing Grades...',
                    text: 'Computing student GPAs & signing ledger entry...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('gradeSheetForm').submit();
            }
        });
    }
</script>
@endsection
