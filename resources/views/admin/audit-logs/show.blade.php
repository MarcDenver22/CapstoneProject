@extends('layouts.app')

@section('title', 'Audit Log Details')
@section('header', 'Audit Log Details')
@section('subheader', 'View activity details')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ ucfirst($auditLog->action) }}</h1>
            <p class="text-gray-600">{{ $auditLog->model_type ?? 'System' }} Activity</p>
        </div>

        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Action</p>
                        <p class="text-gray-800 mt-1 font-semibold">{{ ucfirst($auditLog->action) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Model Type</p>
                        <p class="text-gray-800 mt-1">{{ $auditLog->model_type ?? 'System' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-xs text-gray-500 font-semibold uppercase">User</p>
                        <p class="text-gray-800 mt-1">{{ $auditLog->user?->name ?? 'System' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Timestamp</p>
                        <p class="text-gray-800 mt-1">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-xs text-gray-500 font-semibold uppercase">IP Address</p>
                        <p class="text-gray-800 mt-1">{{ $auditLog->ip_address ?? 'N/A' }}</p>
                    </div>
                    @if($auditLog->model_id)
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Model ID</p>
                            <p class="text-gray-800 mt-1">{{ $auditLog->model_id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Changes -->
            @if($auditLog->changes)
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Changes</h2>
                    <div class="bg-gray-100 p-4 rounded text-sm font-mono overflow-x-auto">
                        <pre>{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex gap-4 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.audit_logs.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                Back to Logs
            </a>
        </div>
    </div>
</div>

@endsection
