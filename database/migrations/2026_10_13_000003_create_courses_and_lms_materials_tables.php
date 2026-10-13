<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('title');
            $table->string('code')->unique();
            $table->string('semester')->default('Fall 2026');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('category')->default('Core'); // Core, Science, Arts, Technology, Languages
            $table->integer('total_lessons')->default(12);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_type')->default('pdf'); // pdf, video, document, slides, link
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('file_size')->nullable();
            $table->integer('download_count')->default(0);
            $table->string('version')->default('1.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('courses');
    }
};