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
        Schema::table('users', function (Blueprint $table) {
            // Store face encodings as JSON
            $table->json('face_encodings')->nullable();
            // Track if face enrollment is complete
            $table->boolean('face_enrolled')->default(false);
            // Track the number of samples captured
            $table->integer('face_samples_count')->default(0);
            // Store the face enrollment timestamp
            $table->timestamp('face_enrolled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_encodings', 'face_enrolled', 'face_samples_count', 'face_enrolled_at']);
        });
    }
};
