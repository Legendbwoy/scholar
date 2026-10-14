<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 30)->unique(); // email, sms, whatsapp
            $table->string('provider', 50)->default('smtp'); // smtp, sendgrid, twilio, termii, meta_whatsapp, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('sandbox_mode')->default(false);
            $table->json('credentials')->nullable();
            $table->string('from_name', 100)->nullable();
            $table->string('from_address', 150)->nullable();
            $table->integer('rate_limit_per_minute')->default(60);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status', 30)->nullable(); // success, failed
            $table->text('last_test_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_gateways');
    }
};
