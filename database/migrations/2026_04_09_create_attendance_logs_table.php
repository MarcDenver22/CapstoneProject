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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->date('log_date');
            $table->enum('period', ['AM', 'PM'])->default('AM');
            $table->enum('punch_type', ['IN', 'OUT'])->default('IN');
            $table->datetime('punched_at');
            $table->enum('method', ['face_recognition', 'manual', 'api'])->default('face_recognition');
            $table->decimal('confidence', 5, 2)->nullable(); // Face recognition confidence (0-100)
            $table->boolean('liveness_passed')->default(false); // Liveness verification result
            $table->string('photo_path')->nullable(); // Path to stored photo
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index('employee_id');
            $table->index('log_date');
            $table->index('period');
            $table->index(['employee_id', 'log_date', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
