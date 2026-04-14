@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">System Reports</h1>
                <p class="text-gray-600 mt-1">Generate and view system reports</p>
            </div>
            <i class="fas fa-file-alt text-4xl text-red-500 opacity-20"></i>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Daily Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Daily Report</h3>
                    <p class="text-sm text-gray-600 mt-1">Employee attendance for today</p>
                </div>
                <i class="fas fa-calendar-day text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>

        <!-- Weekly Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Weekly Report</h3>
                    <p class="text-sm text-gray-600 mt-1">Attendance summary for the week</p>
                </div>
                <i class="fas fa-calendar-week text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>

        <!-- Monthly Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Monthly Report</h3>
                    <p class="text-sm text-gray-600 mt-1">Attendance summary for the month</p>
                </div>
                <i class="fas fa-calendar-alt text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>

        <!-- Employee Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Employee Report</h3>
                    <p class="text-sm text-gray-600 mt-1">Per-employee attendance details</p>
                </div>
                <i class="fas fa-user text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>

        <!-- Department Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Department Report</h3>
                    <p class="text-sm text-gray-600 mt-1">Department-wise attendance analysis</p>
                </div>
                <i class="fas fa-sitemap text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>

        <!-- System Audit Report -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Audit Report</h3>
                    <p class="text-sm text-gray-600 mt-1">System activity and user actions</p>
                </div>
                <i class="fas fa-shield-alt text-red-500"></i>
            </div>
            <button class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">Generate Report</button>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-900 mb-4">Recent Reports</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Report Name</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Type</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Generated Date</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 px-4 text-gray-900">Monthly Report April 2026</td>
                        <td class="py-3 px-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Monthly</span></td>
                        <td class="py-3 px-4 text-gray-600">2026-04-12</td>
                        <td class="py-3 px-4"><a href="#" class="text-red-600 hover:text-red-800 font-medium">Download</a></td>
                    </tr>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 px-4 text-gray-900">Weekly Report</td>
                        <td class="py-3 px-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Weekly</span></td>
                        <td class="py-3 px-4 text-gray-600">2026-04-10</td>
                        <td class="py-3 px-4"><a href="#" class="text-red-600 hover:text-red-800 font-medium">Download</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
