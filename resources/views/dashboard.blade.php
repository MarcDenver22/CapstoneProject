@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'Welcome back, Admin! Here\'s your overview.')

@section('content')

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    <!-- Total Employees -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Employees</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalEmployees }}</p>
        </div>
    </div>

    <!-- Present Today -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-check text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Present Today</p>
            <p class="text-2xl font-bold text-gray-800">{{ $presentToday }}</p>
        </div>
    </div>

    <!-- Absent Today -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-times text-red-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Absent Today</p>
            <p class="text-2xl font-bold text-gray-800">{{ $absentToday }}</p>
        </div>
    </div>

    <!-- Late Arrivals -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-yellow-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Late Arrivals</p>
            <p class="text-2xl font-bold text-gray-800">{{ $lateArrivals }}</p>
        </div>
    </div>

</div>

<!-- Announcements & Events -->
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-bullhorn text-indigo-500 mr-2"></i>Announcements & Events
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($announcements as $announcement)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-{{ $announcement['color'] }}-100 rounded-lg flex items-center justify-center">
                    <i class="fas {{ $announcement['icon'] }} text-{{ $announcement['color'] }}-600"></i>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-{{ $announcement['color'] }}-600 bg-{{ $announcement['color'] }}-50 px-2 py-0.5 rounded">
                    {{ $announcement['type'] }}
                </span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">{{ $announcement['title'] }}</h3>
            <p class="text-sm text-gray-500 mb-3">{{ $announcement['description'] }}</p>
            <p class="text-xs text-gray-400">
                <i class="far fa-calendar mr-1"></i>{{ $announcement['date'] }}
            </p>
        </div>
        @endforeach
    </div>
</div>

<!-- Two Column Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Department-wise Summary (Takes 2 cols) -->
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-building text-indigo-500 mr-2"></i>Department-wise Summary (Today)
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Department</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-600">Total</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-600">Present</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-600">Absent</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-600">Late</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $dept)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 px-4 font-medium text-gray-700">{{ $dept['name'] }}</td>
                        <td class="py-3 px-4 text-center text-gray-600">{{ $dept['total'] }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $dept['present'] }}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $dept['absent'] }}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $dept['late'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent System Activities (Takes 1 col) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-history text-indigo-500 mr-2"></i>Recent Activities
        </h2>
        <div class="space-y-4">
            @foreach ($activities as $activity)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-700">{{ $activity['action'] }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $activity['detail'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="far fa-clock mr-1"></i>{{ $activity['time'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection