@extends('layouts.app')

@section('header', 'Manage Announcements')
@section('subheader', 'Create, edit, and manage system announcements')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Announcements</h1>
    <a href="{{ route('admin.announcements.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
        <i class="fas fa-plus"></i> Create Announcement
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Announcements Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="text-left py-3 px-4 font-semibold text-gray-700">TITLE</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">PRIORITY</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">PUBLISHED</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">EXPIRES</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">CREATOR</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $announcement)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <p class="font-semibold text-gray-800">{{ $announcement->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($announcement->content, 50) }}</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            @if($announcement->priority === 'high') bg-red-100 text-red-800
                            @elseif($announcement->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            @if($announcement->status === 'active') bg-green-100 text-green-800
                            @elseif($announcement->status === 'inactive') bg-gray-100 text-gray-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($announcement->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600 text-xs">
                        {{ $announcement->published_at?->format('M d, Y H:i') ?? 'Not published' }}
                    </td>
                    <td class="py-3 px-4 text-gray-600 text-xs">
                        {{ $announcement->expires_at?->format('M d, Y H:i') ?? 'Never' }}
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        {{ $announcement->creator->name ?? 'System' }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.edit', $announcement) : route('admin.announcements.edit', $announcement) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ auth()->user()->role === 'hr' ? route('hr.announcements.destroy', $announcement) : route('admin.announcements.destroy', $announcement) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-500">
                        No announcements found. <a href="{{ route('admin.announcements.create') }}" class="text-blue-600 hover:underline">Create one</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($announcements->hasPages())
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>
@endif

@endsection
