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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Academic'); // Academic, Sports, Arts & Culture, Symposium, Ceremony, Workshop
            $table->text('description');
            $table->dateTime('event_date');
            $table->dateTime('end_date')->nullable();
            $table->string('venue')->default('Main Quadrangle Amphitheater');
            $table->string('banner_image')->nullable();
            $table->string('organizer')->default('Office of Academic Affairs');
            $table->string('registration_link')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
