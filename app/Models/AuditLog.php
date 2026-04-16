<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $connection = 'supabase';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getActionBadgeClass()
    {
        return match($this->action) {
            'create' => 'bg-green-100 text-green-800',
            'update' => 'bg-yellow-100 text-yellow-800',
            'delete' => 'bg-red-100 text-red-800',
            'login' => 'bg-blue-100 text-blue-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'export' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getActionIcon()
    {
        return match($this->action) {
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'export' => 'fas fa-download',
            default => 'fas fa-history',
        };
    }

    public function getActionBgColor()
    {
        return match($this->action) {
            'create' => 'bg-green-100',
            'update' => 'bg-yellow-100',
            'delete' => 'bg-red-100',
            'login' => 'bg-blue-100',
            'logout' => 'bg-gray-100',
            'export' => 'bg-purple-100',
            default => 'bg-gray-100',
        };
    }

    public function getActionIconColor()
    {
        return match($this->action) {
            'create' => '#059669',
            'update' => '#d97706',
            'delete' => '#dc2626',
            'login' => '#2563eb',
            'logout' => '#6b7280',
            'export' => '#9333ea',
            default => '#6b7280',
        };
    }
}
