<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $connection = 'supabase';
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'attendance_date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'liveness_verified',
        'synced',
        'am_arrival',
        'am_departure',
        'pm_arrival',
        'pm_departure',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'am_arrival' => 'datetime',
        'am_departure' => 'datetime',
        'pm_arrival' => 'datetime',
        'pm_departure' => 'datetime',
        'liveness_verified' => 'boolean',
        'synced' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByDate($query, $date = null)
    {
        return $query->where('attendance_date', $date ?? now()->toDateString());
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'present' => 'bg-green-100 text-green-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'absent' => 'bg-red-100 text-red-800',
            'half_day' => 'bg-orange-100 text-orange-800',
            'leave' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'present' => 'fas fa-check-circle',
            'late' => 'fas fa-clock',
            'absent' => 'fas fa-times-circle',
            'half_day' => 'fas fa-hourglass-half',
            'leave' => 'fas fa-calendar-times',
            default => 'fas fa-question-circle',
        };
    }
}