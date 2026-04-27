@extends('layouts.app')

@section('title', 'Employee Profile')
@section('header', 'Employee Profile')
@section('subheader', $employee->name)

@section('content')

<!-- Back Button -->
<a href="{{ route('admin.employees.list') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-6">
    <i class="fas fa-arrow-left"></i> Back to Employees
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Profile -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Employee Information</h2>

            <div class="space-y-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-semibold mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Full Name</p>
                            <p class="text-gray-800 font-semibold">{{ $employee->name }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Email</p>
                            <p class="text-gray-800 font-semibold text-sm">{{ $employee->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Work Details -->
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-semibold mb-4">Work Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Department</p>
                            <p class="text-gray-800 font-semibold">{{ $employee->department?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Faculty ID</p>
                            <p class="text-gray-800 font-semibold">{{ $employee->faculty_id ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Role</p>
                            <p class="text-gray-800 font-semibold">{{ ucfirst($employee->role) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Registration Info -->
                <div>
                    <h3 class="text-gray-500 text-sm uppercase font-semibold mb-4">Registration Info</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Registered On</p>
                            <p class="text-gray-800 font-semibold">{{ $employee->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-gray-500 text-xs uppercase mb-1">Last Updated</p>
                            <p class="text-gray-800 font-semibold">{{ $employee->updated_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Face Recognition Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-gray-800 font-bold mb-4">Face Recognition</h3>

            @if($employee->face_enrolled)
                <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200">
                    <p class="text-green-700 font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Enrolled
                    </p>
                </div>
            @else
                <div class="mb-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                    <p class="text-yellow-700 font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-clock"></i> Pending Enrollment
                    </p>
                </div>
            @endif

            <div class="space-y-3">
                <div>
                    <p class="text-gray-500 text-xs uppercase mb-2 font-semibold">Samples Captured</p>
                    <div class="relative h-2 rounded-full bg-gray-200 overflow-hidden">
                        <div class="h-full bg-blue-600" style="width: {{ ($employee->face_samples_count / 5) * 100 }}%"></div>
                    </div>
                    <p class="text-gray-700 text-sm mt-2">{{ $employee->face_samples_count ?? 0 }} / 5</p>
                </div>
            </div>

            @if($employee->face_enrolled)
                <form method="POST" action="{{ route('admin.employees.reset_face', $employee->id) }}" class="mt-4" onsubmit="return confirm('Reset face enrollment?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-sm transition">
                        <i class="fas fa-redo"></i> Reset Enrollment
                    </button>
                </form>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="{{ route('admin.employees.list') }}" class="block w-full px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-center transition">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}" onsubmit="return confirm('Delete employee? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                    <i class="fas fa-trash"></i> Delete Employee
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
