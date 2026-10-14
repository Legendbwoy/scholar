<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} — Interactive Examination Terminal</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-accent: #009961;
            --primary-dark: #07472f;
            --dark-obsidian: #0f172a;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }
        .exam-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .timer-badge {
            font-variant-numeric: tabular-nums;
            font-size: 1.15rem;
            letter-spacing: 0.05em;
        }
        .timer-warning {
            animation: pulse-red 1s infinite alternate;
        }
        @keyframes pulse-red {
            from { background-color: #ef4444; color: #fff; }
            to { background-color: #991b1b; color: #fff; }
        }
        .q-matrix-btn {
            width: 38px;
            height: 38px;
            font-weight: 700;
            font-size: 0.85rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
        }
        .q-matrix-btn:hover {
            border-color: var(--primary-accent);
            color: var(--primary-accent);
        }
        .q-matrix-btn.active {
            border-color: #0f172a !important;
            background-color: #0f172a !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.2);
        }
        .q-matrix-btn.answered {
            border-color: var(--primary-accent) !important;
            background-color: var(--primary-accent) !important;
            color: #ffffff !important;
        }
        .option-choice-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        .option-choice-card:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }
        .option-choice-card.selected {
            border-color: var(--primary-accent);
            background: #ecfdf5;
            box-shadow: 0 0 0 2px rgba(0, 153, 97, 0.2);
        }
        .exam-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

    <!-- Strict Examination Top Bar -->
    <header class="exam-header shadow-sm py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 bg-success text-white rounded-3 fs-5">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $quiz->title }}</h5>
                    <span class="small text-muted">
                        Course: <strong>{{ $quiz->course->code ?? 'LMS' }}</strong> • Candidate: <strong>{{ $attempt->student->user->name ?? 'Student' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Countdown Timer & Actions -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 p-2 px-3 bg-light rounded-pill border">
                    <i class="fa-solid fa-stopwatch text-warning fs-5"></i>
                    <div>
                        <span class="small text-muted d-block" style="font-size: 0.7rem; line-height: 1;">REMAINING TIME</span>
                        <span id="countdownDisplay" class="fw-bold timer-badge text-dark">
                            --:--
                        </span>
                    </div>
                </div>

                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#submitModal">
                    <i class="fa-solid fa-flag-checkered me-1"></i> Finish & Submit
                </button>
            </div>
        </div>
    </header>

    <div class="container-fluid px-lg-5 py-4">
        <div class="row g-4">
            <!-- Main Question Area -->
            <div class="col-lg-8 col-xl-9">
                <form id="examForm" action="{{ route('lms.quizzes.submit', $attempt->id) }}" method="POST">
                    @csrf

                    @foreach($questions as $index => $q)
                        @php
                            $savedAnswer = $answersMap->get($q->id);
                        @endphp
                        <div class="exam-card p-4 p-md-5 question-pane {{ $index > 0 ? 'd-none' : '' }}" id="question_pane_{{ $index }}" data-qindex="{{ $index }}" data-qid="{{ $q->id }}">
                            <!-- Top Info Ribbon -->
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-pill px-3 py-2 fw-bold">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                                    <span class="badge bg-light text-muted border text-uppercase">{{ str_replace('_', ' ', $q->question_type) }}</span>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2">
                                    <i class="fa-solid fa-award me-1"></i>{{ $q->points }} Marks
                                </span>
                            </div>

                            <!-- Question Prompt -->
                            <h4 class="fw-bold text-dark mb-4" style="line-height: 1.5;">{{ $q->question_text }}</h4>

                            <!-- Input depending on question type -->
                            @if($q->question_type === 'mcq_single')
                                <div class="options-container d-flex flex-column gap-3 mb-4">
                                    @php
                                        $options = $quiz->shuffle_options ? $q->options->shuffle() : $q->options;
                                    @endphp
                                    @foreach($options as $opt)
                                        @php
                                            $isChecked = ($savedAnswer && $savedAnswer->quiz_question_option_id == $opt->id);
                                        @endphp
                                        <label class="option-choice-card d-flex align-items-center justify-content-between {{ $isChecked ? 'selected' : '' }}" for="opt_{{ $opt->id }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <input class="form-check-input mt-0 answer-radio" type="radio" 
                                                       name="answers[{{ $q->id }}]" id="opt_{{ $opt->id }}" 
                                                       value="{{ $opt->id }}" data-qid="{{ $q->id }}" {{ $isChecked ? 'checked' : '' }}
                                                       onchange="onSelectOption({{ $index }}, {{ $q->id }}, {{ $opt->id }}, this)">
                                                <span class="fw-semibold text-dark fs-5">{{ $opt->option_text }}</span>
                                            </div>
                                            <span class="badge bg-light text-muted border rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ chr(64 + $loop->iteration) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'mcq_multi')
                                <div class="options-container d-flex flex-column gap-3 mb-4">
                                    @php
                                        $options = $quiz->shuffle_options ? $q->options->shuffle() : $q->options;
                                        $savedOpts = $savedAnswer && $savedAnswer->selected_options ? (array)$savedAnswer->selected_options : [];
                                    @endphp
                                    @foreach($options as $opt)
                                        @php
                                            $isChecked = in_array($opt->id, $savedOpts);
                                        @endphp
                                        <label class="option-choice-card d-flex align-items-center justify-content-between {{ $isChecked ? 'selected' : '' }}" for="opt_multi_{{ $opt->id }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <input class="form-check-input mt-0 answer-checkbox" type="checkbox" 
                                                       name="answers[{{ $q->id }}][]" id="opt_multi_{{ $opt->id }}" 
                                                       value="{{ $opt->id }}" data-qid="{{ $q->id }}" {{ $isChecked ? 'checked' : '' }}
                                                       onchange="onSelectMultiOption({{ $index }}, {{ $q->id }}, this)">
                                                <span class="fw-semibold text-dark fs-5">{{ $opt->option_text }}</span>
                                            </div>
                                            <span class="badge bg-light text-muted border rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ chr(64 + $loop->iteration) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'true_false')
                                <div class="row g-3 mb-4">
                                    @foreach($q->options as $opt)
                                        @php
                                            $isChecked = ($savedAnswer && $savedAnswer->quiz_question_option_id == $opt->id);
                                        @endphp
                                        <div class="col-md-6">
                                            <label class="option-choice-card h-100 d-flex align-items-center justify-content-center text-center p-4 {{ $isChecked ? 'selected' : '' }}" for="tf_{{ $opt->id }}">
                                                <input class="form-check-input d-none answer-radio" type="radio" 
                                                       name="answers[{{ $q->id }}]" id="tf_{{ $opt->id }}" 
                                                       value="{{ $opt->id }}" data-qid="{{ $q->id }}" {{ $isChecked ? 'checked' : '' }}
                                                       onchange="onSelectOption({{ $index }}, {{ $q->id }}, {{ $opt->id }}, this)">
                                                <div>
                                                    <i class="fa-solid {{ strtolower($opt->option_text) === 'true' ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger' }} fs-2 mb-2 d-block"></i>
                                                    <span class="fw-bold fs-4 text-dark">{{ strtoupper($opt->option_text) }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->question_type === 'short_answer')
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Your Concise Answer</label>
                                    <input type="text" name="answers[{{ $q->id }}]" class="form-control form-control-lg" 
                                           placeholder="Type your answer here..." 
                                           value="{{ $savedAnswer ? $savedAnswer->text_answer : '' }}"
                                           oninput="onTextInput({{ $index }}, {{ $q->id }}, this.value)">
                                </div>
                            @elseif($q->question_type === 'essay')
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Essay Response</label>
                                    <textarea name="answers[{{ $q->id }}]" class="form-control" rows="8" 
                                              placeholder="Provide your comprehensive explanation or analytical argument here..." 
                                              oninput="onTextInput({{ $index }}, {{ $q->id }}, this.value)">{{ $savedAnswer ? $savedAnswer->text_answer : '' }}</textarea>
                                </div>
                            @endif

                            <!-- Bottom Navigation Controls -->
                            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" 
                                        onclick="goToQuestion({{ $index - 1 }})" {{ $index === 0 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-arrow-left me-1"></i> Previous
                                </button>
                                
                                <span class="small text-muted" id="saveStatus_{{ $index }}">
                                    <i class="fa-solid fa-cloud-arrow-up text-success me-1"></i> Answers auto-saved
                                </span>

                                @if($index < $questions->count() - 1)
                                    <button type="button" class="btn btn-dark rounded-pill px-4" onclick="goToQuestion({{ $index + 1 }})">
                                        Next <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#submitModal">
                                        Review & Finish <i class="fa-solid fa-check ms-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

            <!-- Right Question Matrix & Progress -->
            <div class="col-lg-4 col-xl-3">
                <div class="exam-card p-4 position-sticky" style="top: 90px;">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-table-cells text-primary me-2"></i>Question Palette</h6>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($questions as $index => $q)
                            @php
                                $isAnswered = false;
                                if ($ans = $answersMap->get($q->id)) {
                                    $isAnswered = ($ans->quiz_question_option_id || !empty($ans->selected_options) || !empty($ans->text_answer));
                                }
                            @endphp
                            <button type="button" class="q-matrix-btn {{ $index === 0 ? 'active' : '' }} {{ $isAnswered ? 'answered' : '' }}" 
                                    id="matrix_btn_{{ $index }}" 
                                    onclick="goToQuestion({{ $index }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="p-3 bg-light rounded-3 small">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="q-matrix-btn answered" style="width: 20px; height: 20px; cursor: default;"></span>
                            <span class="text-muted">Answered</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="q-matrix-btn active" style="width: 20px; height: 20px; cursor: default;"></span>
                            <span class="text-muted">Current Question</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="q-matrix-btn" style="width: 20px; height: 20px; cursor: default;"></span>
                            <span class="text-muted">Unanswered</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <button type="button" class="btn btn-outline-danger w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#submitModal">
                            Submit Examination
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark" id="submitModalLabel">
                        <i class="fa-solid fa-circle-exclamation text-warning me-2"></i>Submit Assessment?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="p-3 bg-light rounded-circle d-inline-block mb-3">
                        <i class="fa-solid fa-flag-checkered fs-1 text-danger"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Are you ready to submit your exam?</h5>
                    <p class="text-muted mb-0">Once submitted, all answers are locked in. You will not be able to revisit or modify your responses.</p>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Resume Assessment</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold rounded-pill" onclick="document.getElementById('examForm').submit()">
                        Yes, Submit Final Answers
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentIndex = 0;
        const totalQuestions = {{ $questions->count() }};
        const attemptId = {{ $attempt->id }};
        const saveAnswerUrl = "{{ route('lms.quizzes.saveAnswer', $attempt->id) }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Navigation
        function goToQuestion(index) {
            if (index < 0 || index >= totalQuestions) return;
            
            // Hide current
            document.getElementById('question_pane_' + currentIndex).classList.add('d-none');
            const prevMatrixBtn = document.getElementById('matrix_btn_' + currentIndex);
            if (prevMatrixBtn) prevMatrixBtn.classList.remove('active');

            // Show target
            currentIndex = index;
            document.getElementById('question_pane_' + currentIndex).classList.remove('d-none');
            const newMatrixBtn = document.getElementById('matrix_btn_' + currentIndex);
            if (newMatrixBtn) newMatrixBtn.classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Single Choice / True-False selection
        function onSelectOption(qIndex, questionId, optionId, inputElement) {
            // Update visual selection styles
            const pane = document.getElementById('question_pane_' + qIndex);
            pane.querySelectorAll('.option-choice-card').forEach(card => card.classList.remove('selected'));
            inputElement.closest('.option-choice-card').classList.add('selected');

            // Mark matrix answered
            const matrixBtn = document.getElementById('matrix_btn_' + qIndex);
            if (matrixBtn) matrixBtn.classList.add('answered');

            // AJAX auto-save
            sendAnswerPayload({
                question_id: questionId,
                selected_option_id: optionId
            }, qIndex);
        }

        // Multi-Select MCQ
        function onSelectMultiOption(qIndex, questionId, inputElement) {
            const card = inputElement.closest('.option-choice-card');
            if (inputElement.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }

            const pane = document.getElementById('question_pane_' + qIndex);
            const selectedCheckboxes = Array.from(pane.querySelectorAll('.answer-checkbox:checked')).map(cb => cb.value);

            const matrixBtn = document.getElementById('matrix_btn_' + qIndex);
            if (matrixBtn) {
                if (selectedCheckboxes.length > 0) matrixBtn.classList.add('answered');
                else matrixBtn.classList.remove('answered');
            }

            sendAnswerPayload({
                question_id: questionId,
                selected_option_ids: selectedCheckboxes
            }, qIndex);
        }

        // Debounced text input saving
        let textSaveTimers = {};
        function onTextInput(qIndex, questionId, text) {
            const matrixBtn = document.getElementById('matrix_btn_' + qIndex);
            if (matrixBtn) {
                if (text.trim().length > 0) matrixBtn.classList.add('answered');
                else matrixBtn.classList.remove('answered');
            }

            clearTimeout(textSaveTimers[questionId]);
            textSaveTimers[questionId] = setTimeout(() => {
                sendAnswerPayload({
                    question_id: questionId,
                    text_answer: text
                }, qIndex);
            }, 800);
        }

        // Send AJAX payload
        function sendAnswerPayload(payload, qIndex) {
            const statusEl = document.getElementById('saveStatus_' + qIndex);
            if (statusEl) statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-primary me-1"></i> Saving...';

            fetch(saveAnswerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (statusEl) {
                    statusEl.innerHTML = '<i class="fa-solid fa-cloud-arrow-up text-success me-1"></i> Saved to cloud';
                }
            })
            .catch(err => {
                if (statusEl) {
                    statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Offline save';
                }
            });
        }

        // Live Countdown Timer
        let remainingSeconds = {{ $attempt->remaining_seconds ?? 0 }};
        const displayEl = document.getElementById('countdownDisplay');

        function updateTimer() {
            if (remainingSeconds <= 0) {
                displayEl.textContent = "00:00:00 (Time's Up)";
                displayEl.classList.add('timer-warning');
                // Auto submit form
                document.getElementById('examForm').submit();
                return;
            }

            const hrs = Math.floor(remainingSeconds / 3600);
            const mins = Math.floor((remainingSeconds % 3600) / 60);
            const secs = remainingSeconds % 60;

            const pad = (n) => String(n).padStart(2, '0');
            displayEl.textContent = `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;

            if (remainingSeconds < 300) { // under 5 mins
                displayEl.classList.add('timer-warning');
            }

            remainingSeconds--;
        }

        @if($attempt->remaining_seconds !== null)
            updateTimer();
            setInterval(updateTimer, 1000);
        @else
            displayEl.textContent = "Untimed";
        @endif
    </script>
</body>
</html>
