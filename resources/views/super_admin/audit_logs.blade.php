@extends('layouts.app')

@section('title', 'Audit Logs')
@section('header', 'Audit Logs')
@section('subheader', 'System activity and user action logs')

@section('content')

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-bold text-gray-800">Complete Audit Trail</h2>
        <p class="text-xs text-gray-500 mt-1">All system activities and user actions are logged here</p>
    </div>

    <div class="p-6">
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-300 bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">USER</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTION</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">MODEL</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">IP ADDRESS</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">TIMESTAMP</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">DETAILS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <p class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $log->getActionBadgeClass() }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $log->model_type }}</span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-xs font-mono">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-xs">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($log->model_id)
                                        <span class="text-xs text-gray-500">ID: {{ $log->model_id }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <i class="fas fa-history text-gray-300 text-4xl mb-3 block"></i>
                <p class="text-gray-600 text-lg font-medium">No audit logs found</p>
                <p class="text-gray-500 text-sm">System activities will appear here once they are recorded</p>
            </div>
        @endif
    </div>
</div>

@endsection
