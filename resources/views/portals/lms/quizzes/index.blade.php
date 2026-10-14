@extends('layouts.portal')

@section('title', 'LMS Assessments, Quizzes & Examinations')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    @if($course)
                        <li class="breadcrumb-item"><a href="{{ route('courses.show', $course->id) }}" class="text-decoration-none">{{ $course->code }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Assessments & Quizzes</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-graduation-cap text-success me-2"></i>
                @if($course)
                    {{ $course->title }} — Assessments & Quizzes
                @else
                    Digital LMS Assessments, Quizzes & Examinations
                @endif
            </h2>
            <p class="text-muted mb-0">Interactive testing terminals, Q&A authoring, countdown limits, automated evaluations, and grading synchronization.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                <a href="{{ route('lms.quizzes.create', $course ? ['course_id' => $course->id] : []) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus-circle me-1"></i> Author New Assessment
                </a>
            @endif
            @if($course)
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Course Syllabus
                </a>
            @endif
        </div>
    </div>
</div>

<!-- KPI Metrics Ribbon -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Published Assessments</span>
                <h3 class="mb-0">{{ $totalQuizzes }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-file-pen"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Official Midterms/Exams</span>
                <h3 class="mb-0">{{ $totalExams }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Student Submissions</span>
                <h3 class="mb-0">{{ $totalAttempts }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Active Live Sessions</span>
                <h3 class="mb-0">{{ $activeSessions }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filtering Hub -->
<div class="portal-card p-4 mb-4">
    <form method="GET" action="{{ route('lms.quizzes.index') }}" class="row g-3 align-items-end">
        @if($course)
            <input type="hidden" name="course_id" value="{{ $course->id }}">
        @endif
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted text-uppercase">Search Keyword</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Title, topic, course name..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Assessment Type</label>
            <select name="type" class="form-select">
                <option value="">All Assessment Formats</option>
                <option value="quiz" {{ request('type') === 'quiz' ? 'selected' : '' }}>Quick Knowledge Quiz</option>
                <option value="test" {{ request('type') === 'test' ? 'selected' : '' }}>Class Test</option>
                <option value="midterm" {{ request('type') === 'midterm' ? 'selected' : '' }}>Mid-Term Examination</option>
                <option value="final_exam" {{ request('type') === 'final_exam' ? 'selected' : '' }}>Final Examination</option>
                <option value="practice" {{ request('type') === 'practice' ? 'selected' : '' }}>Practice Drill</option>
            </select>
        </div>
        @if(!$course)
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted text-uppercase">Course Filter</label>
            <select name="course_id" class="form-select">
                <option value="">All Academic Courses</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->title }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100 fw-bold">Filter</button>
            <a href="{{ route('lms.quizzes.index') }}" class="btn btn-light border px-3" title="Clear Filters"><i class="fa-solid fa-rotate-left"></i></a>
        </div>
    </form>
</div>

<!-- Quizzes / Examinations Grid -->
<div class="row g-4 mb-4">
    @forelse($quizzes as $item)
        <div class="col-md-6 col-xl-4">
            <div class="portal-card h-100 d-flex flex-column border shadow-sm transition-all" style="border-radius: 14px; overflow: hidden;">
                <!-- Card Header with Type Badge and Duration -->
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <span class="badge {{ $item->type_badge }} px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-bookmark me-1 opacity-75"></i>{{ strtoupper($item->type) }}
                    </span>
                    <span class="small fw-semibold text-muted">
                        <i class="fa-regular fa-clock text-secondary me-1"></i>
                        {{ $item->duration_minutes > 0 ? $item->duration_minutes . ' Mins' : 'Untimed' }}
                    </span>
                </div>

                <div class="p-4 flex-grow-1">
                    <div class="mb-2">
                        <span class="badge bg-light text-dark border fw-bold me-1">{{ $item->course->code ?? 'LMS' }}</span>
                        <span class="badge bg-light text-secondary border">{{ $item->course->schoolClass->name ?? 'Cohort' }}</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2 line-clamp-2" title="{{ $item->title }}">
                        {{ $item->title }}
                    </h5>
                    <p class="text-muted small mb-3 line-clamp-2" style="min-height: 38px;">
                        {{ $item->instructions ?? 'No special instructions specified. Click below to begin evaluation.' }}
                    </p>

                    <!-- Assessment Details Grid -->
                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted"><i class="fa-solid fa-list-check me-1 text-secondary"></i> Total Questions:</span>
                            <span class="fw-bold text-dark">{{ $item->questions_count ?? $item->questions->count() }} Qs</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted"><i class="fa-solid fa-bullseye me-1 text-secondary"></i> Total Points:</span>
                            <span class="fw-bold text-dark">{{ $item->total_points }} pts (Pass: {{ $item->pass_points }})</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fa-solid fa-repeat me-1 text-secondary"></i> Max Attempts:</span>
                            <span class="fw-bold text-dark">{{ $item->max_attempts }} allowed</span>
                        </div>
                    </div>

                    <!-- Availability Window Pill -->
                    <div class="d-flex align-items-center gap-2 small text-muted mb-2">
                        <i class="fa-solid fa-calendar-day text-secondary"></i>
                        @if($item->start_time && $item->end_time)
                            <span>{{ $item->start_time->format('M d') }} — {{ $item->end_time->format('M d, Y') }}</span>
                        @elseif($item->end_time)
                            <span>Closes {{ $item->end_time->format('M d, Y') }}</span>
                        @else
                            <span class="text-emerald fw-bold">Always Accessible</span>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Student Action -->
                    @if(auth()->user()->isStudent())
                        @php
                            $studentAttempts = $item->attempts->where('student_id', auth()->user()->student?->id);
                            $activeAttempt = $studentAttempts->where('status', 'in_progress')->first();
                            $completedAttempt = $studentAttempts->whereIn('status', ['completed', 'submitted', 'timed_out'])->last();
                            $canAttempt = $studentAttempts->count() < $item->max_attempts;
                        @endphp

                        @if($activeAttempt)
                            <a href="{{ route('lms.quizzes.take', $activeAttempt->id) }}" class="btn btn-dark w-100 fw-bold text-white rounded-pill">
                                <i class="fa-solid fa-play me-1"></i> Resume In-Progress Session
                            </a>
                        @elseif($canAttempt)
                            <form action="{{ route('lms.quizzes.start', $item->id) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-emerald w-100 fw-bold rounded-pill shadow-sm">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Start Assessment
                                </button>
                            </form>
                        @elseif($completedAttempt)
                            <a href="{{ route('lms.quizzes.results', $completedAttempt->id) }}" class="btn btn-outline-dark w-100 fw-semibold rounded-pill">
                                <i class="fa-solid fa-chart-pie me-1"></i> View Result (Score: {{ $completedAttempt->percentage }}%)
                            </a>
                        @else
                            <button class="btn btn-secondary w-100 rounded-pill disabled" disabled>Attempts Exhausted</button>
                        @endif
                    @else
                        <!-- Faculty & Admin Action Suite -->
                        <div class="d-flex align-items-center gap-2 w-100">
                            <a href="{{ route('lms.quizzes.builder', $item->id) }}" class="btn btn-sm btn-dark rounded-pill flex-grow-1 fw-semibold" title="Author Questions">
                                <i class="fa-solid fa-sliders me-1"></i> Studio ({{ $item->questions->count() }})
                            </a>
                            <a href="{{ route('lms.quizzes.submissions', $item->id) }}" class="btn btn-sm btn-light border rounded-pill flex-grow-1 fw-semibold text-dark" title="View Student Submissions">
                                <i class="fa-solid fa-users me-1 text-secondary"></i> Submissions
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <form action="{{ route('lms.quizzes.start', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa-solid fa-eye me-2 text-muted"></i> Preview as Student
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('lms.quizzes.edit', $item->id) }}">
                                            <i class="fa-solid fa-pen-to-square me-2 text-warning"></i> Edit Settings
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('lms.quizzes.destroy', $item->id) }}" method="POST" class="confirm-action" 
                                              data-confirm-title="Delete Assessment?" 
                                              data-confirm-text="Purging '{{ $item->title }}' will remove all authoring questions, options, and student attempt histories." 
                                              data-confirm-btn="Delete Assessment" 
                                              data-confirm-color="#dc3545">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="portal-card p-5 text-center">
                <div class="p-4 bg-light rounded-circle d-inline-block mb-3">
                    <i class="fa-solid fa-clipboard-question fs-1 text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark">No Assessments Found</h4>
                <p class="text-muted max-w-md mx-auto mb-4">No tests, quizzes, or examinations match your active filters. Author your first online assessment to enable student testing.</p>
                @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                    <a href="{{ route('lms.quizzes.create', $course ? ['course_id' => $course->id] : []) }}" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-plus me-1"></i> Author New Assessment
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination Links -->
@if($quizzes->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $quizzes->withQueryString()->links() }}
    </div>
@endif
@endsection
