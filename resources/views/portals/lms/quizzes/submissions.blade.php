@extends('layouts.portal')

@section('title', 'Candidate Submissions — ' . $quiz->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $quiz->course_id) }}" class="text-decoration-none">{{ $quiz->course->code ?? 'Course' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments</a></li>
            <li class="breadcrumb-item active" aria-current="page">Submissions</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark">{{ $quiz->course->code ?? 'LMS' }}</span>
                <span class="badge {{ $quiz->type_badge }} text-uppercase">{{ $quiz->type }}</span>
            </div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-users text-emerald me-2"></i>Candidate Submissions: {{ $quiz->title }}</h2>
            <p class="text-muted mb-0">Evaluate student attempt outcomes, inspect answer sheets, grade essay submissions, and commit marks to gradebook.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lms.quizzes.builder', $quiz->id) }}" class="btn btn-dark rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-sliders me-1"></i> Question Studio
            </a>
            <a href="{{ route('lms.quizzes.index') }}" class="btn btn-light border rounded-pill px-3 fw-semibold text-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Assessments
            </a>
        </div>
    </div>
</div>

<!-- Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Total Attempts</span>
                <h3 class="mb-0">{{ $totalSubmissions }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Graded / Completed</span>
                <h3 class="mb-0">{{ $completedCount }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Awaiting Essay Grading</span>
                <h3 class="mb-0">{{ $pendingGrading }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Cohort Average Score</span>
                <h3 class="mb-0">{{ round($avgScore, 1) }}%</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table -->
<div class="portal-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Candidate</th>
                    <th>Cohort / Class</th>
                    <th>Attempt</th>
                    <th>Started</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Marks / Score</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $att)
                    @php
                        $userObj = $att->student->user ?? null;
                        $isPassed = $att->percentage >= (($quiz->pass_points / max(1, $quiz->total_points)) * 100);
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($userObj->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $userObj->name ?? 'Candidate' }}</span>
                                    <small class="text-muted">{{ $att->student->admission_number ?? 'REG-N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $att->student->schoolClass->name ?? 'Enrolled' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-pill">#{{ $att->attempt_number }}</span>
                        </td>
                        <td class="small text-muted">{{ $att->started_at ? $att->started_at->format('M d, H:i') : '—' }}</td>
                        <td class="small text-muted">{{ $att->submitted_at ? $att->submitted_at->format('M d, H:i') : 'In session' }}</td>
                        <td>
                            @if($att->status === 'completed')
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Graded</span>
                            @elseif($att->status === 'submitted')
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen-nib me-1"></i> Needs Review</span>
                            @elseif($att->status === 'in_progress')
                                <span class="badge bg-info text-dark"><i class="fa-solid fa-spinner fa-spin me-1"></i> In Progress</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($att->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold {{ $isPassed ? 'text-success' : 'text-danger' }}">
                                {{ $att->score }} / {{ $quiz->total_points }} pts
                                <span class="small text-muted fw-normal">({{ $att->percentage }}%)</span>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('lms.quizzes.grading', $att->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" title="Grade / Review Answers">
                                    <i class="fa-solid fa-file-signature me-1"></i> Grade
                                </a>
                                <a href="{{ route('lms.quizzes.results', $att->id) }}" class="btn btn-sm btn-light border rounded-pill px-3" title="View Full Report">
                                    <i class="fa-solid fa-eye text-muted"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block text-muted"></i>
                            No student submissions recorded for this assessment yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attempts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $attempts->links() }}
        </div>
    @endif
</div>
@endsection
