@extends('layouts.portal')

@section('title', 'Rubric Evaluation & Grading — Attempt #' . $attempt->id)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.submissions', $attempt->quiz_id) }}" class="text-decoration-none">Submissions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rubric Grading</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-file-signature text-warning me-2"></i>Assessment Rubric Review Console</h2>
            <p class="text-muted mb-0">Grade open-ended essay questions, modify automated points, and provide bespoke teacher commentary.</p>
        </div>
        <a href="{{ route('lms.quizzes.submissions', $attempt->quiz_id) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Submissions
        </a>
    </div>
</div>

<!-- Candidate Summary Card -->
<div class="portal-card p-4 mb-4 bg-light border-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 52px; height: 52px;">
                {{ strtoupper(substr($attempt->student->user->name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark">{{ $attempt->student->user->name ?? 'Student Candidate' }}</h4>
                <span class="text-muted small">
                    Reg: <strong>{{ $attempt->student->admission_number ?? 'REG-N/A' }}</strong> • 
                    Cohort: <strong>{{ $attempt->student->schoolClass->name ?? 'General' }}</strong> • 
                    Attempt: <strong>#{{ $attempt->attempt_number }}</strong>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <span class="text-muted small text-uppercase fw-bold d-block">Current Score</span>
                <h3 class="fw-bold mb-0 text-success">{{ $attempt->score }} <span class="fs-6 text-muted">/ {{ $attempt->quiz->total_points }} pts</span></h3>
            </div>
            <div class="text-end">
                <span class="text-muted small text-uppercase fw-bold d-block">Status</span>
                <span class="badge {{ $attempt->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 text-uppercase fw-bold">
                    {{ $attempt->status === 'completed' ? 'Graded' : 'Needs Review' }}
                </span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('lms.quizzes.grading.submit', $attempt->id) }}" method="POST">
    @csrf

    @foreach($attempt->answers as $index => $ans)
        @php
            $q = $ans->question;
            if (!$q) continue;
            $isEssay = ($q->question_type === 'essay');
        @endphp
        <div class="portal-card p-4 p-md-5 mb-4 border-start border-4 {{ $isEssay ? 'border-warning' : ($ans->is_correct ? 'border-success' : 'border-danger') }}">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-pill px-3 py-2">Question #{{ $index + 1 }}</span>
                    <span class="badge bg-light text-muted border text-uppercase">{{ str_replace('_', ' ', $q->question_type) }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="small fw-bold text-muted text-uppercase mb-0">Award Marks:</label>
                    <div class="input-group" style="width: 140px;">
                        <input type="number" step="0.5" min="0" max="{{ $q->points }}" 
                               name="points[{{ $ans->id }}]" class="form-control fw-bold text-center" 
                               value="{{ old('points.' . $ans->id, $ans->awarded_points) }}" required>
                        <span class="input-group-text bg-light text-muted">/ {{ $q->points }}</span>
                    </div>
                </div>
            </div>

            <!-- Question Prompt -->
            <h5 class="fw-bold text-dark mb-3">{{ $q->question_text }}</h5>

            <!-- Student Response -->
            <div class="p-3 bg-light rounded-3 border mb-3">
                <span class="fw-bold text-muted small text-uppercase d-block mb-1">Candidate's Submitted Answer:</span>
                @if($q->question_type === 'essay')
                    <div class="p-3 bg-white rounded-3 border text-dark" style="white-space: pre-wrap; min-height: 80px;">{{ $ans->text_answer ?? 'Left blank.' }}</div>
                @elseif($q->question_type === 'short_answer')
                    <span class="fw-bold {{ $ans->is_correct ? 'text-success' : 'text-danger' }} fs-5">
                        {{ $ans->text_answer ?? 'Left blank' }}
                    </span>
                @elseif($q->question_type === 'mcq_single' || $q->question_type === 'true_false')
                    <span class="fw-bold {{ $ans->is_correct ? 'text-success' : 'text-danger' }} fs-5">
                        <i class="fa-solid {{ $ans->is_correct ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                        {{ $ans->selectedOption->option_text ?? 'No option selected' }}
                    </span>
                @elseif($q->question_type === 'mcq_multi')
                    @php
                        $selectedTexts = !empty($ans->selected_options) ? \App\Models\QuizQuestionOption::whereIn('id', (array)$ans->selected_options)->pluck('option_text')->toArray() : [];
                    @endphp
                    <span class="fw-bold {{ $ans->is_correct ? 'text-success' : 'text-danger' }}">
                        {{ !empty($selectedTexts) ? implode(', ', $selectedTexts) : 'No options checked' }}
                    </span>
                @endif
            </div>

            <!-- Model Explanation / Rubric Key -->
            @if($q->explanation)
                <div class="p-3 bg-white rounded-3 border-start border-3 border-info small text-muted mb-3">
                    <strong class="text-dark"><i class="fa-regular fa-lightbulb text-warning me-1"></i> Model Answer / Scoring Rubric:</strong>
                    {{ $q->explanation }}
                </div>
            @endif

            <!-- Teacher Feedback Input -->
            <div>
                <label class="form-label small fw-bold text-uppercase text-muted">Instructor Feedback for Candidate</label>
                <input type="text" name="feedback[{{ $ans->id }}]" class="form-control" 
                       placeholder="e.g. Well articulated argument, strong reasoning on asynchronous event loops." 
                       value="{{ old('feedback.' . $ans->id, $ans->teacher_feedback) }}">
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-end gap-2 p-4 portal-card position-sticky bottom-0 bg-white shadow-lg border-top" style="z-index: 100;">
        <a href="{{ route('lms.quizzes.submissions', $attempt->quiz_id) }}" class="btn btn-light px-4">Cancel</a>
        <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm">
            <i class="fa-solid fa-check-double me-1"></i> Finalize Marks & Publish to Gradebook
        </button>
    </div>
</form>
@endsection
