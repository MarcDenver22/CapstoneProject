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
        Schema::table('attendance', function (Blueprint $table) {
            // Add period-specific time columns
            $table->time('am_arrival')->nullable()->after('time_in');
            $table->time('am_departure')->nullable()->after('am_arrival');
            $table->time('pm_arrival')->nullable()->after('am_departure');
            $table->time('pm_departure')->nullable()->after('pm_arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['am_arrival', 'am_departure', 'pm_arrival', 'pm_departure']);
        });
    }
};
