@extends('layouts.app')

@section('header', 'Weekly Attendance Report')
@section('subheader', $startDate->format('F j') . ' - ' . $endDate->format('F j, Y'))

@section('content')
<div class="space-y-6">
    <!-- Header with Export Buttons -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Weekly Report</h2>
            <p class="text-gray-600 mt-2">{{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
            @if($department)
                <p class="text-gray-600">Department: <span class="font-semibold">{{ $department->name }}</span></p>
            @endif
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr.reports.export-csv', array_merge(request()->query(), ['type' => 'weekly'])) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-download"></i>Export CSV
            </a>
            <a href="{{ route('hr.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Overall Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
            <p class="text-blue-700 text-sm font-semibold">Avg Hours/Day</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">
                @php
                    $totalHours = 0;
                    $daysWithHours = 0;
                    foreach($records as $record) {
                        if($record->time_in && $record->time_out) {
                            $totalHours += $record->time_out->diffInHours($record->time_in);
                            $daysWithHours++;
                        }
                    }
                    $avgHours = $daysWithHours > 0 ? round($totalHours / $daysWithHours, 1) : 0;
                @endphp
                {{ $avgHours }}h
            </p>
        </div>
    </div>

    <!-- Employee Summary Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 p-4">
            <h3 class="font-bold text-lg text-gray-800">Employee Summary</h3>
        </div>
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $item)
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">No attendance records for this week</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
