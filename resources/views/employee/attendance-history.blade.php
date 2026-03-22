@extends('layouts.app')

@section('header', 'Attendance History')
@section('subheader', 'View your complete attendance records')

@section('content')

<div class="space-y-6">
    <!-- Filter Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- This Month Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-2">This Month</p>
            <p class="text-2xl font-bold text-blue-600">{{ $daysPresent ?? 0 }}</p>
            <p class="text-xs text-gray-600 mt-1">Days Present</p>
        </div>

        <!-- Absences Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-2">This Month</p>
            <p class="text-2xl font-bold text-red-600">{{ $absences ?? 0 }}</p>
            <p class="text-xs text-gray-600 mt-1">Absences</p>
        </div>

        <!-- Late Arrivals Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-2">This Month</p>
            <p class="text-2xl font-bold text-orange-600">{{ $lateArrivals ?? 0 }}</p>
            <p class="text-xs text-gray-600 mt-1">Late Arrivals</p>
        </div>

        <!-- Attendance Rate -->
        @php
            $totalDays = $daysPresent + $absences + $lateArrivals;
            $attendanceRate = $totalDays > 0 ? round((($daysPresent + $lateArrivals) / $totalDays) * 100) : 0;
        @endphp
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-2">Attendance Rate</p>
            <p class="text-2xl font-bold text-green-600">{{ $attendanceRate }}%</p>
            <p class="text-xs text-gray-600 mt-1">This Month</p>
        </div>
    </div>

    <!-- Main Attendance History Table -->
    @include('components.attendance-history', [
        'records' => $attendanceRecords,
        'title' => 'Complete Attendance History',
        'subtitle' => 'All records'
    ])
</div>

@endsection
