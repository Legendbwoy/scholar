<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'selected_option_id',
        'selected_option_ids',
        'text_answer',
        'score_awarded',
        'is_correct',
        'teacher_comment',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'score_awarded' => 'decimal:2',
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuizQuestionOption::class, 'selected_option_id');
    }

    /**
     * Automatic evaluator for objective questions.
     */
    public function autoEvaluate(): void
    {
        $q = $this->question;
        $maxPoints = (float) $q->points;

        if ($q->question_type === 'mcq_single' || $q->question_type === 'true_false') {
            $opt = QuizQuestionOption::find($this->selected_option_id);
            $correct = $opt && $opt->is_correct;
            $this->update([
                'is_correct' => $correct,
                'score_awarded' => $correct ? $maxPoints : 0,
            ]);
        } elseif ($q->question_type === 'mcq_multi') {
            $correctOptionIds = $q->options()->where('is_correct', true)->pluck('id')->sort()->values()->toArray();
            $studentIds = is_array($this->selected_option_ids) ? collect($this->selected_option_ids)->map(fn($v) => (int)$v)->sort()->values()->toArray() : [];
            $correct = ($correctOptionIds === $studentIds);
            $this->update([
                'is_correct' => $correct,
                'score_awarded' => $correct ? $maxPoints : 0,
            ]);
        } elseif ($q->question_type === 'short_answer') {
            $studentText = trim(strtolower($this->text_answer ?? ''));
            $acceptable = array_map('trim', explode(',', strtolower($q->acceptable_answers ?? '')));
            $correct = in_array($studentText, $acceptable, true);
            $this->update([
                'is_correct' => $correct,
                'score_awarded' => $correct ? $maxPoints : 0,
            ]);
        }
        // Essay questions require manual teacher grading
    }

    public function getAwardedPointsAttribute()
    {
        return $this->score_awarded;
    }

    public function setAwardedPointsAttribute($value)
    {
        $this->attributes['score_awarded'] = $value;
    }

    public function getQuizQuestionOptionIdAttribute()
    {
        return $this->selected_option_id;
    }

    public function setQuizQuestionOptionIdAttribute($value)
    {
        $this->attributes['selected_option_id'] = $value;
    }

    public function getSelectedOptionsAttribute()
    {
        $val = $this->attributes['selected_option_ids'] ?? null;
        if (is_string($val)) {
            return json_decode($val, true);
        }
        return $val;
    }

    public function setSelectedOptionsAttribute($value)
    {
        $this->attributes['selected_option_ids'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getTeacherFeedbackAttribute()
    {
        return $this->teacher_comment;
    }

    public function setTeacherFeedbackAttribute($value)
    {
        $this->attributes['teacher_comment'] = $value;
    }
}
