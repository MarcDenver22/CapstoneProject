@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">System Health</h1>
                <p class="text-gray-600 mt-1">Monitor system performance and status</p>
            </div>
            <i class="fas fa-heartbeat text-4xl text-red-500 opacity-20"></i>
        </div>
    </div>

    <!-- Health Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Database Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Database</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span> Healthy
                </span>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Connection:</span>
                    <span class="font-medium">Active</span>
                </div>
                <div class="flex justify-between">
                    <span>Response Time:</span>
                    <span class="font-medium">2ms</span>
                </div>
            </div>
        </div>

        <!-- Server Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Server</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span> Healthy
                </span>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Uptime:</span>
                    <span class="font-medium">48h 32m</span>
                </div>
                <div class="flex justify-between">
                    <span>Load:</span>
                    <span class="font-medium">45%</span>
                </div>
            </div>
        </div>

        <!-- Storage Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Storage</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span> Healthy
                </span>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Usage:</span>
                    <span class="font-medium">62%</span>
                </div>
                <div class="flex justify-between">
                    <span>Available:</span>
                    <span class="font-medium">38GB</span>
                </div>
            </div>
        </div>

        <!-- Memory Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Memory</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <span class="w-2 h-2 bg-yellow-600 rounded-full mr-2"></span> Warning
                </span>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Usage:</span>
                    <span class="font-medium">78%</span>
                </div>
                <div class="flex justify-between">
                    <span>Available:</span>
                    <span class="font-medium">2.2GB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- CPU Usage -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-900 mb-4">CPU Usage</h3>
            <div class="w-full bg-gray-100 rounded-full h-4" style="overflow: hidden;">
                <div class="bg-red-500 h-4 rounded-full" style="width: 65%; transition: width 0.3s ease;"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">Current: 65% • Average: 58%</p>
        </div>

        <!-- Memory Distribution -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-900 mb-4">Memory Distribution</h3>
            <div class="w-full bg-gray-100 rounded-full h-4" style="overflow: hidden;">
                <div class="bg-yellow-500 h-4 rounded-full" style="width: 78%; transition: width 0.3s ease;"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">Used: 6.2GB / Total: 8GB</p>
        </div>
    </div>

    <!-- System Logs -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-900 mb-4">Recent System Events</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-0.5">✓</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Database Connection Established</p>
                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                </div>
            </div>
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-0.5">ℹ</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">System Cache Cleared</p>
                    <p class="text-xs text-gray-500 mt-1">15 minutes ago</p>
                </div>
            </div>
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-0.5">✓</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Backup Completed Successfully</p>
                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                </div>
            </div>
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-0.5">✓</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">All Users Authenticated</p>
                    <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
