<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'delivery_mode',
        'category_group',
        'term',
        'academic_year',
        'school_class_id',
        'subject_id',
        'teacher_id',
        'max_marks',
        'weightage',
        'weightage_in_component',
        'date',
        'status',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'max_marks' => 'integer',
        'weightage' => 'integer',
        'weightage_in_component' => 'integer',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function averageScore()
    {
        return round($this->grades()->avg('score') ?? 0, 1);
    }

    public function highestScore()
    {
        return $this->grades()->max('score') ?? 0;
    }

    public function lowestScore()
    {
        return $this->grades()->min('score') ?? 0;
    }
}
