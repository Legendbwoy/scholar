<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolWeeklySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'day_of_week',
        'day_name',
        'is_instructional',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_instructional' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
