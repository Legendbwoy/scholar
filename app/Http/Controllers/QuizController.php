<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Assessment;
use App\Models\Grade;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request, $course_id = null)
    {
        $user = auth()->user();
        $course = null;

        $query = Quiz::with(['course.schoolClass', 'course.subject', 'creator', 'attempts']);

        if ($course_id) {
            $course = Course::with(['schoolClass', 'subject'])->findOrFail($course_id);
            $query->where('course_id', $course_id);
        }

        // Scope for teacher if not admin
        if ($user && $user->isTeacher() && $user->staff) {
            $teacherCourses = Course::where('teacher_id', $user->staff->id)->pluck('id');
            $query->whereIn('course_id', $teacherCourses);
        } elseif ($user && $user->isStudent()) {
            if ($user->student && $user->student->school_class_id) {
                $studentClassId = $user->student->school_class_id;
                $query->whereHas('course', function ($q) use ($studentClassId) {
                    $q->where('school_class_id', $studentClassId);
                })->where('is_published', true);
            } else {
                $query->whereRaw('1=0');
            }
        } elseif ($user && $user->isGuardian() && $user->guardian) {
            $wardClassIds = $user->guardian->students()->pluck('school_class_id')->filter();
            $query->whereHas('course', function ($q) use ($wardClassIds) {
                $q->whereIn('school_class_id', $wardClassIds);
            })->where('is_published', true);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('instructions', 'like', "%{$term}%")
                  ->orWhereHas('course', function ($cq) use ($term) {
                      $cq->where('title', 'like', "%{$term}%");
                  });
            });
        }

        $quizzes = $query->orderBy('created_at', 'desc')->paginate(12);

        // Stats calculation tailored to user role
        if ($user && $user->isStudent()) {
            $studentClassId = $user->student?->school_class_id;
            $courses = $studentClassId ? Course::where('school_class_id', $studentClassId)->orderBy('title')->get() : collect();
            $myQuizIds = $studentClassId ? Quiz::whereHas('course', function ($q) use ($studentClassId) {
                $q->where('school_class_id', $studentClassId);
            })->where('is_published', true)->pluck('id') : collect();

            $totalQuizzes = $myQuizIds->count();
            $totalExams = Quiz::whereIn('id', $myQuizIds)->whereIn('type', ['midterm', 'final_exam', 'exam'])->count();
            $totalAttempts = $user->student ? QuizAttempt::where('student_id', $user->student->id)->count() : 0;
            $activeSessions = $user->student ? QuizAttempt::where('student_id', $user->student->id)->where('status', 'in_progress')->count() : 0;
        } else {
            $totalQuizzes = Quiz::count();
            $totalExams = Quiz::whereIn('type', ['midterm', 'final_exam', 'exam'])->count();
            $totalAttempts = QuizAttempt::count();
            $activeSessions = QuizAttempt::where('status', 'in_progress')->count();
            $courses = Course::orderBy('title')->get();
        }

        return view('portals.lms.quizzes.index', compact('quizzes', 'course', 'courses', 'totalQuizzes', 'totalExams', 'totalAttempts', 'activeSessions'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $course_id = $request->query('course_id');
        $preselectedCourse = $course_id ? Course::find($course_id) : null;

        $coursesQuery = Course::with('schoolClass');
        if ($user && $user->isTeacher() && $user->staff) {
            $coursesQuery->where('teacher_id', $user->staff->id);
        }
        $courses = $coursesQuery->orderBy('title')->get();

        return view('portals.lms.quizzes.create', compact('courses', 'preselectedCourse'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,test,midterm,final_exam,practice',
            'grading_component' => 'required|in:quiz,test,midterm,final_exam,continuous_assessment,practice',
            'duration_minutes' => 'required|integer|min:0|max:360',
            'pass_points' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'instructions' => 'nullable|string',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'auto_submit_on_expiry' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz = new Quiz();
        $quiz->course_id = $validated['course_id'];
        $quiz->teacher_id = $user->staff ? $user->staff->id : null;
        $quiz->created_by = $user->id;
        $quiz->title = $validated['title'];
        $quiz->type = $validated['type'];
        $quiz->grading_component = $validated['grading_component'];
        $quiz->duration_minutes = $validated['duration_minutes'];
        $quiz->total_points = 0; // calculated dynamically as questions are added
        $quiz->pass_points = $validated['pass_points'];
        $quiz->max_attempts = $validated['max_attempts'];
        $quiz->start_time = $validated['start_time'] ?? null;
        $quiz->end_time = $validated['end_time'] ?? null;
        $quiz->instructions = $validated['instructions'] ?? null;
        $quiz->shuffle_questions = $request->boolean('shuffle_questions', true);
        $quiz->shuffle_options = $request->boolean('shuffle_options', true);
        $quiz->auto_submit_on_expiry = $request->boolean('auto_submit_on_expiry', true);
        $quiz->show_correct_answers = $request->boolean('show_correct_answers', true);
        $quiz->is_published = $request->boolean('is_published', true);
        $quiz->save();

        if (class_exists(AuditTrail::class)) {
            AuditTrail::record('LMS Assessment Created', "Created quiz/exam '{$quiz->title}' for Course #{$quiz->course_id}", 'create', $user->id);
        }

        return redirect()->route('lms.quizzes.builder', $quiz->id)->with('success', "Assessment '{$quiz->title}' initialized! Add your questions below.");
    }

    public function edit($id)
    {
        $quiz = Quiz::with('course')->findOrFail($id);
        $courses = Course::with('schoolClass')->orderBy('title')->get();

        return view('portals.lms.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,test,midterm,final_exam,practice',
            'grading_component' => 'required|in:quiz,test,midterm,final_exam,continuous_assessment,practice',
            'duration_minutes' => 'required|integer|min:0|max:360',
            'pass_points' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1|max:10',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'instructions' => 'nullable|string',
        ]);

        $quiz->course_id = $validated['course_id'];
        $quiz->title = $validated['title'];
        $quiz->type = $validated['type'];
        $quiz->grading_component = $validated['grading_component'];
        $quiz->duration_minutes = $validated['duration_minutes'];
        $quiz->pass_points = $validated['pass_points'];
        $quiz->max_attempts = $validated['max_attempts'];
        $quiz->start_time = $validated['start_time'] ?? null;
        $quiz->end_time = $validated['end_time'] ?? null;
        $quiz->instructions = $validated['instructions'] ?? null;
        $quiz->shuffle_questions = $request->boolean('shuffle_questions');
        $quiz->shuffle_options = $request->boolean('shuffle_options');
        $quiz->auto_submit_on_expiry = $request->boolean('auto_submit_on_expiry');
        $quiz->show_correct_answers = $request->boolean('show_correct_answers');
        $quiz->is_published = $request->boolean('is_published');
        $quiz->save();

        return redirect()->route('lms.quizzes.index', ['course_id' => $quiz->course_id])->with('success', "Assessment '{$quiz->title}' updated successfully.");
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $courseId = $quiz->course_id;
        $title = $quiz->title;
        $quiz->delete();

        return redirect()->route('lms.quizzes.index', ['course_id' => $courseId])->with('success', "Assessment '{$title}' removed.");
    }

    public function builder($id)
    {
        $quiz = Quiz::with(['course.schoolClass', 'course.subject', 'questions.options'])->findOrFail($id);
        $banks = \App\Models\QuestionBank::with('questions.options')->orderBy('title')->get();
        return view('portals.lms.quizzes.builder', compact('quiz', 'banks'));
    }

    /**
     * Download CSV template for bulk question onboarding.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lms_questions_import_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'question_type',
            'question_text',
            'points',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_options',
            'acceptable_answers',
            'explanation',
        ];

        $sampleRows = [
            [
                'mcq_single',
                'What is the primary function of RAM in a computing architecture?',
                '10',
                'Stores permanent system BIOS firmware',
                'Provides high-speed volatile working memory for active programs',
                'Acts as secondary offline archival tape storage',
                'Regulates hardware power supply voltages',
                'B',
                '',
                'RAM provides fast read/write memory directly accessible to the CPU for active processes.',
            ],
            [
                'true_false',
                'HTTP protocol encrypts client-server traffic by default without TLS.',
                '10',
                'True',
                'False',
                '',
                '',
                'False',
                '',
                'HTTP transmits data in cleartext; HTTPS utilizing TLS/SSL is required for encrypted transport.',
            ],
            [
                'mcq_multi',
                'Which of the following are relational database management systems? (Select all that apply)',
                '10',
                'PostgreSQL',
                'MySQL',
                'Redis In-Memory Key-Value',
                'SQLite',
                'A,B,D',
                '',
                'PostgreSQL, MySQL, and SQLite adhere to SQL relational tabular architectures.',
            ],
            [
                'short_answer',
                'What standard industry acronym represents Create, Read, Update, and Delete operations?',
                '10',
                '',
                '',
                '',
                '',
                'CRUD',
                'CRUD, crud, C.R.U.D',
                'CRUD represents the four fundamental persistence actions in database storage.',
            ],
            [
                'essay',
                'Explain the difference between synchronous execution and asynchronous event-driven architectures.',
                '15',
                '',
                '',
                '',
                '',
                '',
                '',
                'Synchronous execution blocks subsequent threads until completion; asynchronous delegates background tasks to event loops.',
            ],
        ];

        $callback = function () use ($columns, $sampleRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk upload questions from uploaded CSV/Excel spreadsheet into quiz.
     */
    public function bulkUploadQuestions(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'questions_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('questions_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['csv', 'txt'])) {
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                // Read header
                $header = fgetcsv($handle, 4096, ',');
                if ($header) {
                    $header = array_map(fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h))), $header);
                    while (($data = fgetcsv($handle, 4096, ',')) !== false) {
                        if (count($data) >= 2 && !empty(trim($data[1] ?? ''))) {
                            $row = [];
                            foreach ($header as $idx => $colName) {
                                $row[$colName] = isset($data[$idx]) ? trim($data[$idx]) : '';
                            }
                            $rows[] = $row;
                        }
                    }
                }
                fclose($handle);
            }
        } elseif (in_array($ext, ['xlsx', 'xls']) && class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $sheetData = $sheet->toArray(null, true, true, true);
                if (!empty($sheetData)) {
                    $firstRow = array_shift($sheetData);
                    $headerMap = [];
                    foreach ($firstRow as $colLetter => $colVal) {
                        if ($colVal) {
                            $headerMap[$colLetter] = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$colVal)));
                        }
                    }
                    foreach ($sheetData as $r) {
                        $row = [];
                        foreach ($headerMap as $colLetter => $colName) {
                            $row[$colName] = isset($r[$colLetter]) ? trim((string)$r[$colLetter]) : '';
                        }
                        if (!empty($row['question_text'])) {
                            $rows[] = $row;
                        }
                    }
                }
            } catch (\Throwable $e) {
                return redirect()->back()->withErrors(['error' => 'Failed to parse Excel spreadsheet: ' . $e->getMessage()]);
            }
        }

        if (empty($rows)) {
            return redirect()->back()->withErrors(['error' => 'The uploaded file contains no valid question rows or unreadable header columns.']);
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $currentOrder = $quiz->questions()->max('order_index') ?? 0;

            foreach ($rows as $row) {
                $qType = strtolower(trim($row['question_type'] ?? 'mcq_single'));
                if (!in_array($qType, ['mcq_single', 'mcq_multi', 'true_false', 'short_answer', 'essay'])) {
                    $qType = 'mcq_single';
                }

                $qText = trim($row['question_text'] ?? '');
                if ($qText === '') continue;

                $points = isset($row['points']) && is_numeric($row['points']) ? max(1, (int)$row['points']) : 10;
                $explanation = $row['explanation'] ?? null;
                $acceptableAnswers = $row['acceptable_answers'] ?? null;
                $correctInput = strtoupper(trim($row['correct_options'] ?? ''));

                $currentOrder++;
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_type' => $qType,
                    'question_text' => $qText,
                    'points' => $points,
                    'order_index' => $currentOrder,
                    'explanation' => $explanation,
                    'acceptable_answers' => $acceptableAnswers,
                ]);

                if ($qType === 'true_false') {
                    $isTrueCorrect = in_array($correctInput, ['TRUE', 'T', '1', 'YES', 'A']);
                    QuizQuestionOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => 'True',
                        'is_correct' => $isTrueCorrect,
                        'order_index' => 1,
                    ]);
                    QuizQuestionOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => 'False',
                        'is_correct' => !$isTrueCorrect,
                        'order_index' => 2,
                    ]);
                } elseif (in_array($qType, ['mcq_single', 'mcq_multi'])) {
                    $optA = trim($row['option_a'] ?? '');
                    $optB = trim($row['option_b'] ?? '');
                    $optC = trim($row['option_c'] ?? '');
                    $optD = trim($row['option_d'] ?? '');

                    $optionsMap = [
                        'A' => $optA,
                        'B' => $optB,
                        'C' => $optC,
                        'D' => $optD,
                    ];

                    $correctLetters = array_map('trim', explode(',', $correctInput));

                    $optIdx = 1;
                    foreach ($optionsMap as $letter => $optText) {
                        if ($optText !== '') {
                            $isCorrect = in_array($letter, $correctLetters) || in_array((string)$optIdx, $correctLetters);
                            QuizQuestionOption::create([
                                'quiz_question_id' => $question->id,
                                'option_text' => $optText,
                                'is_correct' => $isCorrect,
                                'order_index' => $optIdx++,
                            ]);
                        }
                    }
                }

                $importedCount++;
            }

            $quiz->recalculateTotalPoints();
            DB::commit();

            return redirect()->route('lms.quizzes.builder', $quiz->id)->with('success', "Bulk Onboarding Complete: Successfully imported {$importedCount} questions into assessment!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Bulk question import failed: ' . $e->getMessage()]);
        }
    }

    public function storeQuestion(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq_single,mcq_multi,true_false,short_answer,essay',
            'points' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
            'acceptable_answers' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_options' => 'nullable|array',
            'tf_correct' => 'nullable|in:true,false',
        ]);

        DB::beginTransaction();
        try {
            $question = new QuizQuestion();
            $question->quiz_id = $quiz->id;
            $question->question_text = $validated['question_text'];
            $question->question_type = $validated['question_type'];
            $question->points = $validated['points'];
            $question->explanation = $validated['explanation'] ?? null;
            $question->order_index = $quiz->questions()->count() + 1;

            if ($validated['question_type'] === 'short_answer') {
                $question->acceptable_answers = $validated['acceptable_answers'] ?? null;
            }

            $question->save();

            // Populate options depending on question type
            if ($validated['question_type'] === 'true_false') {
                $isTrue = ($request->input('tf_correct') === 'true');
                QuizQuestionOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => 'True',
                    'is_correct' => $isTrue,
                    'order_index' => 1,
                ]);
                QuizQuestionOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => 'False',
                    'is_correct' => !$isTrue,
                    'order_index' => 2,
                ]);
            } elseif (in_array($validated['question_type'], ['mcq_single', 'mcq_multi'])) {
                $options = $request->input('options', []);
                $correctIndices = $request->input('correct_options', []);

                $optIndex = 1;
                foreach ($options as $key => $optText) {
                    if (trim($optText) !== '') {
                        $isCorrect = in_array((string)$key, (array)$correctIndices);
                        QuizQuestionOption::create([
                            'quiz_question_id' => $question->id,
                            'option_text' => trim($optText),
                            'is_correct' => $isCorrect,
                            'order_index' => $optIndex++,
                        ]);
                    }
                }
            }

            $quiz->recalculateTotalPoints();
            DB::commit();

            return redirect()->route('lms.quizzes.builder', $quiz->id)->with('success', 'Question successfully added to assessment.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save question: ' . $e->getMessage()]);
        }
    }

    public function destroyQuestion($questionId)
    {
        $question = QuizQuestion::findOrFail($questionId);
        $quiz = $question->quiz;
        $question->delete();

        $quiz->recalculateTotalPoints();

        return redirect()->route('lms.quizzes.builder', $quiz->id)->with('success', 'Question deleted.');
    }

    public function submissions($id)
    {
        $quiz = Quiz::with(['course.schoolClass', 'questions'])->findOrFail($id);

        $attempts = QuizAttempt::with(['student.user', 'answers'])
            ->where('quiz_id', $quiz->id)
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        $totalSubmissions = QuizAttempt::where('quiz_id', $quiz->id)->count();
        $completedCount = QuizAttempt::where('quiz_id', $quiz->id)->whereIn('status', ['completed', 'submitted'])->count();
        $avgScore = QuizAttempt::where('quiz_id', $quiz->id)->where('status', 'completed')->avg('percentage') ?? 0;
        $pendingGrading = QuizAttempt::where('quiz_id', $quiz->id)->where('status', 'submitted')->count();

        return view('portals.lms.quizzes.submissions', compact('quiz', 'attempts', 'totalSubmissions', 'completedCount', 'avgScore', 'pendingGrading'));
    }

    public function gradingView($attemptId)
    {
        $attempt = QuizAttempt::with([
            'quiz.questions.options',
            'student.user',
            'answers.question.options',
            'answers.selectedOption'
        ])->findOrFail($attemptId);

        return view('portals.lms.quizzes.grading', compact('attempt'));
    }

    public function submitGrade(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz', 'answers.question'])->findOrFail($attemptId);

        $pointsInput = $request->input('points', []);
        $feedbackInput = $request->input('feedback', []);

        DB::beginTransaction();
        try {
            foreach ($attempt->answers as $ans) {
                if (isset($pointsInput[$ans->id])) {
                    $awarded = (float) $pointsInput[$ans->id];
                    $maxPts = $ans->question ? $ans->question->points : 10;
                    $ans->awarded_points = min(max(0, $awarded), $maxPts);
                    $ans->is_correct = ($ans->awarded_points >= ($maxPts * 0.5));
                }
                if (isset($feedbackInput[$ans->id])) {
                    $ans->teacher_feedback = $feedbackInput[$ans->id];
                }
                $ans->save();
            }

            // Recalculate total attempt score and set to completed
            $attempt->calculateScore();
            $attempt->status = 'completed';
            $attempt->save();

            DB::commit();

            return redirect()->route('lms.quizzes.submissions', $attempt->quiz_id)->with('success', "Attempt #{$attempt->id} graded and published to gradebook.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Grading submission failed: ' . $e->getMessage()]);
        }
    }
}
