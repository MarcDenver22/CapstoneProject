<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations on the Supabase connection so the attendance
     * table gains a `synced` flag and a performance index.
     */
    public function getConnection(): string
    {
        return 'supabase';
    }

    public function up(): void
    {
        Schema::connection('supabase')->table('attendance', function (Blueprint $table) {
            // True  = record was saved directly (online) or has been synced from the queue.
            // False = record is pending sync (stored via offline queue but not yet confirmed).
            if (!Schema::connection('supabase')->hasColumn('attendance', 'synced')) {
                $table->boolean('synced')->default(true)->after('liveness_verified');
            }
        });

        // Index to make "pending sync" queries fast (admin dashboard / monitoring)
        Schema::connection('supabase')->table('attendance', function (Blueprint $table) {
            $table->index('synced', 'attendance_synced_index');
        });
    }

    public function down(): void
    {
        Schema::connection('supabase')->table('attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_synced_index');
            $table->dropColumn('synced');
        });
    }
};
