@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-header rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Privacy Settings</h1>
                <p class="text-gray-600 mt-1">Manage data privacy and security policies</p>
            </div>
            <i class="fas fa-shield-alt text-4xl text-red-500 opacity-20"></i>
        </div>
    </div>

    <!-- Privacy Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Data Collection -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-database text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Data Collection</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Collect Attendance Data</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Store Face Encodings</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-2 ml-7">Required for face recognition functionality</p>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Log User Activities</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Data Retention -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-hourglass-end text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Data Retention</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Attendance Record Retention (months)</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="24" min="1" max="60">
                    <p class="text-xs text-gray-500 mt-1">Records older than this will be archived</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Audit Log Retention (months)</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="12" min="1" max="36">
                </div>
            </div>
        </div>

        <!-- Access Control -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-lock text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Access Control</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Require Strong Passwords</span>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Enable Two-Factor Authentication</span>
                    </label>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                    <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="30" min="5" max="480">
                </div>
            </div>
        </div>

        <!-- Encryption Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-key text-red-500"></i>
                <h2 class="text-xl font-semibold text-gray-900">Encryption</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Enable Data Encryption</span>
                    </label>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Encryption Method</label>
                    <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" value="AES-256" disabled>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                        <span class="text-sm font-medium text-gray-700">Encrypt Sensitive Data at Rest</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- GDPR & Compliance -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-900 mb-4">GDPR & Compliance</h3>
        <div class="space-y-4">
            <div>
                <label class="flex items-center gap-3 cursor-pointer mb-3">
                    <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                    <span class="text-sm font-medium text-gray-700">Allow users to request data export</span>
                </label>
            </div>
            <div>
                <label class="flex items-center gap-3 cursor-pointer mb-3">
                    <input type="checkbox" class="w-4 h-4 text-red-600 rounded" checked>
                    <span class="text-sm font-medium text-gray-700">Allow users to request data deletion</span>
                </label>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Auto-delete inactive user accounts (days)</label>
                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" value="365" min="30" max="1825">
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
