<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the queue tables (jobs, job_batches, failed_jobs) on the LOCAL
 * SQLite database so that queued attendance jobs persist even when the
 * kiosk has no internet connection.
 *
 * Run once after checkout:
 *   php artisan migrate --path=database/migrations/2026_05_02_create_local_queue_tables.php
 *
 * Or run all migrations (Laravel respects getConnection() per-migration):
 *   php artisan migrate
 */
return new class extends Migration
{
    /**
     * Always run this migration against the local SQLite database.
     */
    public function getConnection(): string
    {
        return 'sqlite';
    }

    public function up(): void
    {
        if (!Schema::connection('sqlite')->hasTable('jobs')) {
            Schema::connection('sqlite')->create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue');
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');

                $table->index(['queue', 'reserved_at', 'available_at']);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('job_batches')) {
            Schema::connection('sqlite')->create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('failed_jobs')) {
            Schema::connection('sqlite')->create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('jobs');
        Schema::connection('sqlite')->dropIfExists('job_batches');
        Schema::connection('sqlite')->dropIfExists('failed_jobs');
    }
};
