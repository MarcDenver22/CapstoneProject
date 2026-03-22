@extends('layouts.app')

@section('header', 'Attendance Details')
@section('subheader', 'View attendance record')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
            <h2 class="text-2xl font-bold">{{ $attendance->user->name }}</h2>
            <p class="text-blue-100 text-sm">{{ $attendance->user->email }}</p>
        </div>

        <!-- Details -->
        <div class="p-6 space-y-6">
            
            <!-- Attendance Date -->
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-200">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Date</p>
                    <p class="text-lg text-gray-900">{{ $attendance->attendance_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Day</p>
                    <p class="text-lg text-gray-900">{{ $attendance->attendance_date->format('l') }}</p>
                </div>
            </div>

            <!-- Time In / Out -->
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-200">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Time In</p>
                    @if($attendance->time_in)
                        <p class="text-lg text-gray-900">{{ $attendance->time_in->format('h:i A') }}</p>
                    @else
                        <p class="text-lg text-gray-400 italic">Not recorded</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">Time Out</p>
                    @if($attendance->time_out)
                        <p class="text-lg text-gray-900">{{ $attendance->time_out->format('h:i A') }}</p>
                    @else
                        <p class="text-lg text-gray-400 italic">Not recorded</p>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="pb-4 border-b border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-2">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $attendance->getStatusBadgeClass() }}">
                    {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                </span>
            </div>

            <!-- Liveness Verified -->
            <div class="pb-4 border-b border-gray-200">
                <p class="text-sm font-semibold text-gray-600 mb-2">Liveness Verified</p>
                @if($attendance->liveness_verified)
                    <div class="flex items-center gap-2 text-green-600">
                        <span class="text-lg">✓</span>
                        <span>Face Recognition Verified</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-red-600">
                        <span class="text-lg">✕</span>
                        <span>Not Verified</span>
                    </div>
                @endif
            </div>

            <!-- Notes -->
            @if($attendance->notes)
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Notes</p>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $attendance->notes }}</p>
                </div>
            @endif

            <!-- Timestamps -->
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                <div>
                    <p class="font-semibold">Created</p>
                    <p>{{ $attendance->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="font-semibold">Last Updated</p>
                    <p>{{ $attendance->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
