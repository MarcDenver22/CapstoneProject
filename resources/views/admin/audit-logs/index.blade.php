@extends('layouts.app')

@section('title', 'Audit Logs')
@section('header', 'Audit Logs')
@section('subheader', 'View system activity and audit trail')

@section('content')


<!-- Audit Logs Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6">
        @if($logs->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTION</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">DETAILS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">USER</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">IP ADDRESS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TIMESTAMP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ $log->getActionBadgeClass() }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-semibold text-gray-800">{{ ucfirst($log->model_type ?? 'System') }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-gray-600">{{ $log->user?->name ?? 'System' }}</p>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $log->ip_address ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="py-12 text-center">
                <p class="text-gray-500 text-lg">No audit logs found</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($logs->hasPages())
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
@endif

@endsection
