<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuizPlayerController extends Controller
{
    public function start(Request $request, $quizId)
    {
        $user = auth()->user();
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        // Determine student record
        $student = null;
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                return redirect()->back()->withErrors(['error' => 'No student profile linked to your user account.']);
            }
        } else {
            // For teacher or admin preview testing, find or pick a student reference
            $student = Student::first();
        }

        // 1. Publishing check
        if (!$quiz->is_published && !$user->isAdmin() && !$user->isTeacher()) {
            return redirect()->back()->withErrors(['error' => 'This assessment is currently in draft mode and has not been published by your instructor.']);
        }

        // 2. Future Access Window Lock
        if ($quiz->start_time && now()->lt($quiz->start_time) && !$user->isAdmin() && !$user->isTeacher()) {
            return redirect()->back()->withErrors(['error' => "Assessment Scheduled: Access doors open on " . $quiz->start_time->format('M d, Y \a\t h:i A') . " (" . $quiz->start_time->diffForHumans() . "). Please return during the scheduled window."]);
        }

        // 3. Past Access Window Expiry
        if ($quiz->end_time && now()->gt($quiz->end_time) && !$user->isAdmin() && !$user->isTeacher()) {
            return redirect()->back()->withErrors(['error' => "Testing Window Closed: The submission deadline for this assessment was " . $quiz->end_time->format('M d, Y \a\t h:i A') . "."]);
        }

        // Check if student already has an active in_progress attempt
        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            if ($activeAttempt->isExpired()) {
                $this->finalizeAttempt($activeAttempt, 'timed_out');
                return redirect()->route('lms.quizzes.results', $activeAttempt->id)->with('info', 'Your previous session timed out and was submitted.');
            }
            return redirect()->route('lms.quizzes.take', $activeAttempt->id);
        }

        // Check attempt count
        $completedAttemptsCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereIn('status', ['completed', 'submitted', 'timed_out'])
            ->count();

        if ($completedAttemptsCount >= $quiz->max_attempts && !$user->isAdmin()) {
            $latest = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->latest()
                ->first();
            return redirect()->route('lms.quizzes.results', $latest->id)->with('info', "Maximum attempt limit ({$quiz->max_attempts}) reached.");
        }

        // Create new attempt
        $attempt = new QuizAttempt();
        $attempt->quiz_id = $quiz->id;
        $attempt->student_id = $student->id;
        $attempt->attempt_number = $completedAttemptsCount + 1;
        $attempt->started_at = now();
        $attempt->expires_at = $quiz->duration_minutes > 0 ? now()->addMinutes($quiz->duration_minutes) : null;
        $attempt->status = 'in_progress';
        $attempt->score = 0;
        $attempt->percentage = 0;
        $attempt->save();

        // Seed initial blank answer records
        foreach ($quiz->questions as $question) {
            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $question->id,
                'is_correct' => false,
                'awarded_points' => 0,
            ]);
        }

        return redirect()->route('lms.quizzes.take', $attempt->id);
    }

    public function take($attemptId)
    {
        $user = auth()->user();
        $attempt = QuizAttempt::with([
            'quiz.questions.options',
            'student.user',
            'answers'
        ])->findOrFail($attemptId);

        // Security check
        if ($user->isStudent() && $user->student && $attempt->student_id !== $user->student->id) {
            abort(403, 'Unauthorized access to this examination session.');
        }

        // If expired, auto-submit
        if ($attempt->isExpired() && $attempt->status === 'in_progress') {
            $this->finalizeAttempt($attempt, 'timed_out');
            return redirect()->route('lms.quizzes.results', $attempt->id)->with('info', 'Your time limit elapsed. Assessment automatically submitted.');
        }

        if (in_array($attempt->status, ['completed', 'submitted', 'timed_out'])) {
            return redirect()->route('lms.quizzes.results', $attempt->id);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions;

        // Shuffle questions if configured
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Map existing answers keyed by question_id
        $answersMap = $attempt->answers->keyBy('quiz_question_id');

        return view('portals.lms.quizzes.player', compact('attempt', 'quiz', 'questions', 'answersMap'));
    }

    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);

        if ($attempt->status !== 'in_progress' || $attempt->isExpired()) {
            return response()->json(['status' => 'error', 'message' => 'Assessment session has expired or concluded.'], 403);
        }

        $questionId = $request->input('question_id');
        $selectedOptionId = $request->input('selected_option_id');
        $selectedOptionIds = $request->input('selected_option_ids');
        $textAnswer = $request->input('text_answer');

        $answer = QuizAnswer::firstOrNew([
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $questionId,
        ]);

        if ($selectedOptionId) {
            $answer->quiz_question_option_id = $selectedOptionId;
        }

        if ($selectedOptionIds) {
            $answer->selected_options = is_array($selectedOptionIds) ? $selectedOptionIds : [$selectedOptionIds];
        }

        if ($textAnswer !== null) {
            $answer->text_answer = $textAnswer;
        }

        $answer->save();

        return response()->json([
            'status' => 'success',
            'remaining_seconds' => $attempt->remaining_seconds,
        ]);
    }

    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions.options', 'answers.question.options'])->findOrFail($attemptId);

        if ($attempt->status === 'in_progress') {
            // Save any submitted form payload answers if direct POST
            if ($request->has('answers')) {
                foreach ($request->input('answers') as $qId => $val) {
                    $ans = QuizAnswer::firstOrNew([
                        'quiz_attempt_id' => $attempt->id,
                        'quiz_question_id' => $qId,
                    ]);

                    if (is_array($val)) {
                        $ans->selected_options = $val;
                    } elseif (is_numeric($val)) {
                        $ans->quiz_question_option_id = $val;
                    } else {
                        $ans->text_answer = $val;
                    }
                    $ans->save();
                }
            }

            $this->finalizeAttempt($attempt, 'submitted');
        }

        return redirect()->route('lms.quizzes.results', $attempt->id)->with('success', 'Your assessment has been officially submitted!');
    }

    public function results($attemptId)
    {
        $user = auth()->user();
        $attempt = QuizAttempt::with([
            'quiz.course.schoolClass',
            'quiz.questions.options',
            'student.user',
            'answers.question.options',
            'answers.selectedOption'
        ])->findOrFail($attemptId);

        // Security check
        if ($user->isStudent() && $user->student && $attempt->student_id !== $user->student->id) {
            abort(403, 'Unauthorized access to this examination report.');
        }

        $quiz = $attempt->quiz;

        return view('portals.lms.quizzes.results', compact('attempt', 'quiz'));
    }

    private function finalizeAttempt(QuizAttempt $attempt, string $status = 'completed')
    {
        $attempt->loadMissing(['quiz.questions.options', 'answers.question.options']);

        $hasEssayQuestions = false;

        // Auto evaluate objective questions
        foreach ($attempt->answers as $answer) {
            if ($answer->question && $answer->question->question_type === 'essay') {
                $hasEssayQuestions = true;
                continue;
            }
            $answer->autoEvaluate();
        }

        $attempt->calculateScore();
        $attempt->submitted_at = now();

        // If essay questions are present, status is 'submitted' awaiting teacher review
        if ($hasEssayQuestions && $status !== 'timed_out') {
            $attempt->status = 'submitted';
        } else {
            $attempt->status = 'completed';
        }

        $attempt->save();
    }
}
