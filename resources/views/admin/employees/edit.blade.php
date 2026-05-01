@extends('layouts.app')

@section('title', 'Edit Employee')
@section('header', 'Edit Employee')
@section('subheader', $employee?->name ?? 'Edit Employee')

@section('content')

<div class="max-w-2xl">

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Employee</h2>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                <p class="text-red-700 font-semibold mb-2">Please fix the errors below:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $employee->name) }}" 
                    placeholder=""
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $employee->email) }}" 
                    placeholder=""
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Faculty ID -->
            <div>
                <label for="faculty_id" class="block text-sm font-semibold text-gray-700 mb-2">Faculty ID</label>
                <input 
                    type="text" 
                    id="faculty_id" 
                    name="faculty_id" 
                    value="{{ old('faculty_id', $employee->faculty_id) }}" 
                    placeholder="0000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('faculty_id') border-red-500 @enderror"
                >
                @error('faculty_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department -->
            <div>
                <label for="department" class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                @php(
                    $selectedDepartmentId = old('department_id', $employee?->department_id)
                )
                @php($departments = collect($departments ?? []))
                <select 
                    id="department" 
                    name="department_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Select a department</option>
                    @if($departments->isNotEmpty())
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No departments available</option>
                    @endif
                </select>
            </div>

            <!-- Read-only Info -->
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Account Status</p>
                    <p class="text-sm text-gray-700 font-semibold">Active</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Face Recognition</p>
                    <p class="text-sm text-gray-700">
                        @if($employee->face_enrolled)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Enrolled
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition"
                >
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
                <a 
                    href="{{ route('admin.employees.show', $employee->id) }}" 
                    class="flex-1 px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-center transition"
                >
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
