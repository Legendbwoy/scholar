<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'term',
        'academic_year',
        'conduct',
        'teacher_remarks',
        'principal_remarks',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
