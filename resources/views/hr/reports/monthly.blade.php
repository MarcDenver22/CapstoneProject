@extends('layouts.app')

@section('header', 'Monthly Attendance Report')
@section('subheader', $month->format('F Y'))

@section('content')
<div class="space-y-6">
    <!-- Header with Export Buttons -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Monthly Report</h2>
            <p class="text-gray-600 mt-2">{{ $month->format('F Y') }} ({{ $startDate->format('j') }} - {{ $endDate->format('j') }})</p>
            @if($department)
                <p class="text-gray-600">Department: <span class="font-semibold">{{ $department->name }}</span></p>
            @endif
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr.reports.export-pdf', array_merge(request()->query(), ['type' => 'monthly'])) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('hr.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Monthly Summary Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-300 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">EMPLOYEE</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">PRESENT</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">ABSENT</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">LATE</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">HALF DAY</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">LEAVE</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">TOTAL HOURS</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">ATTENDANCE %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $item)
                        @php
                            $totalDays = $item['present'] + $item['absent'] + $item['late'] + $item['half_day'] + $item['leave'];
                            $attendanceRate = $totalDays > 0 ? round((($item['present'] + ($item['half_day'] * 0.5)) / $totalDays) * 100, 1) : 0;
                        @endphp
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $item['user']->name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">{{ $item['present'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">{{ $item['absent'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">{{ $item['late'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">{{ $item['half_day'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-semibold">{{ $item['leave'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-gray-900">{{ $item['total_hours'] }}h</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-3 py-1 rounded text-xs font-semibold
                                    @if($attendanceRate >= 95) bg-green-100 text-green-800
                                    @elseif($attendanceRate >= 80) bg-blue-100 text-blue-800
                                    @elseif($attendanceRate >= 70) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $attendanceRate }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="mt-2">No attendance records for this month</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $totalPresent = $summary->sum('present');
            $totalAbsent = $summary->sum('absent');
            $totalLate = $summary->sum('late');
            $totalLeave = $summary->sum('leave');
        @endphp
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4 text-center">
            <p class="text-green-700 text-sm font-semibold">Total Present</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalPresent }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4 text-center">
            <p class="text-red-700 text-sm font-semibold">Total Absent</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $totalAbsent }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-4 text-center">
            <p class="text-yellow-700 text-sm font-semibold">Total Late</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalLate }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-4 text-center">
            <p class="text-purple-700 text-sm font-semibold">Total Leave</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalLeave }}</p>
        </div>
    </div>
</div>
@endsection
