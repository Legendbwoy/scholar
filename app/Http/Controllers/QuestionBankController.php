<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionBank;
use App\Models\BankQuestion;
use App\Models\BankQuestionOption;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = QuestionBank::with(['subject', 'course', 'creator', 'questions.options']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('topic', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $banks = $query->orderBy('created_at', 'desc')->paginate(12);

        $subjects = Subject::orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        $totalBanks = QuestionBank::count();
        $totalQuestions = BankQuestion::count();

        return view('portals.lms.banks.index', compact('banks', 'subjects', 'courses', 'totalBanks', 'totalQuestions'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        return view('portals.lms.banks.create', compact('subjects', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string',
        ]);

        $bank = QuestionBank::create([
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?? null,
            'subject_id' => $validated['subject_id'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
            'teacher_id' => auth()->user()->staff?->id,
            'created_by' => auth()->id(),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('lms.banks.show', $bank->id)->with('success', "Question Bank '{$bank->title}' created! Add or import questions below.");
    }

    public function show($id)
    {
        $bank = QuestionBank::with(['subject', 'course', 'questions.options'])->findOrFail($id);
        $quizzes = Quiz::where('course_id', $bank->course_id)->orWhereNull('course_id')->orderBy('title')->get();

        return view('portals.lms.banks.show', compact('bank', 'quizzes'));
    }

    public function storeQuestion(Request $request, $id)
    {
        $bank = QuestionBank::findOrFail($id);

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
            $question = BankQuestion::create([
                'question_bank_id' => $bank->id,
                'question_type' => $validated['question_type'],
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'acceptable_answers' => ($validated['question_type'] === 'short_answer') ? ($validated['acceptable_answers'] ?? null) : null,
            ]);

            if ($validated['question_type'] === 'true_false') {
                $isTrue = ($request->input('tf_correct') === 'true');
                BankQuestionOption::create([
                    'bank_question_id' => $question->id,
                    'option_text' => 'True',
                    'is_correct' => $isTrue,
                    'order_index' => 1,
                ]);
                BankQuestionOption::create([
                    'bank_question_id' => $question->id,
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
                        BankQuestionOption::create([
                            'bank_question_id' => $question->id,
                            'option_text' => trim($optText),
                            'is_correct' => $isCorrect,
                            'order_index' => $optIndex++,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('lms.banks.show', $bank->id)->with('success', 'Question added to Question Bank.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save question: ' . $e->getMessage()]);
        }
    }

    public function destroyQuestion($questionId)
    {
        $question = BankQuestion::findOrFail($questionId);
        $bankId = $question->question_bank_id;
        $question->delete();

        return redirect()->route('lms.banks.show', $bankId)->with('success', 'Question deleted from bank.');
    }

    public function destroy($id)
    {
        $bank = QuestionBank::findOrFail($id);
        $title = $bank->title;
        $bank->delete();

        return redirect()->route('lms.banks.index')->with('success', "Question Bank '{$title}' removed.");
    }

    /**
     * Save questions from an active Quiz into a Question Bank.
     */
    public function saveQuizToBank(Request $request, $quizId)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);

        $bankId = $request->input('question_bank_id');
        $newBankTitle = $request->input('new_bank_title');

        DB::beginTransaction();
        try {
            if ($bankId) {
                $bank = QuestionBank::findOrFail($bankId);
            } else {
                $bank = QuestionBank::create([
                    'title' => $newBankTitle ?: "{$quiz->title} - Bank",
                    'course_id' => $quiz->course_id,
                    'subject_id' => $quiz->course?->subject_id,
                    'teacher_id' => auth()->user()->staff?->id,
                    'created_by' => auth()->id(),
                    'description' => "Exported from assessment '{$quiz->title}' on " . now()->format('M d, Y'),
                ]);
            }

            $count = 0;
            foreach ($quiz->questions as $q) {
                $bankQ = BankQuestion::create([
                    'question_bank_id' => $bank->id,
                    'question_type' => $q->question_type,
                    'question_text' => $q->question_text,
                    'points' => $q->points,
                    'explanation' => $q->explanation,
                    'acceptable_answers' => $q->acceptable_answers,
                ]);

                foreach ($q->options as $opt) {
                    BankQuestionOption::create([
                        'bank_question_id' => $bankQ->id,
                        'option_text' => $opt->option_text,
                        'is_correct' => $opt->is_correct,
                        'order_index' => $opt->order_index,
                    ]);
                }
                $count++;
            }

            DB::commit();
            return redirect()->back()->with('success', "Successfully exported {$count} questions to '{$bank->title}' for future reuse!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Import selected questions from Question Bank into a Quiz.
     */
    public function importFromBank(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $questionIds = $request->input('bank_question_ids', []);

        if (empty($questionIds)) {
            return redirect()->back()->withErrors(['error' => 'No questions selected for import.']);
        }

        $bankQuestions = BankQuestion::with('options')->whereIn('id', $questionIds)->get();

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $currentOrder = $quiz->questions()->max('order_index') ?? 0;

            foreach ($bankQuestions as $bq) {
                $currentOrder++;
                $newQ = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_type' => $bq->question_type,
                    'question_text' => $bq->question_text,
                    'points' => $bq->points,
                    'order_index' => $currentOrder,
                    'explanation' => $bq->explanation,
                    'acceptable_answers' => $bq->acceptable_answers,
                ]);

                foreach ($bq->options as $bOpt) {
                    QuizQuestionOption::create([
                        'quiz_question_id' => $newQ->id,
                        'option_text' => $bOpt->option_text,
                        'is_correct' => $bOpt->is_correct,
                        'order_index' => $bOpt->order_index,
                    ]);
                }
                $importedCount++;
            }

            $quiz->recalculateTotalPoints();
            DB::commit();

            return redirect()->route('lms.quizzes.builder', $quiz->id)->with('success', "Imported {$importedCount} questions from Question Bank into assessment.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }
}
