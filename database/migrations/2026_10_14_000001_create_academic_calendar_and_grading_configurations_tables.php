<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // e.g. 2026-2027
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->string('status', 20)->default('active'); // upcoming, active, completed, archived
            $table->timestamps();
        });

        // 2. Academic Terms / Semesters
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name', 50); // Term 1 (Fall), Term 2 (Spring), Term 3 (Summer)
            $table->string('term_code', 20); // T1-2026, T2-2027
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('instructional_days_target')->default(90);
            $table->date('grade_submission_deadline')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('status', 20)->default('active'); // planned, active, grading, closed
            $table->timestamps();

            $table->unique(['academic_year_id', 'term_code']);
        });

        // 3. School Calendar Holidays & Observances
        Schema::create('school_calendar_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('title', 150);
            $table->string('type', 30)->default('national_holiday'); 
            // national_holiday, regional_holiday, institutional_holiday, recess, emergency_closure
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_recurring_yearly')->default(false);
            $table->json('affects_cohorts')->nullable(); // ["all"] or cohort IDs
            $table->string('attendance_policy', 30)->default('closed_no_register');
            // closed_no_register, excused_auto, special_session
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Weekly Instructional Schedule
        Schema::create('school_weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->tinyInteger('day_of_week'); // 1 = Monday, ..., 7 = Sunday
            $table->string('day_name', 15);
            $table->boolean('is_instructional')->default(true);
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->default('15:30:00');
            $table->timestamps();
        });

        // 5. Enhance Courses with Dual-Grading Scheme
        Schema::table('courses', function (Blueprint $table) {
            $table->string('grading_mode', 20)->default('dual')->after('category'); 
            // dual, offline_only, lms_only
            $table->integer('lms_weight')->default(30)->after('grading_mode');
            $table->integer('offline_weight')->default(70)->after('lms_weight');
        });

        // 6. Enhance Assessments with Delivery Mode & Category Group
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('delivery_mode', 30)->default('offline_coursework')->after('type');
            // lms_online, offline_coursework
            $table->string('category_group', 30)->default('quiz')->after('delivery_mode');
            // quiz, assignment, lab_practical, midterm, final_exam, project
            $table->integer('weightage_in_component')->default(100)->after('weightage');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['delivery_mode', 'category_group', 'weightage_in_component']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['grading_mode', 'lms_weight', 'offline_weight']);
        });

        Schema::dropIfExists('school_weekly_schedules');
        Schema::dropIfExists('school_calendar_holidays');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_years');
    }
};
