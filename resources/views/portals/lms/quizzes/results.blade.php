@extends('layouts.portal')

@section('title', 'Assessment Performance Report — ' . $quiz->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $quiz->course_id) }}" class="text-decoration-none">{{ $quiz->course->code ?? 'Course' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments</a></li>
            <li class="breadcrumb-item active" aria-current="page">Session Report</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i>Examination Performance Card</h2>
            <p class="text-muted mb-0">Official evaluation record for <strong>{{ $quiz->title }}</strong>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('courses.show', $quiz->course_id) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Course
            </a>
            @if(auth()->user()->isStudent() && $attempt->attempt_number < $quiz->max_attempts)
                <form action="{{ route('lms.quizzes.start', $quiz->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-rotate-right me-1"></i> Retake Assessment (Attempt {{ $attempt->attempt_number + 1 }} of {{ $quiz->max_attempts }})
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Score Banner Card -->
@php
    $isPassed = $attempt->percentage >= (($quiz->pass_points / max(1, $quiz->total_points)) * 100);
    $isPendingEssayGrading = ($attempt->status === 'submitted');
@endphp

<div class="portal-card p-4 p-md-5 mb-4 text-center position-relative overflow-hidden" 
     style="background: linear-gradient(135deg, {{ $isPassed ? '#064e3b' : '#7f1d1d' }} 0%, #0f172a 100%); color: #fff;">
    <div class="position-relative z-1">
        <span class="badge {{ $isPassed ? 'bg-success' : 'bg-danger' }} px-3 py-2 text-uppercase fw-bold mb-3 fs-6">
            <i class="fa-solid {{ $isPassed ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1"></i>
            {{ $isPendingEssayGrading ? 'Submitted — Pending Essay Review' : ($isPassed ? 'Assessment Passed' : 'Needs Improvement') }}
        </span>

        <h1 class="display-3 fw-bold mb-2">
            {{ $attempt->percentage }}<span class="fs-2">%</span>
        </h1>
        <h4 class="fw-semibold text-white-50 mb-4">
            Earned <strong>{{ $attempt->score }}</strong> out of <strong>{{ $quiz->total_points }}</strong> points
            (Passing Mark: {{ $quiz->pass_points }} pts)
        </h4>

        <!-- Session Timing Metadata -->
        <div class="d-inline-flex flex-wrap gap-4 p-3 bg-white bg-opacity-10 rounded-pill small text-white-50">
            <div>
                <i class="fa-solid fa-user me-1 text-white"></i> Candidate: <strong class="text-white">{{ $attempt->student->user->name ?? 'Student' }}</strong>
            </div>
            <div>
                <i class="fa-solid fa-repeat me-1 text-white"></i> Attempt: <strong class="text-white">{{ $attempt->attempt_number }} of {{ $quiz->max_attempts }}</strong>
            </div>
            <div>
                <i class="fa-solid fa-calendar-check me-1 text-white"></i> Submitted: <strong class="text-white">{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y h:i A') : 'N/A' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Pending Essay Alert if applicable -->
@if($isPendingEssayGrading)
    <div class="alert alert-warning border-0 shadow-sm p-4 rounded-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-hourglass-half fs-2 text-warning"></i>
            <div>
                <h5 class="fw-bold mb-1">Subjective Questions Awaiting Teacher Scoring</h5>
                <p class="mb-0 small">This assessment includes open-ended essay questions. Your final cumulative score and terminal gradebook entry will update once your faculty instructor evaluates your submissions.</p>
            </div>
        </div>
    </div>
@endif

<!-- Detailed Question Breakdown -->
<div class="portal-card p-4 p-md-5 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-clipboard-check text-primary me-2"></i>Question Evaluation Breakdown
        </h4>
        @if(!$quiz->show_correct_answers)
            <span class="badge bg-secondary px-3 py-2"><i class="fa-solid fa-lock me-1"></i> Solution explanations locked by instructor</span>
        @else
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2"><i class="fa-solid fa-unlock me-1"></i> Solution review active</span>
        @endif
    </div>

    @foreach($quiz->questions as $index => $q)
        @php
            $ans = $attempt->answers->firstWhere('quiz_question_id', $q->id);
            $awarded = $ans ? $ans->awarded_points : 0;
            $isCorrect = $ans ? $ans->is_correct : false;
        @endphp
        <div class="p-4 rounded-3 border mb-3 {{ $isCorrect ? 'border-success bg-success bg-opacity-10' : ($q->question_type === 'essay' ? 'border-warning bg-warning bg-opacity-10' : 'border-danger bg-danger bg-opacity-10') }}">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-pill px-3 py-2">Question #{{ $index + 1 }}</span>
                    <span class="badge bg-light text-muted border text-uppercase">{{ str_replace('_', ' ', $q->question_type) }}</span>
                </div>
                <div>
                    <span class="badge {{ $isCorrect ? 'bg-success' : ($q->question_type === 'essay' ? 'bg-warning text-dark' : 'bg-danger') }} px-3 py-2 fw-bold">
                        {{ $awarded }} / {{ $q->points }} Marks
                    </span>
                </div>
            </div>

            <!-- Prompt -->
            <h5 class="fw-bold text-dark mb-3">{{ $q->question_text }}</h5>

            <!-- Student's Given Answer -->
            <div class="p-3 bg-white rounded-3 border mb-3 small">
                <span class="text-muted fw-bold text-uppercase d-block mb-1">Your Submitted Response:</span>
                @if($q->question_type === 'mcq_single' || $q->question_type === 'true_false')
                    @php
                        $selected = $ans && $ans->selectedOption ? $ans->selectedOption->option_text : 'No option selected';
                    @endphp
                    <span class="fw-semibold {{ $isCorrect ? 'text-success' : 'text-danger' }}">
                        <i class="fa-solid {{ $isCorrect ? 'fa-check' : 'fa-xmark' }} me-1"></i>{{ $selected }}
                    </span>
                @elseif($q->question_type === 'mcq_multi')
                    @if($ans && !empty($ans->selected_options))
                        @php
                            $selectedTexts = \App\Models\QuizQuestionOption::whereIn('id', (array)$ans->selected_options)->pluck('option_text')->toArray();
                        @endphp
                        <span class="fw-semibold {{ $isCorrect ? 'text-success' : 'text-danger' }}">
                            {{ implode(', ', $selectedTexts) }}
                        </span>
                    @else
                        <span class="text-muted fst-italic">No choices checked</span>
                    @endif
                @elseif($q->question_type === 'short_answer')
                    <span class="fw-semibold {{ $isCorrect ? 'text-success' : 'text-danger' }}">
                        {{ $ans && $ans->text_answer ? $ans->text_answer : 'Left blank' }}
                    </span>
                @elseif($q->question_type === 'essay')
                    <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $ans && $ans->text_answer ? $ans->text_answer : 'Left blank' }}</p>
                @endif
            </div>

            <!-- Solution Feedback if enabled -->
            @if($quiz->show_correct_answers)
                @if($q->explanation)
                    <div class="p-3 bg-white rounded-3 border-start border-3 border-primary small text-muted">
                        <strong class="text-dark"><i class="fa-regular fa-lightbulb text-warning me-1"></i> Solution Explanation:</strong>
                        {{ $q->explanation }}
                    </div>
                @endif
            @endif

            <!-- Teacher Feedback on Essay -->
            @if($ans && $ans->teacher_feedback)
                <div class="p-3 bg-white rounded-3 border-start border-3 border-success small text-muted mt-2">
                    <strong class="text-success"><i class="fa-solid fa-comment-dots me-1"></i> Instructor Feedback:</strong>
                    {{ $ans->teacher_feedback }}
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
