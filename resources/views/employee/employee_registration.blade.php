@extends('layouts.app')

@section('header', 'Employee Registration')
@section('subheader', 'Join the Attendance System')

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Progress Indicator -->
    <div class="flex items-center justify-between mb-10">
        <div class="flex-1 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
            <p class="text-gray-700 mt-2 text-sm font-semibold">Register</p>
        </div>
        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
        <div class="flex-1 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
            <p class="text-gray-500 mt-2 text-sm font-semibold">Face Enrollment</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                <h4 class="text-red-700 font-semibold mb-2">Please fix the following errors:</h4>
                <ul class="text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Employee Information</h2>
        <p class="text-gray-600 mb-6">Please provide your details to register</p>

        <form action="{{ route('employee.registration.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- First Name -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Faculty ID -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Faculty ID</label>
                    <input type="text" name="faculty_id" value="{{ old('faculty_id') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Position</label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Department -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Department</label>
                <select name="department_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a department</option>
                    @forelse($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @empty
                        <option value="" disabled>No departments available</option>
                    @endforelse
                </select>
                @error('department_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Terms Agreement -->
            <div class="space-y-3 pt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="terms" required class="w-5 h-5 rounded border-gray-300">
                    <span class="text-gray-700 text-sm">I agree to the terms and conditions</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full mt-6 px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                Continue to Face Enrollment →
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Login here</a>
            </p>
        </div>
    </div>
</div>

@endsection
