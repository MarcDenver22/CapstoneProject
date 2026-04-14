@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">System Configuration</h1>
                <p class="text-gray-600 mt-1">Manage system settings and configurations</p>
            </div>
            <i class="fas fa-sliders-h text-4xl text-red-500 opacity-20"></i>
        </div>
    </div>

    <!-- Configuration Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- General Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-cog text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">General Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">System Name</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="Employee Attendance Management System" disabled>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">System Version</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="1.0.0" disabled>
                </div>
            </div>
        </div>

        <!-- Database Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-database text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Database Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Database Host</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="{{ config('database.connections.mysql.host') }}" disabled>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Database Name</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="{{ config('database.connections.mysql.database') }}" disabled>
                </div>
            </div>
        </div>

        <!-- Mail Configuration -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-envelope text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Mail Configuration</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Mail Driver</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="{{ config('mail.driver') }}" disabled>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">From Address</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="{{ config('mail.from.address') }}" disabled>
                </div>
            </div>
        </div>

        <!-- Face Recognition Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-face-smile text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Face Recognition</h2>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm font-medium text-gray-700">Face Recognition Enabled</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-red-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Min. Confidence Score</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="0.6" min="0" max="1" step="0.1" disabled>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
