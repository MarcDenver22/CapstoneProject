@extends('layouts.app')

@section('header', 'Department Attendance Report')
@section('subheader', $department->name)

@section('content')
<div class="space-y-6">
    <!-- Header with Export Buttons -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $department->name }}</h2>
            <p class="text-gray-600 mt-2">{{ $startDate->format('F j') }} - {{ $endDate->format('F j, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr.reports.export-pdf', array_merge(request()->query(), ['type' => 'per-department'])) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('hr.reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>Back
            </a>
        </div>
    </div>

    <!-- Department Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Total Present</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $departmentSummary['total_present'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Total Absent</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $departmentSummary['total_absent'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Total Late</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $departmentSummary['total_late'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Half Days</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $departmentSummary['total_half_day'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-gray-600 text-sm font-semibold">Avg Attendance</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $departmentSummary['average_attendance_rate'] }}%</p>
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
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">ATTENDANCE %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employeeSummary as $item)
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
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-3 py-1 rounded text-xs font-semibold
                                    @if($item['attendance_rate'] >= 95) bg-green-100 text-green-800
                                    @elseif($item['attendance_rate'] >= 80) bg-blue-100 text-blue-800
                                    @elseif($item['attendance_rate'] >= 70) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $item['attendance_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="mt-2">No employees in this department</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
