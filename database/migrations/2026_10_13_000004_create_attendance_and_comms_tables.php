<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->date('date');
            $table->string('status')->default('present'); // present, absent, late, excused
            $table->string('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method')->default('manual'); // manual, qr_code, biometric
            $table->timestamps();

            $table->unique(['student_id', 'date']);
        });

        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('target_audience')->default('all'); // all, teachers, students, parents, class
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->json('channels'); // ["email", "sms", "whatsapp", "in_app"]
            $table->string('priority')->default('normal'); // normal, urgent, emergency
            $table->string('status')->default('sent'); // draft, queued, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcast_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel'); // email, sms, whatsapp, in_app
            $table->string('recipient_contact')->nullable();
            $table->string('status')->default('delivered'); // queued, sent, delivered, failed
            $table->text('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_deliveries');
        Schema::dropIfExists('broadcast_messages');
        Schema::dropIfExists('attendances');
    }
};