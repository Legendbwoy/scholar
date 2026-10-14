@extends('layouts.portal')

@section('title', 'Author Assessment — LMS Studio')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments & Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Author Assessment</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-file-circle-plus text-success me-2"></i>Author New LMS Assessment</h2>
            <p class="text-muted mb-0">Define timing rules, grading components, anti-cheating shuffle flags, and candidate instructions.</p>
        </div>
        <a href="{{ route('lms.quizzes.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Assessments
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="portal-card p-4 p-md-5">
            <form action="{{ route('lms.quizzes.store') }}" method="POST">
                @csrf

                <!-- Step 1: Target Course & Assessment Type -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">1</span>
                    <h5 class="fw-bold mb-0 text-dark">Course Curriculum & Format</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-uppercase text-muted">Academic Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            <option value="">-- Choose Target Academic Course --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ (old('course_id', $preselectedCourse?->id) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->title }} ({{ $c->schoolClass->name ?? 'All Cohorts' }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-uppercase text-muted">Assessment Category <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quick Knowledge Quiz</option>
                            <option value="test" {{ old('type') === 'test' ? 'selected' : '' }}>Class Test</option>
                            <option value="midterm" {{ old('type', 'midterm') === 'midterm' ? 'selected' : '' }}>Mid-Term Examination</option>
                            <option value="final_exam" {{ old('type') === 'final_exam' ? 'selected' : '' }}>Final Semester Examination</option>
                            <option value="practice" {{ old('type') === 'practice' ? 'selected' : '' }}>Practice Drill (Self-Assessment)</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-muted">Assessment Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               placeholder="e.g. Unit 3 Quiz: Advanced Object Oriented Principles & Inheritance" 
                               value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Step 2: Timing, Passing Score & Gradebook Sync -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">2</span>
                    <h5 class="fw-bold mb-0 text-dark">Timing, Passing Threshold & Gradebook Component</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Time Limit (Minutes) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-stopwatch text-warning"></i></span>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   min="0" max="360" value="{{ old('duration_minutes', 30) }}" required>
                        </div>
                        <small class="text-muted">Enter 0 for untimed assessments.</small>
                        @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Passing Threshold (Points) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-check-double text-success"></i></span>
                            <input type="number" name="pass_points" class="form-control @error('pass_points') is-invalid @enderror" 
                                   min="1" value="{{ old('pass_points', 20) }}" required>
                        </div>
                        <small class="text-muted">Minimum points to pass.</small>
                        @error('pass_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Maximum Attempts <span class="text-danger">*</span></label>
                        <select name="max_attempts" class="form-select @error('max_attempts') is-invalid @enderror" required>
                            <option value="1" {{ old('max_attempts', 1) == 1 ? 'selected' : '' }}>1 Attempt (Strict Exam)</option>
                            <option value="2" {{ old('max_attempts') == 2 ? 'selected' : '' }}>2 Attempts</option>
                            <option value="3" {{ old('max_attempts') == 3 ? 'selected' : '' }}>3 Attempts</option>
                            <option value="5" {{ old('max_attempts') == 5 ? 'selected' : '' }}>5 Attempts</option>
                            <option value="10" {{ old('max_attempts') == 10 ? 'selected' : '' }}>Unlimited / 10 Attempts</option>
                        </select>
                        @error('max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">Gradebook Component Sync <span class="text-danger">*</span></label>
                        <select name="grading_component" class="form-select @error('grading_component') is-invalid @enderror" required>
                            <option value="quiz" {{ old('grading_component') === 'quiz' ? 'selected' : '' }}>Quizzes (Continuous Assessment)</option>
                            <option value="test" {{ old('grading_component') === 'test' ? 'selected' : '' }}>Class Tests</option>
                            <option value="midterm" {{ old('grading_component', 'midterm') === 'midterm' ? 'selected' : '' }}>Mid-Term Assessment Weight</option>
                            <option value="final_exam" {{ old('grading_component') === 'final_exam' ? 'selected' : '' }}>Final Examination Weight</option>
                            <option value="practice" {{ old('grading_component') === 'practice' ? 'selected' : '' }}>Ungraded Practice (No Gradebook Impact)</option>
                        </select>
                        @error('grading_component')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Available From</label>
                        <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Available Until</label>
                        <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time') }}">
                    </div>
                </div>

                <!-- Step 3: Instructions & Anti-Cheating Policies -->
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-success rounded-circle p-2 px-3 fw-bold">3</span>
                    <h5 class="fw-bold mb-0 text-dark">Security, Randomization & Candidate Guidance</h5>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Candidate Instructions / Exam Rules</label>
                    <textarea name="instructions" class="form-control" rows="3" 
                              placeholder="Please ensure a stable internet connection. Calculators and external references are strictly prohibited. Answers are automatically saved during progression.">{{ old('instructions') }}</textarea>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Assessment Execution Flags</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="shuffle_questions" id="shuffle_q" value="1" {{ old('shuffle_questions', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="shuffle_q">Shuffle Question Sequence</label>
                                <div class="text-muted small">Each candidate receives questions in randomized order.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="shuffle_options" id="shuffle_o" value="1" {{ old('shuffle_options', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="shuffle_o">Shuffle MCQ Option Choices</label>
                                <div class="text-muted small">Randomize choices (A, B, C, D) per student session.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_submit_on_expiry" id="auto_submit" value="1" {{ old('auto_submit_on_expiry', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="auto_submit">Auto-Submit on Timer Expiry</label>
                                <div class="text-muted small">Automatically freeze and submit answers when countdown hits zero.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_correct_answers" id="show_answers" value="1" {{ old('show_correct_answers', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="show_answers">Reveal Solutions after Submission</label>
                                <div class="text-muted small">Allow students to review solution explanations after completion.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_published">Publish Immediately</label>
                                <div class="text-muted small">Make visible on enrolled student portals.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('lms.quizzes.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm">
                        Create & Open Question Studio <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
