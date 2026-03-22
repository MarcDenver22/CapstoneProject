<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Log an audit entry
     */
    public static function log(string $action, ?string $modelType = null, ?int $modelId = null, ?array $changes = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a resource creation
     */
    public static function logCreate(string $modelType, int $modelId, ?array $data = null): AuditLog
    {
        return self::log('create', $modelType, $modelId, $data);
    }

    /**
     * Log a resource update
     */
    public static function logUpdate(string $modelType, int $modelId, array $changes): AuditLog
    {
        return self::log('update', $modelType, $modelId, $changes);
    }

    /**
     * Log a resource deletion
     */
    public static function logDelete(string $modelType, int $modelId, ?array $data = null): AuditLog
    {
        return self::log('delete', $modelType, $modelId, $data);
    }

    /**
     * Log a login
     */
    public static function logLogin(): AuditLog
    {
        return self::log('login', 'System');
    }

    /**
     * Log a logout
     */
    public static function logLogout(): AuditLog
    {
        return self::log('logout', 'System');
    }

    /**
     * Log an export
     */
    public static function logExport(string $type, ?array $filters = null): AuditLog
    {
        return self::log('export', $type, null, $filters);
    }
}
