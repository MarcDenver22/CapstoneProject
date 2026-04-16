<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $connection = 'supabase';
    protected $table = 'attendance_logs';

    protected $fillable = [
        'employee_id',
        'log_date',
        'period',
        'punch_type',
        'punched_at',
        'method',
        'confidence',
        'liveness_passed',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'punched_at' => 'datetime',
        'liveness_passed' => 'boolean',
        'confidence' => 'decimal:2',
    ];

    /**
     * Get the employee associated with this log
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the period name
     */
    public function getPeriodLabel(): string
    {
        return $this->period === 'AM' ? 'Morning' : 'Afternoon';
    }

    /**
     * Get punch type label
     */
    public function getPunchTypeLabel(): string
    {
        return $this->punch_type === 'IN' ? 'Time In' : 'Time Out';
    }
}
