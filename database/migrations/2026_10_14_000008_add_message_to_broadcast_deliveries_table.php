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
        Schema::table('broadcast_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('broadcast_deliveries', 'message')) {
                $table->text('message')->nullable()->after('recipient_contact');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('broadcast_deliveries', 'message')) {
                $table->dropColumn('message');
            }
        });
    }
};
