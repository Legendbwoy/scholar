<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'total_score',
        'percentage',
        'status',
        'teacher_feedback',
        'graded_by',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'total_score' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Compute remaining time in seconds based on quiz duration and started_at.
     */
    public function getRemainingSecondsAttribute(): int
    {
        $hardEnd = $this->quiz->end_time;

        if (!$this->quiz->duration_minutes || $this->quiz->duration_minutes <= 0) {
            if ($hardEnd) {
                return max(0, (int) now()->diffInSeconds($hardEnd, false));
            }
            return 999999; // untimed
        }

        $durationEnd = $this->started_at->copy()->addMinutes($this->quiz->duration_minutes);
        $targetExpiry = ($hardEnd && $hardEnd->lt($durationEnd)) ? $hardEnd : $durationEnd;
        $diff = now()->diffInSeconds($targetExpiry, false);
        return max(0, (int) $diff);
    }

    /**
     * Check if attempt has exceeded time limit.
     */
    public function isExpired(): bool
    {
        if (!$this->quiz->duration_minutes || $this->quiz->duration_minutes <= 0) {
            return false;
        }

        return $this->remaining_seconds <= 0;
    }

    /**
     * Recompute total score and percentage from recorded answers.
     */
    public function calculateScore(): void
    {
        $awarded = (float) $this->answers()->sum('score_awarded');
        $quizTotal = (float) ($this->quiz->total_points > 0 ? $this->quiz->total_points : 100);
        $pct = round(($awarded / $quizTotal) * 100, 2);

        $hasPendingEssays = $this->answers()->whereHas('question', function ($q) {
            $q->where('question_type', 'essay');
        })->whereNull('teacher_comment')->where('score_awarded', 0)->exists();

        $this->update([
            'total_score' => $awarded,
            'percentage' => $pct,
            'status' => $hasPendingEssays ? 'submitted' : 'graded',
            'submitted_at' => $this->submitted_at ?? now(),
        ]);
    }

    public function getScoreAttribute()
    {
        return $this->total_score;
    }

    public function setScoreAttribute($value)
    {
        $this->attributes['total_score'] = $value;
    }
}
