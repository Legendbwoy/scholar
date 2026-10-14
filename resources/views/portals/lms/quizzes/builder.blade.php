@extends('layouts.portal')

@section('title', 'Q&A Authoring Studio — ' . $quiz->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments & Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Q&A Studio</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark">{{ $quiz->course->code ?? 'LMS' }}</span>
                <span class="badge {{ $quiz->type_badge }} text-uppercase">{{ $quiz->type }}</span>
                <span class="badge bg-secondary">{{ $quiz->course->schoolClass->name ?? 'All Cohorts' }}</span>
            </div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-sliders text-success me-2"></i>{{ $quiz->title }}
            </h2>
            <p class="text-muted mb-0">Author and curate questions, assign mark weightings, specify correct choices, and provide model answers.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="fa-solid fa-plus-circle me-1"></i> Add Question
            </button>
            <form action="{{ route('lms.quizzes.start', $quiz->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-dark rounded-pill px-3">
                    <i class="fa-solid fa-play me-1"></i> Preview Exam Player
                </button>
            </form>
            <a href="{{ route('lms.quizzes.submissions', $quiz->id) }}" class="btn btn-outline-info rounded-pill px-3">
                <i class="fa-solid fa-users me-1"></i> Submissions
            </a>
            <a href="{{ route('lms.quizzes.edit', $quiz->id) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-gear me-1"></i> Settings
            </a>
        </div>
    </div>
</div>

<!-- Studio Score & Summary Strip -->
<div class="portal-card p-3 p-md-4 mb-4 bg-gradient" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
    <div class="row g-3 align-items-center">
        <div class="col-md-3 border-end border-secondary border-opacity-50">
            <span class="text-white-50 small text-uppercase fw-bold">Total Assessment Points</span>
            <h2 class="fw-bold mb-0 text-success">{{ $quiz->total_points }} <span class="fs-6 text-white-50">pts</span></h2>
            <small class="text-white-50">Passing Score: {{ $quiz->pass_points }} pts</small>
        </div>
        <div class="col-md-3 border-end border-secondary border-opacity-50">
            <span class="text-white-50 small text-uppercase fw-bold">Questions Count</span>
            <h2 class="fw-bold mb-0 text-white">{{ $quiz->questions->count() }} <span class="fs-6 text-white-50">items</span></h2>
            <small class="text-white-50">Order sequence active</small>
        </div>
        <div class="col-md-3 border-end border-secondary border-opacity-50">
            <span class="text-white-50 small text-uppercase fw-bold">Time Limit</span>
            <h2 class="fw-bold mb-0 text-warning">{{ $quiz->duration_minutes > 0 ? $quiz->duration_minutes . ' Min' : 'Untimed' }}</h2>
            <small class="text-white-50">{{ $quiz->auto_submit_on_expiry ? 'Auto-submit on expiry' : 'Manual submission' }}</small>
        </div>
        <div class="col-md-3">
            <span class="text-white-50 small text-uppercase fw-bold">Shuffle Mode</span>
            <div class="mt-1">
                <span class="badge {{ $quiz->shuffle_questions ? 'bg-success' : 'bg-secondary' }} me-1">
                    <i class="fa-solid fa-shuffle me-1"></i> Qs: {{ $quiz->shuffle_questions ? 'Randomized' : 'Sequential' }}
                </span>
                <span class="badge {{ $quiz->shuffle_options ? 'bg-success' : 'bg-secondary' }}">
                    <i class="fa-solid fa-list-ol me-1"></i> Choices: {{ $quiz->shuffle_options ? 'Randomized' : 'Fixed' }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Questions List -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-ol text-primary me-2"></i>Curated Questions ({{ $quiz->questions->count() }})</h4>
        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="fa-solid fa-plus me-1"></i> Add Another Question
        </button>
    </div>

    @forelse($quiz->questions as $index => $q)
        <div class="portal-card p-4 mb-3 border-start border-4 {{ $q->question_type === 'essay' ? 'border-warning' : ($q->question_type === 'true_false' ? 'border-info' : 'border-success') }}">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">Question #{{ $index + 1 }}</span>
                    @php
                        $typeBadges = [
                            'mcq_single' => ['badge bg-primary', 'Single Choice MCQ'],
                            'mcq_multi' => ['badge bg-info text-dark', 'Multiple Select MCQ'],
                            'true_false' => ['badge bg-secondary', 'True / False'],
                            'short_answer' => ['badge bg-dark', 'Short Answer'],
                            'essay' => ['badge bg-warning text-dark', 'Essay / Rubric'],
                        ];
                        $badgeMeta = $typeBadges[$q->question_type] ?? ['badge bg-secondary', 'General'];
                    @endphp
                    <span class="{{ $badgeMeta[0] }}">{{ $badgeMeta[1] }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                        <i class="fa-solid fa-award me-1"></i>{{ $q->points }} Points
                    </span>
                </div>
                <div>
                    <form action="{{ route('lms.quizzes.questions.destroy', $q->id) }}" method="POST" class="d-inline confirm-action"
                          data-confirm-title="Delete Question?" 
                          data-confirm-text="Are you sure you want to delete this question? It will be removed from future candidate attempts." 
                          data-confirm-btn="Delete Question" 
                          data-confirm-color="#dc3545">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="fa-solid fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Question Body -->
            <h5 class="fw-bold text-dark mt-2 mb-3" style="line-height: 1.5;">{{ $q->question_text }}</h5>

            <!-- Options Display -->
            @if(in_array($q->question_type, ['mcq_single', 'mcq_multi', 'true_false']))
                <div class="row g-2 mb-3">
                    @foreach($q->options as $opt)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between {{ $opt->is_correct ? 'bg-success bg-opacity-10 border-success' : 'bg-light' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $opt->is_correct ? 'bg-success' : 'bg-secondary' }} rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ chr(64 + $loop->iteration) }}
                                    </span>
                                    <span class="fw-medium text-dark">{{ $opt->option_text }}</span>
                                </div>
                                @if($opt->is_correct)
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Correct</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($q->question_type === 'short_answer')
                <div class="p-3 bg-light rounded-3 border mb-3 small">
                    <span class="fw-bold text-muted text-uppercase d-block mb-1">Acceptable Answer Patterns:</span>
                    <code>{{ $q->acceptable_answers ?? 'Exact keyword matching' }}</code>
                </div>
            @elseif($q->question_type === 'essay')
                <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-50 mb-3 small">
                    <i class="fa-solid fa-pencil text-warning me-1"></i> <strong>Subjective Essay Question:</strong> Evaluated manually via Teacher Rubric Review Console during submissions grading.
                </div>
            @endif

            <!-- Explanation / Solution Feedback -->
            @if($q->explanation)
                <div class="p-3 bg-light rounded-3 border-start border-3 border-primary small text-muted">
                    <strong class="text-dark"><i class="fa-regular fa-lightbulb text-warning me-1"></i> Solution Explanation:</strong>
                    {{ $q->explanation }}
                </div>
            @endif
        </div>
    @empty
        <div class="portal-card p-5 text-center">
            <div class="p-4 bg-light rounded-circle d-inline-block mb-3">
                <i class="fa-solid fa-pen-ruler fs-1 text-muted"></i>
            </div>
            <h4 class="fw-bold text-dark">No Questions Added Yet</h4>
            <p class="text-muted max-w-md mx-auto mb-4">This assessment doesn't have any questions yet. Add Multiple Choice, True/False, Short Answer, or Essay prompts using the studio button below.</p>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="fa-solid fa-plus-circle me-1"></i> Add First Question
            </button>
        </div>
    @endforelse
</div>

<!-- Add Question Studio Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="addQuestionModalLabel">
                    <i class="fa-solid fa-plus-circle text-success me-2"></i>Add Question to Assessment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lms.quizzes.questions.store', $quiz->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Question Type & Points -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-uppercase text-muted">Question Format <span class="text-danger">*</span></label>
                            <select name="question_type" id="questionTypeSelect" class="form-select fw-semibold" required onchange="handleQuestionTypeChange(this.value)">
                                <option value="mcq_single">Single Choice Multiple Choice (One Correct Option)</option>
                                <option value="mcq_multi">Multiple Choice (Multiple Correct Options)</option>
                                <option value="true_false">True / False</option>
                                <option value="short_answer">Short Answer (Keywords / Direct match)</option>
                                <option value="essay">Subjective Long Essay (Teacher Graded)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Marks / Points <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-award text-success"></i></span>
                                <input type="number" name="points" class="form-control fw-bold" min="1" max="100" value="10" required>
                            </div>
                        </div>
                    </div>

                    <!-- Question Prompt -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Question Prompt / Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="3" placeholder="Enter clear, concise question prompt or problem statement..." required></textarea>
                    </div>

                    <!-- DYNAMIC PANELS ACCORDING TO TYPE -->

                    <!-- 1. Single Choice MCQ Panel -->
                    <div id="panel_mcq_single" class="q-type-panel mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted d-flex justify-content-between">
                            <span>Answer Options (Select the correct radio option)</span>
                            <span class="text-success"><i class="fa-solid fa-circle-check"></i> Choose 1 Correct</span>
                        </label>
                        <div class="row g-2">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-12">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio" name="correct_options[]" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} title="Mark as correct answer">
                                        </div>
                                        <span class="input-group-text bg-light fw-bold">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="options[{{ $i }}]" class="form-control" placeholder="Option choice {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- 2. Multiple Choice MCQ Panel -->
                    <div id="panel_mcq_multi" class="q-type-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted d-flex justify-content-between">
                            <span>Answer Choices (Check all options that are correct)</span>
                            <span class="text-primary"><i class="fa-solid fa-square-check"></i> Multi-Select</span>
                        </label>
                        <div class="row g-2">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-12">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="checkbox" name="correct_options[]" value="{{ $i }}" title="Check if correct">
                                        </div>
                                        <span class="input-group-text bg-light fw-bold">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="options[{{ $i }}]" class="form-control" placeholder="Option choice {{ chr(65 + $i) }}">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- 3. True / False Panel -->
                    <div id="panel_true_false" class="q-type-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted">Correct Statement Truth Value</label>
                        <div class="d-flex gap-4 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tf_correct" id="tf_true" value="true" checked>
                                <label class="form-check-label fw-bold text-success" for="tf_true"><i class="fa-solid fa-check me-1"></i> TRUE</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tf_correct" id="tf_false" value="false">
                                <label class="form-check-label fw-bold text-danger" for="tf_false"><i class="fa-solid fa-xmark me-1"></i> FALSE</label>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Short Answer Panel -->
                    <div id="panel_short_answer" class="q-type-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted">Acceptable Answers / Keywords (Comma-separated)</label>
                        <input type="text" name="acceptable_answers" class="form-control" placeholder="e.g. CPU, Central Processing Unit, processor">
                        <small class="text-muted">Candidate answers matching any of these terms (case-insensitive) will be automatically credited full marks.</small>
                    </div>

                    <!-- 5. Essay Panel -->
                    <div id="panel_essay" class="q-type-panel mb-4 d-none">
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 small">
                            <i class="fa-solid fa-circle-info text-warning me-1"></i> <strong>Subjective Essay Marking:</strong> Candidate answers will be collected in the submission console where you can award rubric points and provide bespoke feedback.
                        </div>
                    </div>

                    <!-- Solution Explanation -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-uppercase text-muted">Solution Explanation & Model Feedback (Optional)</label>
                        <textarea name="explanation" class="form-control" rows="2" placeholder="Explain why the answer is correct to aid student post-exam learning..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Save Question to Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleQuestionTypeChange(val) {
    document.querySelectorAll('.q-type-panel').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('panel_' + val);
    if (target) {
        target.classList.remove('d-none');
    }
}
</script>
@endsection
