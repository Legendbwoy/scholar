<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use App\Models\SchoolCalendarHoliday;
use App\Models\SchoolWeeklySchedule;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\AuditTrail;
use Carbon\Carbon;

class AcademicCalendarController extends Controller
{
    /**
     * Display Academic Calendar, Terms, Holidays & Working Days Hub.
     */
    public function index(Request $request)
    {
        $currentYear = AcademicYear::current();
        $years = AcademicYear::with(['terms', 'weeklySchedules'])->orderBy('start_date', 'desc')->get();

        $selectedYearId = $request->get('year_id', $currentYear?->id);
        $selectedYear = AcademicYear::with(['terms', 'weeklySchedules'])->find($selectedYearId) ?? $currentYear;

        $terms = $selectedYear ? $selectedYear->terms()->orderBy('start_date')->get() : collect();
        $schedules = $selectedYear ? $selectedYear->weeklySchedules()->orderBy('day_of_week')->get() : collect();

        // Query holidays for this year
        $holidays = SchoolCalendarHoliday::where(function ($q) use ($selectedYear) {
            if ($selectedYear) {
                $q->where('academic_year_id', $selectedYear->id)
                  ->orWhereNull('academic_year_id');
            }
        })->orderBy('start_date')->get();

        // Calculate instructional stats per term
        $termStats = [];
        foreach ($terms as $term) {
            $instructionalDaysToDate = $term->getInstructionalDaysCount();
            $totalProjectedDays = $term->getInstructionalDaysCount($term->end_date);
            $termStats[$term->id] = [
                'days_to_date' => $instructionalDaysToDate,
                'total_projected' => $totalProjectedDays,
                'progress_pct' => $totalProjectedDays > 0 ? min(100, round(($instructionalDaysToDate / $totalProjectedDays) * 100)) : 0,
            ];
        }

        $classes = SchoolClass::orderBy('numeric_grade')->get();

        return view('portals.academic.calendar', compact(
            'currentYear', 'selectedYear', 'years', 'terms', 'schedules', 'holidays', 'termStats', 'classes'
        ));
    }

    /**
     * Store a new holiday or school closure.
     */
    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'title' => 'required|string|max:150',
            'type' => 'required|in:national_holiday,regional_holiday,institutional_holiday,recess,emergency_closure',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_recurring_yearly' => 'boolean',
            'affects_cohorts' => 'nullable|array',
            'attendance_policy' => 'required|in:closed_no_register,excused_auto,special_session',
            'description' => 'nullable|string',
        ]);

        $validated['is_recurring_yearly'] = $request->has('is_recurring_yearly');
        if (empty($validated['affects_cohorts'])) {
            $validated['affects_cohorts'] = ['all'];
        }

        $holiday = SchoolCalendarHoliday::create($validated);

        AuditTrail::log(
            'holiday_created',
            "Registered calendar observance '{$holiday->title}' from {$holiday->start_date->format('M d, Y')} to {$holiday->end_date->format('M d, Y')} ({$holiday->type})",
            'school_calendar_holidays',
            $holiday->id
        );

        return redirect()->back()->with('success', "Calendar holiday '{$holiday->title}' added successfully.");
    }

    /**
     * Remove a holiday from the academic calendar.
     */
    public function destroyHoliday($id)
    {
        $holiday = SchoolCalendarHoliday::findOrFail($id);
        $title = $holiday->title;
        $holiday->delete();

        AuditTrail::log(
            'holiday_deleted',
            "Removed calendar observance '{$title}' from the academic schedule",
            'school_calendar_holidays',
            $id
        );

        return redirect()->back()->with('success', "Holiday '{$title}' removed from the academic calendar.");
    }

    /**
     * Store or update an academic term.
     */
    public function storeTerm(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:50',
            'term_code' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'instructional_days_target' => 'required|integer|min:10|max:200',
            'grade_submission_deadline' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'status' => 'required|in:planned,active,grading,closed',
        ]);

        if ($request->has('is_current')) {
            AcademicTerm::where('academic_year_id', $validated['academic_year_id'])->update(['is_current' => false]);
            $validated['is_current'] = true;
        }

        $term = AcademicTerm::updateOrCreate(
            ['academic_year_id' => $validated['academic_year_id'], 'term_code' => $validated['term_code']],
            $validated
        );

        AuditTrail::log(
            'term_configured',
            "Configured academic period '{$term->name}' ({$term->start_date->format('M d')} to {$term->end_date->format('M d, Y')})",
            'academic_terms',
            $term->id
        );

        return redirect()->back()->with('success', "Academic term '{$term->name}' configured successfully.");
    }

    /**
     * Update weekly instructional schedule.
     */
    public function updateSchedule(Request $request)
    {
        $days = $request->input('days', []);
        $yearId = $request->input('academic_year_id');

        foreach ($days as $dayNum => $data) {
            SchoolWeeklySchedule::updateOrCreate(
                [
                    'academic_year_id' => $yearId,
                    'day_of_week' => (int) $dayNum,
                ],
                [
                    'day_name' => $data['name'],
                    'is_instructional' => isset($data['is_instructional']),
                    'start_time' => $data['start_time'] ?? '08:00:00',
                    'end_time' => $data['end_time'] ?? '15:30:00',
                ]
            );
        }

        AuditTrail::log(
            'weekly_schedule_updated',
            "Updated weekly institutional instructional days and hours",
            'school_weekly_schedules'
        );

        return redirect()->back()->with('success', "Weekly instructional operating schedule updated.");
    }

    /**
     * Display Course Dual-Grading Policies Hub.
     */
    public function gradingPolicies()
    {
        $courses = Course::with(['subject', 'schoolClass', 'teacher.user'])->get();
        return view('portals.academic.grading_policies', compact('courses'));
    }

    /**
     * Update course dual-grading scheme (LMS weight vs Offline weight).
     */
    public function updateCourseGradingPolicy(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'grading_mode' => 'required|in:dual,offline_only,lms_only',
            'lms_weight' => 'required|integer|min:0|max:100',
            'offline_weight' => 'required|integer|min:0|max:100',
        ]);

        if ($validated['grading_mode'] === 'dual') {
            if (($validated['lms_weight'] + $validated['offline_weight']) !== 100) {
                return redirect()->back()->withErrors([
                    'lms_weight' => 'LMS component weight and Offline coursework weight must equal exactly 100%.',
                ]);
            }
        } elseif ($validated['grading_mode'] === 'offline_only') {
            $validated['lms_weight'] = 0;
            $validated['offline_weight'] = 100;
        } elseif ($validated['grading_mode'] === 'lms_only') {
            $validated['lms_weight'] = 100;
            $validated['offline_weight'] = 0;
        }

        $course->update($validated);

        AuditTrail::log(
            'course_grading_policy_updated',
            "Configured grading mode for course '{$course->title}' ({$course->code}) to {$course->grading_mode} (LMS: {$course->lms_weight}%, Offline: {$course->offline_weight}%)",
            'courses',
            $course->id
        );

        return redirect()->back()->with('success', "Grading policy for '{$course->title}' updated: LMS {$course->lms_weight}% &bull; Offline {$course->offline_weight}%.");
    }
}
