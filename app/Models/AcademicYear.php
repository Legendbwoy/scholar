<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function terms()
    {
        return $this->hasMany(AcademicTerm::class);
    }

    public function holidays()
    {
        return $this->hasMany(SchoolCalendarHoliday::class);
    }

    public function weeklySchedules()
    {
        return $this->hasMany(SchoolWeeklySchedule::class);
    }

    public function currentTerm()
    {
        return $this->hasOne(AcademicTerm::class)->where('is_current', true);
    }

    public static function current()
    {
        return static::where('is_current', true)->first() 
            ?? static::where('status', 'active')->latest('start_date')->first();
    }
}
