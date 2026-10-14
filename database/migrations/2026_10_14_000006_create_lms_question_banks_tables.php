<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Institutional Question Banks
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('title');
            $table->string('topic')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Question Bank Questions
        Schema::create('bank_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->string('question_type'); // mcq_single, mcq_multi, true_false, short_answer, essay
            $table->text('question_text');
            $table->integer('points')->default(10);
            $table->text('explanation')->nullable();
            $table->text('acceptable_answers')->nullable();
            $table->timestamps();
        });

        // 3. Question Bank Options
        Schema::create('bank_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_question_id')->constrained('bank_questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_question_options');
        Schema::dropIfExists('bank_questions');
        Schema::dropIfExists('question_banks');
    }
};
