<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'question_type',
        'question_text',
        'points',
        'explanation',
        'acceptable_answers',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function options()
    {
        return $this->hasMany(BankQuestionOption::class)->orderBy('order_index');
    }
}
