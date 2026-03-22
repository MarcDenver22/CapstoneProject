@extends('layouts.app')

@section('header', 'Daily Attendance Report')
@section('subheader', $date->format('l, F j, Y'))

@section('content')
<div class="space-y-6">
    <!-- Header with Export Buttons -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Attendance for {{ $date->format('F j, Y') }}</h2>
            @if($department)
                <p class="text-gray-600 mt-2">Department: <span class="font-semibold">{{ $department->name }}</span></p>
            @else
                <p class="text-gray-600 mt-2">All Departments</p>
            @endif
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr.reports.export-csv', array_merge(request()->query(), ['type' => 'daily'])) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-download"></i>Export CSV
            </a>
            <a href="{{ route('hr.reports.export-pdf', array_merge(request()->query(), ['type' => 'daily'])) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('hr.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Total Records</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $records->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4 text-center">
            <p class="text-green-700 text-sm font-semibold">Present</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $records->where('status', 'present')->count() }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4 text-center">
            <p class="text-red-700 text-sm font-semibold">Absent</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $records->where('status', 'absent')->count() }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-4 text-center">
            <p class="text-yellow-700 text-sm font-semibold">Late</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $records->where('status', 'late')->count() }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4 text-center">
            <p class="text-blue-700 text-sm font-semibold">Half Day</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $records->where('status', 'half_day')->count() }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-4 text-center">
            <p class="text-purple-700 text-sm font-semibold">Leave</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $records->where('status', 'leave')->count() }}</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-300">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">EMPLOYEE</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">DEPARTMENT</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME IN</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME OUT</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">HOURS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">VERIFIED</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NOTES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $record->user->name }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $record->user->department->name ?? 'N/A' }}</td>
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
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="mt-2">No attendance records for this date</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
