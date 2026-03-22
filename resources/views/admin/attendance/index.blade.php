@extends('layouts.app')

@section('header', 'Attendance Management')
@section('subheader', 'View and manage employee attendance records')

@section('content')

<!-- Attendance Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-bold text-gray-800">All Attendance Records</h2>
        <p class="text-xs text-gray-500 mt-1">Displaying all employee attendance logs. Click "View" on today's dashboard for today's records.</p>
    </div>

    <div class="p-6">
        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-300 bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">EMPLOYEE</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">DATE</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME-IN</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME-OUT</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">LIVENESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $record)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <p class="font-medium text-gray-900">{{ $record->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $record->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $record->attendance_date->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    @if($record->time_in)
                                        {{ $record->time_in->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    @if($record->time_out)
                                        {{ $record->time_out->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $record->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($record->liveness_verified)
                                        <span class="text-green-600 font-semibold text-sm">✓</span>
                                    @else
                                        <span class="text-red-600 font-semibold text-sm">✕</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-4xl mb-3 block"></i>
                <p class="text-gray-600 text-lg font-medium">No attendance records found</p>
                <p class="text-gray-500 text-sm">Attendance records will appear here once they are created in the system.</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($attendances->hasPages())
    <div class="mt-6">
        {{ $attendances->links() }}
    </div>
@endif

@endsection
