<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_id',
        'designation',
        'qualification',
        'joining_date',
        'specialization',
        'emergency_contact',
        'salary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function classesAsTeacher()
    {
        return $this->hasMany(SchoolClass::class, 'class_teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'teacher_id');
    }
}