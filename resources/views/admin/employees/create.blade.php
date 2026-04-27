@extends('layouts.app')

@section('title', 'Add New Employee')
@section('header', 'Add New Employee')
@section('subheader', 'Create a new employee account')

@section('content')

<div class="max-w-2xl">
    <!-- Back Button -->
    <a href="{{ route('admin.employees.list') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-6">
        <i class="fas fa-arrow-left"></i> Back to Employees
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Employee</h2>

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

        <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-6">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="John Doe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Account -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Account *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="john@example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Employee ID -->
            <div>
                <label for="faculty_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee ID *</label>
                <input 
                    type="text" 
                    id="faculty_id" 
                    name="faculty_id" 
                    value="{{ old('faculty_id') }}" 
                    placeholder="0001"
                    maxlength="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('faculty_id') border-red-500 @enderror"
                    required
                >
                @error('faculty_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department -->
            <div>
                <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">Department *</label>
                <select 
                    id="department_id" 
                    name="department_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Select a department</option>
                    @forelse($departments ?? [] as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @empty
                        <option value="" disabled>No departments available</option>
                    @endforelse
                </select>
                @error('department_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition"
                >
                    <i class="fas fa-save mr-2"></i> Create Employee
                </button>
                <a 
                    href="{{ route('admin.employees.list') }}" 
                    class="flex-1 px-6 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-center transition"
                >
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
