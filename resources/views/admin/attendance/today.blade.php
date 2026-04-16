@extends('layouts.app')

@section('title', 'Today\'s Attendance')
@section('header', 'Today\'s Attendance')
@section('subheader', "Attendance records for {{ now()->format('M d, Y') }}")

@section('content')

<div class="space-y-6">
    
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Total Present -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Total Present</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayStats['present'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-green-500">✓</div>
            </div>
        </div>

        <!-- Total Late -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Late Arrivals</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayStats['late'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-yellow-500">⏱</div>
            </div>
        </div>

        <!-- Total Absent -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Absent</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayStats['absent'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-red-500">✕</div>
            </div>
        </div>

        <!-- Face Verified -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Face Verified</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayStats['verified'] ?? 0 }}</p>
                </div>
                <div class="text-4xl text-blue-500">👤</div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
        </div>

        @if($attendanceRecords->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Employee</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time In</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time Out</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Verified</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($attendanceRecords as $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-semibold text-gray-900">{{ $record->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($record->time_in)
                                        {{ $record->time_in->format('h:i A') }}
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($record->time_out)
                                        {{ $record->time_out->format('h:i A') }}
                                    @else
                                        <span class="text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block px-3 py-1 rounded-full font-semibold {{ $record->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($record->liveness_verified)
                                        <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                            <span class="text-lg">✓</span> Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                            <span class="text-lg">✕</span> No
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-600 text-lg">No attendance records for today</p>
            </div>
        @endif
    </div>
</div>

@endsection
