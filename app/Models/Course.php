<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'school_class_id',
        'teacher_id',
        'title',
        'code',
        'semester',
        'description',
        'thumbnail',
        'category',
        'grading_mode',
        'lms_weight',
        'offline_weight',
        'total_lessons',
        'is_published',
    ];

    protected $casts = [
        'lms_weight' => 'integer',
        'offline_weight' => 'integer',
        'total_lessons' => 'integer',
        'is_published' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }
}