@extends('layouts.app')

@section('header', 'Employee Attendance Report')
@section('subheader', $employee->name)

@section('content')
<div class="space-y-6">
    <!-- Header with Export Buttons -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $employee->name }}</h2>
            <p class="text-gray-600 mt-2">{{ $startDate->format('F j') }} - {{ $endDate->format('F j, Y') }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $employee->position ?? 'N/A' }} • {{ $employee->department->name ?? 'N/A' }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr.reports.export-pdf', array_merge(request()->query(), ['type' => 'per-employee'])) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('hr.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Present</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $summary['present'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Absent</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $summary['absent'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Late</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $summary['late'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Total Hours</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $summary['total_hours'] }}h</p>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-gray-600 text-sm font-semibold">Half Days</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $summary['half_day'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-gray-600 text-sm font-semibold">Leave Days</p>
            <p class="text-2xl font-bold text-purple-600 mt-2">{{ $summary['leave'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-gray-600 text-sm font-semibold">Average Time In</p>
            <p class="text-2xl font-bold text-gray-700 mt-2 font-mono">{{ $summary['average_time_in'] }}</p>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 p-4">
            <h3 class="font-bold text-lg text-gray-800">Attendance Records</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-300 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">DATE</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME IN</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME OUT</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">HOURS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">VERIFIED</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NOTES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records->sortByDesc('attendance_date') as $record)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $record->attendance_date->format('M j, Y') }}</td>
                            <td class="py-3 px-4 text-gray-600">
                                @if($record->time_in)
                                    <span class="font-mono">{{ $record->time_in->format('H:i:s') }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                @if($record->time_out)
                                    <span class="font-mono">{{ $record->time_out->format('H:i:s') }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                @if($record->time_in && $record->time_out)
                                    <span class="font-mono">{{ $record->time_out->diffInHours($record->time_in) }}h</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                    @if($record->status === 'present') bg-green-100 text-green-800
                                    @elseif($record->status === 'absent') bg-red-100 text-red-800
                                    @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                    @elseif($record->status === 'half_day') bg-blue-100 text-blue-800
                                    @elseif($record->status === 'leave') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($record->liveness_verified)
                                    <span class="text-green-600 font-semibold"><i class="fas fa-check-circle"></i></span>
                                @else
                                    <span class="text-red-600 font-semibold"><i class="fas fa-times-circle"></i></span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600 text-xs">{{ $record->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="mt-2">No attendance records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
