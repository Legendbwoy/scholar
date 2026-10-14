<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_type',
        'question_text',
        'points',
        'order_index',
        'explanation',
        'acceptable_answers',
    ];

    protected $casts = [
        'points' => 'integer',
        'order_index' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizQuestionOption::class)->orderBy('order_index');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Get label & icon for question type.
     */
    public function getTypeMetaAttribute(): array
    {
        return match ($this->question_type) {
            'mcq_single' => ['label' => 'Multiple Choice (Single)', 'icon' => 'fa-regular fa-circle-dot'],
            'mcq_multi' => ['label' => 'Multiple Choice (Multi-Select)', 'icon' => 'fa-regular fa-square-check'],
            'true_false' => ['label' => 'True / False', 'icon' => 'fa-solid fa-toggle-on'],
            'short_answer' => ['label' => 'Short Answer / Fill Blank', 'icon' => 'fa-solid fa-i-cursor'],
            'essay' => ['label' => 'Essay / Long Form', 'icon' => 'fa-solid fa-paragraph'],
            default => ['label' => ucfirst($this->question_type), 'icon' => 'fa-solid fa-question'],
        };
    }
}
