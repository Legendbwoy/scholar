@extends('layouts.portal')

@section('title', 'Edit Assessment Settings — ' . $quiz->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments & Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Assessment</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Edit Assessment Settings</h2>
            <p class="text-muted mb-0">Modify timing, attempt allowances, passing thresholds, or availability windows for <strong>{{ $quiz->title }}</strong>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lms.quizzes.builder', $quiz->id) }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fa-solid fa-sliders me-1"></i> Question Studio
            </a>
            <a href="{{ route('lms.quizzes.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="portal-card p-4 p-md-5">
            <form action="{{ route('lms.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-uppercase text-muted">Academic Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ (old('course_id', $quiz->course_id) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->title }} ({{ $c->schoolClass->name ?? 'All Cohorts' }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-uppercase text-muted">Assessment Category <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="quiz" {{ old('type', $quiz->type) === 'quiz' ? 'selected' : '' }}>Quick Knowledge Quiz</option>
                            <option value="test" {{ old('type', $quiz->type) === 'test' ? 'selected' : '' }}>Class Test</option>
                            <option value="midterm" {{ old('type', $quiz->type) === 'midterm' ? 'selected' : '' }}>Mid-Term Examination</option>
                            <option value="final_exam" {{ old('type', $quiz->type) === 'final_exam' ? 'selected' : '' }}>Final Semester Examination</option>
                            <option value="practice" {{ old('type', $quiz->type) === 'practice' ? 'selected' : '' }}>Practice Drill</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-muted">Assessment Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               value="{{ old('title', $quiz->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Time Limit (Minutes) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-stopwatch text-warning"></i></span>
                            <input type="number" name="duration_minutes" class="form-control" 
                                   min="0" max="360" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Passing Threshold (Points) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-check-double text-success"></i></span>
                            <input type="number" name="pass_points" class="form-control" 
                                   min="1" value="{{ old('pass_points', $quiz->pass_points) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Maximum Attempts <span class="text-danger">*</span></label>
                        <select name="max_attempts" class="form-select" required>
                            <option value="1" {{ old('max_attempts', $quiz->max_attempts) == 1 ? 'selected' : '' }}>1 Attempt (Strict Exam)</option>
                            <option value="2" {{ old('max_attempts', $quiz->max_attempts) == 2 ? 'selected' : '' }}>2 Attempts</option>
                            <option value="3" {{ old('max_attempts', $quiz->max_attempts) == 3 ? 'selected' : '' }}>3 Attempts</option>
                            <option value="5" {{ old('max_attempts', $quiz->max_attempts) == 5 ? 'selected' : '' }}>5 Attempts</option>
                            <option value="10" {{ old('max_attempts', $quiz->max_attempts) == 10 ? 'selected' : '' }}>Unlimited / 10 Attempts</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">Gradebook Component Sync <span class="text-danger">*</span></label>
                        <select name="grading_component" class="form-select" required>
                            <option value="quiz" {{ old('grading_component', $quiz->grading_component) === 'quiz' ? 'selected' : '' }}>Quizzes (Continuous Assessment)</option>
                            <option value="test" {{ old('grading_component', $quiz->grading_component) === 'test' ? 'selected' : '' }}>Class Tests</option>
                            <option value="midterm" {{ old('grading_component', $quiz->grading_component) === 'midterm' ? 'selected' : '' }}>Mid-Term Assessment Weight</option>
                            <option value="final_exam" {{ old('grading_component', $quiz->grading_component) === 'final_exam' ? 'selected' : '' }}>Final Examination Weight</option>
                            <option value="practice" {{ old('grading_component', $quiz->grading_component) === 'practice' ? 'selected' : '' }}>Ungraded Practice</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Available From</label>
                        <input type="datetime-local" name="start_time" class="form-control" 
                               value="{{ old('start_time', $quiz->start_time ? $quiz->start_time->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Available Until</label>
                        <input type="datetime-local" name="end_time" class="form-control" 
                               value="{{ old('end_time', $quiz->end_time ? $quiz->end_time->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Candidate Instructions</label>
                    <textarea name="instructions" class="form-control" rows="3">{{ old('instructions', $quiz->instructions) }}</textarea>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Execution Flags</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="shuffle_questions" id="shuffle_q" value="1" {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="shuffle_q">Shuffle Question Sequence</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="shuffle_options" id="shuffle_o" value="1" {{ old('shuffle_options', $quiz->shuffle_options) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="shuffle_o">Shuffle MCQ Option Choices</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_submit_on_expiry" id="auto_submit" value="1" {{ old('auto_submit_on_expiry', $quiz->auto_submit_on_expiry) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="auto_submit">Auto-Submit on Timer Expiry</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_correct_answers" id="show_answers" value="1" {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="show_answers">Reveal Solutions after Submission</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_published">Publish Immediately</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('lms.quizzes.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-warning px-5 fw-bold rounded-pill">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
