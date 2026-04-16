@extends('layouts.app')

@section('title', 'Edit Attendance Record')
@section('header', 'Edit Attendance Record')
@section('subheader', 'Update attendance details')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.attendance.update', $attendance) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Employee -->
            <div>
                <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
                <select id="user_id" name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('user_id', $attendance->user_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Attendance Date -->
            <div>
                <label for="attendance_date" class="block text-sm font-semibold text-gray-700 mb-1">Date *</label>
                <input type="date" id="attendance_date" name="attendance_date" 
                    value="{{ old('attendance_date', $attendance->attendance_date->toDateString()) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('attendance_date')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time In -->
            <div>
                <label for="time_in" class="block text-sm font-semibold text-gray-700 mb-1">Time In</label>
                <input type="time" id="time_in" name="time_in" 
                    value="{{ old('time_in', $attendance->time_in?->format('H:i')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('time_in')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Out -->
            <div>
                <label for="time_out" class="block text-sm font-semibold text-gray-700 mb-1">Time Out</label>
                <input type="time" id="time_out" name="time_out" 
                    value="{{ old('time_out', $attendance->time_out?->format('H:i')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('time_out')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Status</option>
                    <option value="present" {{ old('status', $attendance->status) === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ old('status', $attendance->status) === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ old('status', $attendance->status) === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="half_day" {{ old('status', $attendance->status) === 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="leave" {{ old('status', $attendance->status) === 'leave' ? 'selected' : '' }}>Leave</option>
                </select>
                @error('status')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Liveness Verified -->
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="liveness_verified" name="liveness_verified" value="1"
                        {{ old('liveness_verified', $attendance->liveness_verified) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Liveness Verified (Face Recognition)</span>
                </label>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $attendance->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                    Update Record
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
