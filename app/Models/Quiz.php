<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'title',
        'type',
        'instructions',
        'duration_minutes',
        'start_time',
        'end_time',
        'total_points',
        'pass_points',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'auto_submit_on_expiry',
        'show_correct_answers',
        'is_published',
        'grading_component',
        'created_by',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'total_points' => 'integer',
        'pass_points' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'auto_submit_on_expiry' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_published' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_index');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Helper to compute total points dynamically from question bank.
     */
    public function recalculateTotalPoints(): int
    {
        $sum = (int) $this->questions()->sum('points');
        $this->update(['total_points' => $sum > 0 ? $sum : 100]);
        return $this->total_points;
    }

    /**
     * Check if quiz is currently active for students to take.
     */
    public function isAvailable(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();
        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    /**
     * Get visual badge class for assessment type.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'quiz' => 'bg-info text-dark',
            'test' => 'bg-primary text-white',
            'midterm' => 'bg-warning text-dark',
            'final_exam' => 'bg-danger text-white',
            'practice' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get full visual metadata (label, class, icon) for assessment type.
     */
    public function getTypeMetaAttribute(): array
    {
        return match ($this->type) {
            'quiz' => ['label' => 'Quiz', 'class' => 'bg-info text-dark', 'icon' => 'fa-solid fa-stopwatch'],
            'test' => ['label' => 'Unit Test', 'class' => 'bg-primary text-white', 'icon' => 'fa-solid fa-file-lines'],
            'midterm' => ['label' => 'Midterm Exam', 'class' => 'bg-warning text-dark', 'icon' => 'fa-solid fa-award'],
            'final_exam' => ['label' => 'Final Exam', 'class' => 'bg-danger text-white', 'icon' => 'fa-solid fa-graduation-cap'],
            'practice' => ['label' => 'Practice Drill', 'class' => 'bg-success text-white', 'icon' => 'fa-solid fa-dumbbell'],
            default => ['label' => ucfirst($this->type), 'class' => 'bg-secondary text-white', 'icon' => 'fa-solid fa-question'],
        };
    }
}
