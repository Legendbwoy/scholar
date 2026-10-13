<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('quiz'); // quiz, assignment, midterm, final
            $table->string('term')->default('Term 1'); // Term 1, Term 2, Term 3
            $table->string('academic_year')->default('2026-2027');
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->integer('max_marks')->default(100);
            $table->integer('weightage')->default(20); // % contribution to term mark
            $table->date('date')->nullable();
            $table->string('status')->default('published'); // draft, published
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'student_id']);
        });

        Schema::create('term_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('term')->default('Term 1');
            $table->string('academic_year')->default('2026-2027');
            $table->string('conduct')->default('Exemplary'); // Exemplary, Very Good, Satisfactory, Needs Attention
            $table->text('teacher_remarks')->nullable();
            $table->text('principal_remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'term', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_remarks');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('assessments');
    }
};
