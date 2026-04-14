@extends('employee.layouts.app')

@section('header', 'Edit Profile')
@section('subheader', 'Update your personal information')

@section('content')

<div class="max-w-2xl">
    <!-- Back Button -->
    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 mb-6">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Your Profile</h2>

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

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                <p class="text-green-700 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    placeholder="John Doe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
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
                    value="{{ old('email', $user->email) }}" 
                    placeholder="john@example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Employee ID -->
            <div>
                <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee ID</label>
                <input 
                    type="text" 
                    id="employee_id" 
                    name="employee_id" 
                    value="{{ $user->employee_id ?? 'N/A' }}" 
                    placeholder="EMP001"
                    disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                >
                <p class="text-gray-500 text-xs mt-1">Your Employee ID is auto-generated and cannot be changed</p>
            </div>

            <!-- Position and Department -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                    <input 
                        type="text" 
                        id="position" 
                        name="position" 
                        value="{{ old('position', $user->position) }}" 
                        placeholder="e.g., Instructor, Professor"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                    <select 
                        id="department_id" 
                        name="department_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Select a department</option>
                        @forelse($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @empty
                            <option value="" disabled>No departments available</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Account Info -->
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Email Address (Read-only)</p>
                    <p class="text-sm text-gray-700">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Account Role</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $user->role }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Member Since</p>
                    <p class="text-sm text-gray-700">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('employee.dashboard') }}" class="flex-1 px-6 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
