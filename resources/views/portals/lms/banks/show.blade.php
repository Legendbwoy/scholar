@extends('layouts.portal')

@section('title', 'Manage Question Bank — ' . $bank->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.banks.index') }}" class="text-decoration-none">Question Banks</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $bank->title }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark">{{ $bank->subject->code ?? ($bank->course->code ?? 'General') }}</span>
                @if($bank->topic)
                    <span class="badge bg-primary text-uppercase">{{ $bank->topic }}</span>
                @endif
                <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $bank->questions->count() }} Items</span>
            </div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-folder-open text-success me-2"></i>{{ $bank->title }}</h2>
            <p class="text-muted mb-0">{{ $bank->description ?? 'Institutional repository of curated assessment questions.' }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addBankQuestionModal">
                <i class="fa-solid fa-plus-circle me-1"></i> Add Question to Bank
            </button>
            <a href="{{ route('lms.banks.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Banks
            </a>
        </div>
    </div>
</div>

<!-- Questions in Bank -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i>Curated Bank Items ({{ $bank->questions->count() }})</h4>
        <span class="text-muted small">These items are ready to be cloned into any quiz, test, or examination.</span>
    </div>

    @forelse($bank->questions as $index => $q)
        <div class="portal-card p-4 mb-3 border-start border-4 {{ $q->question_type === 'essay' ? 'border-warning' : ($q->question_type === 'true_false' ? 'border-info' : 'border-success') }}">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">Item #{{ $index + 1 }}</span>
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
                    <form action="{{ route('lms.banks.questions.destroy', $q->id) }}" method="POST" class="d-inline confirm-action"
                          data-confirm-title="Remove Question?" 
                          data-confirm-text="Are you sure you want to delete this question from the bank?" 
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

            <h5 class="fw-bold text-dark mt-2 mb-3" style="line-height: 1.5;">{{ $q->question_text }}</h5>

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
                    <span class="fw-bold text-muted text-uppercase d-block mb-1">Acceptable Patterns:</span>
                    <code>{{ $q->acceptable_answers ?? 'Exact keyword matching' }}</code>
                </div>
            @elseif($q->question_type === 'essay')
                <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-50 mb-3 small">
                    <i class="fa-solid fa-pencil text-warning me-1"></i> <strong>Subjective Prompt:</strong> Evaluated via instructor rubric scoring.
                </div>
            @endif

            @if($q->explanation)
                <div class="p-3 bg-light rounded-3 border-start border-3 border-primary small text-muted">
                    <strong class="text-dark"><i class="fa-regular fa-lightbulb text-warning me-1"></i> Model Solution:</strong>
                    {{ $q->explanation }}
                </div>
            @endif
        </div>
    @empty
        <div class="portal-card p-5 text-center">
            <div class="p-4 bg-light rounded-circle d-inline-block mb-3">
                <i class="fa-solid fa-folder-open fs-1 text-muted"></i>
            </div>
            <h4 class="fw-bold text-dark">No Questions in this Bank</h4>
            <p class="text-muted max-w-md mx-auto mb-4">Start populating this repository with reusable questions or export them directly from existing quizzes.</p>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addBankQuestionModal">
                <i class="fa-solid fa-plus-circle me-1"></i> Add Question to Bank
            </button>
        </div>
    @endforelse
</div>

<!-- Add Question to Bank Modal -->
<div class="modal fade" id="addBankQuestionModal" tabindex="-1" aria-labelledby="addBankQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="addBankQuestionModalLabel">
                    <i class="fa-solid fa-plus-circle text-success me-2"></i>Add Reusable Item to Bank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lms.banks.questions.store', $bank->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-uppercase text-muted">Question Format <span class="text-danger">*</span></label>
                            <select name="question_type" id="bankQuestionTypeSelect" class="form-select fw-semibold" required onchange="handleBankTypeChange(this.value)">
                                <option value="mcq_single">Single Choice Multiple Choice</option>
                                <option value="mcq_multi">Multiple Choice (Multiple Correct Options)</option>
                                <option value="true_false">True / False</option>
                                <option value="short_answer">Short Answer (Keywords / Direct Match)</option>
                                <option value="essay">Subjective Long Essay (Rubric Scored)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Default Marks <span class="text-danger">*</span></label>
                            <input type="number" name="points" class="form-control fw-bold" min="1" max="100" value="10" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Question Prompt / Statement <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="3" placeholder="Enter clear question statement..." required></textarea>
                    </div>

                    <!-- Single Choice MCQ Panel -->
                    <div id="bpanel_mcq_single" class="bank-q-panel mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Answer Options (Mark 1 Correct)</label>
                        <div class="row g-2">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-12">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio" name="correct_options[]" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}>
                                        </div>
                                        <span class="input-group-text bg-light fw-bold">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="options[{{ $i }}]" class="form-control" placeholder="Option choice {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Multiple Choice MCQ Panel -->
                    <div id="bpanel_mcq_multi" class="bank-q-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted">Answer Choices (Check All Correct)</label>
                        <div class="row g-2">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-12">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="checkbox" name="correct_options[]" value="{{ $i }}">
                                        </div>
                                        <span class="input-group-text bg-light fw-bold">{{ chr(65 + $i) }}</span>
                                        <input type="text" name="options[{{ $i }}]" class="form-control" placeholder="Option choice {{ chr(65 + $i) }}">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- True / False Panel -->
                    <div id="bpanel_true_false" class="bank-q-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted">Truth Value</label>
                        <div class="d-flex gap-4 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tf_correct" id="btf_true" value="true" checked>
                                <label class="form-check-label fw-bold text-success" for="btf_true"><i class="fa-solid fa-check me-1"></i> TRUE</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tf_correct" id="btf_false" value="false">
                                <label class="form-check-label fw-bold text-danger" for="btf_false"><i class="fa-solid fa-xmark me-1"></i> FALSE</label>
                            </div>
                        </div>
                    </div>

                    <!-- Short Answer Panel -->
                    <div id="bpanel_short_answer" class="bank-q-panel mb-4 d-none">
                        <label class="form-label small fw-bold text-uppercase text-muted">Acceptable Answers (Comma-separated)</label>
                        <input type="text" name="acceptable_answers" class="form-control" placeholder="e.g. CPU, processor, Central Processing Unit">
                    </div>

                    <!-- Essay Panel -->
                    <div id="bpanel_essay" class="bank-q-panel mb-4 d-none">
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 small">
                            <i class="fa-solid fa-circle-info text-warning me-1"></i> Open-ended essay prompt evaluated via rubric scoring.
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-uppercase text-muted">Model Answer / Solution Explanation</label>
                        <textarea name="explanation" class="form-control" rows="2" placeholder="Model answer or reference notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm">
                        Save Item to Bank
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleBankTypeChange(val) {
    document.querySelectorAll('.bank-q-panel').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('bpanel_' + val);
    if (target) {
        target.classList.remove('d-none');
    }
}
</script>
@endsection
