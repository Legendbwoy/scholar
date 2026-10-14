<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_question_id',
        'option_text',
        'is_correct',
        'order_index',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order_index' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(BankQuestion::class, 'bank_question_id');
    }
}
