@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Face Recognition Settings</h1>
                <p class="text-gray-600 mt-1">Configure face recognition system parameters</p>
            </div>
            <i class="fas fa-face-smile text-4xl text-red-500 opacity-20"></i>
        </div>
    </div>

    <!-- Settings Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Enrollment Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-image text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Enrollment Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Minimum Samples Required</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="5" min="1" max="20">
                    <p class="text-xs text-gray-500 mt-1">Number of face samples needed for enrollment</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Sample Collection Time (seconds)</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="30" min="10" max="120">
                    <p class="text-xs text-gray-500 mt-1">Time allowed for taking samples</p>
                </div>
            </div>
        </div>

        <!-- Recognition Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-search text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Recognition Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Confidence Threshold</label>
                    <input type="range" class="mt-1 block w-full" min="0.4" max="0.95" step="0.05" value="0.6">
                    <p class="text-xs text-gray-500 mt-1">Current: 0.60 (60% confidence)</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Recognition Timeout (seconds)</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="10" min="5" max="30">
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-cogs text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">System Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Enable Face Recognition</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Require Face Verification for Attendance</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Enable Debug Mode</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-sliders-h text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Advanced Settings</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Model Version</label>
                    <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        <option>face-api.js v0.22.2</option>
                        <option>face-api.js v0.22.1</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Processing Quality</label>
                    <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        <option>High (Slower)</option>
                        <option selected>Medium (Balanced)</option>
                        <option>Low (Faster)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end gap-3">
        <button class="px-6 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition font-medium">Cancel</button>
        <button class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">Save Settings</button>
    </div>
</div>
@endsection
