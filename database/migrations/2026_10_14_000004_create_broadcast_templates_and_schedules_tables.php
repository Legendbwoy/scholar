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
        // 1. Broadcast Templates
        Schema::create('broadcast_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->string('category', 40)->default('seasonal');
            // seasonal, religious, gender_event, holiday_notification, academic_milestone
            $table->string('subject', 200);
            $table->text('message');
            $table->string('target_audience', 20)->default('all');
            // all, students, parents, teachers, class
            $table->json('channels')->nullable();
            $table->string('priority', 20)->default('normal');
            // normal, urgent, emergency
            $table->boolean('is_auto_send')->default(false);
            $table->string('auto_trigger_type', 30)->default('manual_only');
            // manual_only, holiday_linked, scheduled_date
            $table->string('auto_trigger_time', 10)->default('08:00');
            $table->foreignId('school_calendar_holiday_id')->nullable()->constrained('school_calendar_holidays')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Extend Broadcast Messages table for Scheduling & Auto-send
        Schema::table('broadcast_messages', function (Blueprint $table) {
            $table->foreignId('broadcast_template_id')->nullable()->after('sender_id')->constrained('broadcast_templates')->nullOnDelete();
            $table->string('send_mode', 25)->default('immediate')->after('priority'); 
            // immediate, scheduled, auto_holiday
            $table->dateTime('scheduled_at')->nullable()->after('send_mode');
            $table->string('auto_triggered_by', 100)->nullable()->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_messages', function (Blueprint $table) {
            $table->dropForeign(['broadcast_template_id']);
            $table->dropColumn(['broadcast_template_id', 'send_mode', 'scheduled_at', 'auto_triggered_by']);
        });

        Schema::dropIfExists('broadcast_templates');
    }
};
