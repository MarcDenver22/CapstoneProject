@extends('layouts.app')

@section('header', 'User Management')
@section('subheader', 'Manage system users and roles')

@section('content')

<div class="space-y-6">
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Users List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">All Users ({{ $users->total() }})</h2>
        </div>

        <div class="p-6">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 bg-gray-50">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">NAME</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">EMAIL</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ROLE</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">CREATED</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $roleColors = [
                                                'super_admin' => 'bg-red-100 text-red-800',
                                                'admin' => 'bg-purple-100 text-purple-800',
                                                'employee' => 'bg-blue-100 text-blue-800',
                                                'hr' => 'bg-orange-100 text-orange-800'
                                            ];
                                            $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $roleColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 text-xs">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('super_admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                                <i class="fas fa-edit mr-1"></i> Edit Role
                                            </a>
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('super_admin.users.delete', $user->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold" 
                                                        onclick="return confirm('Delete this user? This action cannot be undone.')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">Delete</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="py-12 text-center">
                    <i class="fas fa-users text-gray-300 text-4xl mb-3 block"></i>
                    <p class="text-gray-600 text-lg font-medium">No users found</p>
                    <p class="text-gray-500 text-sm">Create your first user using the form above</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
