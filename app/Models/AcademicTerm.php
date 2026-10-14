<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'term_code',
        'start_date',
        'end_date',
        'instructional_days_target',
        'grade_submission_deadline',
        'is_current',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'grade_submission_deadline' => 'date',
        'is_current' => 'boolean',
        'instructional_days_target' => 'integer',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public static function current()
    {
        return static::where('is_current', true)->first()
            ?? static::where('status', 'active')->first();
    }

    /**
     * Determine if a specific date is an active instructional day.
     */
    public function isDateInstructional($date, $cohortId = null): array
    {
        $d = Carbon::parse($date);

        // 1. Check if date falls within term bounds
        if ($d->lt($this->start_date) || $d->gt($this->end_date)) {
            return [
                'is_instructional' => false,
                'reason' => 'Outside Academic Term Dates',
                'type' => 'term_boundary',
                'holiday' => null,
            ];
        }

        // 2. Check weekly schedule (e.g. Sat/Sun)
        $dayOfWeek = $d->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $sched = SchoolWeeklySchedule::where(function ($q) {
            $q->where('academic_year_id', $this->academic_year_id)
              ->orWhereNull('academic_year_id');
        })->where('day_of_week', $dayOfWeek)->first();

        if ($sched && !$sched->is_instructional) {
            return [
                'is_instructional' => false,
                'reason' => "Weekend / Non-Instructional Day ({$sched->day_name})",
                'type' => 'weekend',
                'holiday' => null,
            ];
        }

        // 3. Check Holidays
        $holiday = SchoolCalendarHoliday::where(function ($q) {
            $q->where('academic_year_id', $this->academic_year_id)
              ->orWhereNull('academic_year_id');
        })
        ->whereDate('start_date', '<=', $d->toDateString())
        ->whereDate('end_date', '>=', $d->toDateString())
        ->first();

        if ($holiday) {
            $affects = $holiday->affects_cohorts;
            $applies = true;
            if ($cohortId && is_array($affects) && !in_array('all', $affects) && !in_array($cohortId, $affects)) {
                $applies = false;
            }

            if ($applies) {
                return [
                    'is_instructional' => false,
                    'reason' => "Holiday: {$holiday->title} (" . ucfirst(str_replace('_', ' ', $holiday->type)) . ")",
                    'type' => 'holiday',
                    'holiday' => $holiday,
                ];
            }
        }

        return [
            'is_instructional' => true,
            'reason' => 'Official Instructional Day',
            'type' => 'instructional',
            'holiday' => null,
        ];
    }

    /**
     * Compute exact count of valid instructional days from term start up to a target date.
     */
    public function getInstructionalDaysCount($upToDate = null, $cohortId = null): int
    {
        $start = $this->start_date;
        $end = $upToDate ? Carbon::parse($upToDate) : min(now(), $this->end_date);

        if ($end->lt($start)) {
            return 0;
        }

        // Query non-instructional days of week
        $nonInstructionalDays = SchoolWeeklySchedule::where(function ($q) {
            $q->where('academic_year_id', $this->academic_year_id)
              ->orWhereNull('academic_year_id');
        })->where('is_instructional', false)->pluck('day_of_week')->toArray();

        // Query all holidays within the window
        $holidays = SchoolCalendarHoliday::where(function ($q) {
            $q->where('academic_year_id', $this->academic_year_id)
              ->orWhereNull('academic_year_id');
        })
        ->whereDate('start_date', '<=', $end->toDateString())
        ->whereDate('end_date', '>=', $start->toDateString())
        ->get();

        $count = 0;
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            // Skip non-instructional weekdays
            if (in_array($date->dayOfWeekIso, $nonInstructionalDays)) {
                continue;
            }

            // Check if covered by holiday
            $isHoliday = false;
            foreach ($holidays as $h) {
                if ($date->betweenIncluded($h->start_date, $h->end_date)) {
                    $affects = $h->affects_cohorts;
                    if (!$cohortId || !is_array($affects) || in_array('all', $affects) || in_array($cohortId, $affects)) {
                        $isHoliday = true;
                        break;
                    }
                }
            }

            if (!$isHoliday) {
                $count++;
            }
        }

        return $count;
    }
}
