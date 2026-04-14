@extends('layouts.app')

@section('header', 'Edit User Role')
@section('subheader', 'Assign or change user role')

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Edit User Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                    @php
                        $roleColors = [
                            'super_admin' => 'bg-red-100 text-red-800',
                            'admin' => 'bg-purple-100 text-purple-800',
                            'employee' => 'bg-blue-100 text-blue-800',
                            'hr' => 'bg-orange-100 text-orange-800'
                        ];
                        echo $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                    @endphp">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('super_admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- User Information (Read-only) -->
                <div class="space-y-4">
                    <h3 class="text-md font-semibold text-gray-700 border-b pb-2">User Information</h3>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <p class="text-gray-600">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <p class="text-gray-600">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Joined Date</label>
                        <p class="text-gray-600">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="space-y-4 border-t pt-6">
                    <h3 class="text-md font-semibold text-gray-700 border-b pb-2">Assign Role</h3>
                    
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name (Optional Update)</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email (Optional Update)</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Select Role *</label>
                        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>
                                Employee
                            </option>
                            <option value="hr" {{ old('role', $user->role) === 'hr' ? 'selected' : '' }}>
                                HR
                            </option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                            <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>
                                Super Admin
                            </option>
                        </select>
                        @error('role')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Role Descriptions:</strong>
                            </p>
                            <ul class="text-xs text-blue-600 mt-2 space-y-1 ml-4">
                                <li><strong>Employee:</strong> Can view personal attendance and events</li>
                                <li><strong>Admin:</strong> Can manage employees, events, and attendance records</li>
                                <li><strong>Super Admin:</strong> Full system access including user management</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                    <a href="{{ route('super_admin.users.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
