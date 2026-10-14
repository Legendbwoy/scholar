<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\AcademicTerm;
use App\Models\SchoolCalendarHoliday;
use App\Models\SchoolWeeklySchedule;
use App\Models\Course;
use App\Models\Assessment;

class AcademicCalendarAndGradingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Current Academic Year
        $year = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-08-24',
                'end_date' => '2027-06-18',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // 2. Terms
        $terms = [
            [
                'name' => 'Term 1 (Fall)',
                'term_code' => 'T1-2026',
                'start_date' => '2026-08-24',
                'end_date' => '2026-12-18',
                'instructional_days_target' => 82,
                'grade_submission_deadline' => '2026-12-22',
                'is_current' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Term 2 (Spring)',
                'term_code' => 'T2-2027',
                'start_date' => '2027-01-11',
                'end_date' => '2027-04-16',
                'instructional_days_target' => 68,
                'grade_submission_deadline' => '2027-04-20',
                'is_current' => false,
                'status' => 'planned',
            ],
            [
                'name' => 'Term 3 (Summer)',
                'term_code' => 'T3-2027',
                'start_date' => '2027-04-26',
                'end_date' => '2027-06-18',
                'instructional_days_target' => 40,
                'grade_submission_deadline' => '2027-06-22',
                'is_current' => false,
                'status' => 'planned',
            ],
        ];

        foreach ($terms as $t) {
            AcademicTerm::updateOrCreate(
                ['academic_year_id' => $year->id, 'term_code' => $t['term_code']],
                $t
            );
        }

        // 3. Weekly Schedule (Mon-Fri active, Sat-Sun off)
        $days = [
            ['day_of_week' => 1, 'day_name' => 'Monday', 'is_instructional' => true, 'start_time' => '08:00:00', 'end_time' => '15:30:00'],
            ['day_of_week' => 2, 'day_name' => 'Tuesday', 'is_instructional' => true, 'start_time' => '08:00:00', 'end_time' => '15:30:00'],
            ['day_of_week' => 3, 'day_name' => 'Wednesday', 'is_instructional' => true, 'start_time' => '08:00:00', 'end_time' => '15:30:00'],
            ['day_of_week' => 4, 'day_name' => 'Thursday', 'is_instructional' => true, 'start_time' => '08:00:00', 'end_time' => '15:30:00'],
            ['day_of_week' => 5, 'day_name' => 'Friday', 'is_instructional' => true, 'start_time' => '08:00:00', 'end_time' => '14:30:00'],
            ['day_of_week' => 6, 'day_name' => 'Saturday', 'is_instructional' => false, 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
            ['day_of_week' => 7, 'day_name' => 'Sunday', 'is_instructional' => false, 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
        ];

        foreach ($days as $d) {
            SchoolWeeklySchedule::updateOrCreate(
                ['academic_year_id' => $year->id, 'day_of_week' => $d['day_of_week']],
                $d
            );
        }

        // 4. Calendar Holidays
        $holidays = [
            [
                'title' => 'Labor Day',
                'type' => 'national_holiday',
                'start_date' => '2026-09-07',
                'end_date' => '2026-09-07',
                'is_recurring_yearly' => true,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Official national public holiday honoring workers and labor contributions.',
            ],
            [
                'title' => 'National Independence Day',
                'type' => 'national_holiday',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-01',
                'is_recurring_yearly' => true,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Statutory federal independence anniversary observance.',
            ],
            [
                'title' => 'Fall Mid-Term & Founder\'s Recess',
                'type' => 'institutional_holiday',
                'start_date' => '2026-10-15',
                'end_date' => '2026-10-16',
                'is_recurring_yearly' => false,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'School-wide mid-semester academic pause and founder commemorative activities.',
            ],
            [
                'title' => 'Thanksgiving Recess',
                'type' => 'national_holiday',
                'start_date' => '2026-11-26',
                'end_date' => '2026-11-27',
                'is_recurring_yearly' => true,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Annual national Thanksgiving holiday and campus recess.',
            ],
            [
                'title' => 'Winter Break & Holiday Recess',
                'type' => 'recess',
                'start_date' => '2026-12-21',
                'end_date' => '2027-01-08',
                'is_recurring_yearly' => false,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Semester break between Fall Term and Spring Term.',
            ],
            [
                'title' => 'Martin Luther King Jr. Day',
                'type' => 'national_holiday',
                'start_date' => '2027-01-18',
                'end_date' => '2027-01-18',
                'is_recurring_yearly' => true,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'National civil rights memorial day.',
            ],
            [
                'title' => 'Spring Academic Recess',
                'type' => 'recess',
                'start_date' => '2027-03-29',
                'end_date' => '2027-04-02',
                'is_recurring_yearly' => false,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Spring break and faculty grading evaluation week.',
            ],
            [
                'title' => 'Memorial Day Observance',
                'type' => 'national_holiday',
                'start_date' => '2027-05-31',
                'end_date' => '2027-05-31',
                'is_recurring_yearly' => true,
                'affects_cohorts' => ['all'],
                'attendance_policy' => 'closed_no_register',
                'description' => 'Federal remembrance day observance.',
            ],
        ];

        foreach ($holidays as $h) {
            SchoolCalendarHoliday::updateOrCreate(
                ['academic_year_id' => $year->id, 'title' => $h['title']],
                $h
            );
        }

        // 5. Update Course Dual-Grading Defaults
        Course::query()->update([
            'grading_mode' => 'dual',
            'lms_weight' => 30,
            'offline_weight' => 70,
        ]);

        // 6. Update Assessments with delivery_mode
        Assessment::whereIn('type', ['quiz', 'assignment'])->update([
            'delivery_mode' => 'lms_online',
            'category_group' => 'quiz',
            'weightage_in_component' => 50,
        ]);

        Assessment::whereIn('type', ['midterm', 'final'])->update([
            'delivery_mode' => 'offline_coursework',
            'category_group' => 'final_exam',
            'weightage_in_component' => 50,
        ]);
    }
}
