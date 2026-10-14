<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BroadcastTemplate;
use App\Models\SchoolCalendarHoliday;
use App\Models\User;

class BroadcastTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : null;

        $holidays = SchoolCalendarHoliday::all()->keyBy('title');

        $templates = [
            // ================= 1. SEASONAL GREETINGS =================
            [
                'title' => 'New Academic Session Welcome & Dean Directive',
                'slug' => 'new-academic-session-welcome',
                'category' => 'seasonal',
                'subject' => 'Welcome to the New Academic Session at {school_name}',
                'message' => "Dear {parent_name} and scholars, on behalf of the faculty and administration of {school_name}, we extend our warmest welcome to the new academic term. Classes commence promptly on {date}. Please ensure that all digital coursework credentials are confirmed and instructional requirements prepared. We look forward to an inspiring term of scholastic excellence.",
                'target_audience' => 'all',
                'channels' => ['email', 'whatsapp', 'in_app', 'sms'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'New Year Joy & Scholastic Vision Announcement',
                'slug' => 'new-year-vision-announcement',
                'category' => 'seasonal',
                'subject' => 'Happy New Year from {school_name} Management',
                'message' => "Happy New Year to {parent_name}, {student_name}, and your extended family! As we step into a new year, {school_name} reaffirms its dedication to nurturing brilliant minds and upright leaders. Administrative desks reopen on {resumption_date}. May this year bring peace, academic distinctions, and flourishing success.",
                'target_audience' => 'all',
                'channels' => ['whatsapp', 'email', 'sms', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("New Year's Day")?->id,
            ],
            [
                'title' => 'End-of-Term Holiday Recess & Safe Travels Directive',
                'slug' => 'end-of-term-recess-directive',
                'category' => 'seasonal',
                'subject' => 'End-of-Term Recess & Campus Closure Advisory',
                'message' => "Dear Parents and Guardians of {student_name}, this is an official notification that instructional activities for the term conclude on {date}. Campus boarding houses close at 17:00. Terminal report cards are now accessible on the Parent Portal. We wish our scholars a refreshing recess and safe travels.",
                'target_audience' => 'parents',
                'channels' => ['sms', 'email', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],

            // ================= 2. RELIGIOUS OBSERVANCES =================
            [
                'title' => 'Ramadan Mubarak & Adjusted Class Hours Advisory',
                'slug' => 'ramadan-mubarak-advisory',
                'category' => 'religious',
                'subject' => 'Ramadan Mubarak to our School Community & Schedule Notice',
                'message' => "Scholar SMS Academy extends heartfelt Ramadan Mubarak greetings to all our Muslim students, guardians, and faculty. To support students observing the holy fast, campus afternoon recreational blocks will conclude 45 minutes early throughout the holy month. We pray for a peaceful season filled with contemplation and grace.",
                'target_audience' => 'all',
                'channels' => ['whatsapp', 'email', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'Eid al-Fitr Blessings & Statutory Campus Holiday',
                'slug' => 'eid-al-fitr-holiday-greetings',
                'category' => 'religious',
                'subject' => 'Eid Mubarak & Official Holiday Closure Notice',
                'message' => "Taqabbal Allahu Minna Wa Minkum! On this blessed occasion of Eid al-Fitr, {school_name} wishes {parent_name} and our entire community immense joy and celebration. Please be advised that campus offices and classrooms will be closed for the official Eid observance on {date}. Academic sessions resume on {resumption_date}.",
                'target_audience' => 'all',
                'channels' => ['sms', 'whatsapp', 'email', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("Eid al-Fitr")?->id,
            ],
            [
                'title' => 'Easter Joy & Resurrection Sunday Goodwill Message',
                'slug' => 'easter-goodwill-message',
                'category' => 'religious',
                'subject' => 'Easter Blessings & Holiday Notice from {school_name}',
                'message' => "Wishing {parent_name}, {student_name}, and your loved ones a blessed and joyful Easter! May the triumph and hope of the season bring renewal, harmony, and peace to your home. In observance of Good Friday and Easter Monday, all campus operations remain suspended until {resumption_date}.",
                'target_audience' => 'all',
                'channels' => ['email', 'whatsapp', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("Good Friday")?->id,
            ],
            [
                'title' => 'Christmas Season Peace & Year-End Benediction',
                'slug' => 'christmas-season-benediction',
                'category' => 'religious',
                'subject' => 'Merry Christmas & Season Greetings from {school_name}',
                'message' => "Merry Christmas from the Executive Board, faculty, and staff of {school_name}! As we celebrate this season of goodwill and joy, we thank {parent_name} for your enduring partnership in the education of {student_name}. Campus offices remain on winter recess through the holidays. Have a safe and joyful celebration.",
                'target_audience' => 'all',
                'channels' => ['whatsapp', 'email', 'sms', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("Christmas Day")?->id,
            ],

            // ================= 3. GENDER & INCLUSION EVENTS =================
            [
                'title' => 'International Women’s Day & Girls in STEM Salute',
                'slug' => 'international-womens-day-salute',
                'category' => 'gender_event',
                'subject' => 'Celebrating International Women’s Day & Girls in STEM',
                'message' => "On International Women's Day, {school_name} honors our phenomenal female scholars, mothers, and visionary women educators. Today, we celebrate their extraordinary breakthroughs across robotics, scientific research, and leadership. Let us continue inspiring every young girl to lead fearlessly and innovate without bounds.",
                'target_audience' => 'all',
                'channels' => ['whatsapp', 'email', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'International Men’s Day & Positive Youth Mentorship',
                'slug' => 'international-mens-day-salute',
                'category' => 'gender_event',
                'subject' => 'Honoring International Men’s Day & Student Mentorship',
                'message' => "In recognition of International Men's Day, {school_name} salutes our dedicated male educators, fathers, and young male scholars. We celebrate positive role modeling, mental wellness, and compassionate servant leadership. Thank you for building a culture of honor, dignity, and academic drive.",
                'target_audience' => 'all',
                'channels' => ['email', 'whatsapp', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'Mother’s Day Appreciation & Family Gratitude Salute',
                'slug' => 'mothers-day-appreciation',
                'category' => 'gender_event',
                'subject' => 'Happy Mother’s Day from {school_name}',
                'message' => "Happy Mother's Day to all incredible mothers, grandmothers, and female guardians within our school family! Your selfless sacrifice, wisdom, and nurturing care form the bedrock of {student_name}'s successes. May your day be filled with happiness and deep appreciation.",
                'target_audience' => 'parents',
                'channels' => ['whatsapp', 'sms', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'Father’s Day Tribute & Paternal Leadership Honor',
                'slug' => 'fathers-day-tribute',
                'category' => 'gender_event',
                'subject' => 'Happy Father’s Day from {school_name}',
                'message' => "Happy Father's Day! {school_name} honors our fathers and paternal guardians whose guidance and unwavering strength shape our scholars into responsible leaders of tomorrow. Thank you for your inspiring commitment to {student_name}'s future.",
                'target_audience' => 'parents',
                'channels' => ['whatsapp', 'sms', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],

            // ================= 4. HOLIDAY NOTIFICATIONS & CLOSURES =================
            [
                'title' => 'National Independence Day Statutory Holiday Closure',
                'slug' => 'independence-day-closure-notice',
                'category' => 'holiday_notification',
                'subject' => 'Statutory Holiday Notice: National Independence Day',
                'message' => "Official Directive: In commemoration of National Independence Day, {school_name} will observe a campus-wide holiday closure on {date}. Daily attendance registers will not be opened, and instructional sessions are suspended. All academic activities resume promptly on {resumption_date}.",
                'target_audience' => 'all',
                'channels' => ['sms', 'whatsapp', 'email', 'in_app'],
                'priority' => 'urgent',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("Independence Day")?->id,
            ],
            [
                'title' => 'Mid-Term Academic Recess & Student Safety Guidelines',
                'slug' => 'mid-term-break-guidelines',
                'category' => 'holiday_notification',
                'subject' => 'Mid-Term Recess & Scholastic Progress Advisory',
                'message' => "Dear Parents and Guardians of {student_name}, our scheduled mid-term break begins on {date} and runs through {resumption_date}. Please encourage your ward to review continuous assessment feedback uploaded to the LMS portal. Campus boarding facilities will reopen at 14:00 on Sunday prior to resumption.",
                'target_audience' => 'parents',
                'channels' => ['sms', 'email', 'in_app'],
                'priority' => 'normal',
                'is_auto_send' => true,
                'auto_trigger_type' => 'holiday_linked',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => $holidays->get("Mid-Term Fall Break")?->id,
            ],
            [
                'title' => 'Inclement Weather Advisory & Remote Learning Protocol',
                'slug' => 'inclement-weather-remote-protocol',
                'category' => 'holiday_notification',
                'subject' => 'EMERGENCY ADVISORY: Inclement Weather Protocol in Effect',
                'message' => "EMERGENCY SAFETY DIRECTIVE: Due to extreme inclement weather warnings and regional civil advisories, physical school attendance is suspended for {date}. Scholars are instructed to log into the LMS portal for asynchronous remote learning modules and faculty check-ins. Stay indoors and stay safe.",
                'target_audience' => 'all',
                'channels' => ['sms', 'whatsapp', 'in_app', 'email'],
                'priority' => 'emergency',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '07:00',
                'school_calendar_holiday_id' => null,
            ],

            // ================= 5. ACADEMIC MILESTONES =================
            [
                'title' => 'Terminal Examination Protocol & Timetable Publication',
                'slug' => 'terminal-exam-timetable-release',
                'category' => 'academic_milestone',
                'subject' => 'Final Examination Schedule & Examination Hall Protocol',
                'message' => "Attention {class} students and guardians: Official terminal examination timetables have been released and published on your portal dashboard. Students are required to be seated in examination halls 15 minutes prior to commencement with approved identification. Punctuality and academic integrity are strictly enforced.",
                'target_audience' => 'all',
                'channels' => ['email', 'whatsapp', 'in_app'],
                'priority' => 'urgent',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
            [
                'title' => 'Official Terminal Report Cards Published on Portal',
                'slug' => 'terminal-report-cards-published',
                'category' => 'academic_milestone',
                'subject' => 'Official Term Report Cards Now Available for {student_name}',
                'message' => "Dear {parent_name}, official terminal academic report cards for {student_name} ({class}) have been certified and published to the Parent Portal. Please log into your portal account at {school_name} to review the continuous assessment breakdown, teacher commendations, and principal endorsement.",
                'target_audience' => 'parents',
                'channels' => ['sms', 'whatsapp', 'email', 'in_app'],
                'priority' => 'urgent',
                'is_auto_send' => false,
                'auto_trigger_type' => 'manual_only',
                'auto_trigger_time' => '08:00',
                'school_calendar_holiday_id' => null,
            ],
        ];

        foreach ($templates as $tData) {
            $tData['created_by'] = $adminId;
            BroadcastTemplate::updateOrCreate(
                ['slug' => $tData['slug']],
                $tData
            );
        }
    }
}
