<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index()
    {
        $logs = AuditLog::with('user')
            ->recent()
            ->paginate(20);
        
        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
