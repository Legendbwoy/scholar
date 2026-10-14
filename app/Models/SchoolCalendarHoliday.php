<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCalendarHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'title',
        'type',
        'start_date',
        'end_date',
        'is_recurring_yearly',
        'affects_cohorts',
        'attendance_policy',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring_yearly' => 'boolean',
        'affects_cohorts' => 'array',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
