<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. LMS Quizzes, Tests & Examinations master table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('quiz'); // quiz, test, midterm, final_exam, practice
            $table->text('instructions')->nullable();
            $table->integer('duration_minutes')->default(30); // 0 or null = untimed
            $table->dateTime('start_time')->nullable(); // access window start
            $table->dateTime('end_time')->nullable(); // access window close
            $table->integer('total_points')->default(100);
            $table->integer('pass_points')->default(50);
            $table->integer('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('auto_submit_on_expiry')->default(true);
            $table->boolean('show_correct_answers')->default(true); // reveal solutions after submission
            $table->boolean('is_published')->default(true);
            $table->string('grading_component')->default('quiz'); // quiz, test, exam, continuous_assessment
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Quiz Questions table
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->string('question_type'); // mcq_single, mcq_multi, true_false, short_answer, essay
            $table->text('question_text');
            $table->integer('points')->default(10);
            $table->integer('order_index')->default(0);
            $table->text('explanation')->nullable(); // feedback/solution explanation
            $table->text('acceptable_answers')->nullable(); // comma-separated or text for short_answer
            $table->timestamps();
        });

        // 3. Question Options (for MCQ & True/False choices)
        Schema::create('quiz_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 4. Student Quiz Attempt Sessions
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('status')->default('in_progress'); // in_progress, submitted, graded, timed_out
            $table->text('teacher_feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Student Submitted Answers per Question
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('quiz_question_options')->nullOnDelete();
            $table->json('selected_option_ids')->nullable(); // for multi-select
            $table->text('text_answer')->nullable(); // for short_answer and essay
            $table->decimal('score_awarded', 8, 2)->default(0);
            $table->boolean('is_correct')->default(false);
            $table->text('teacher_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_question_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
