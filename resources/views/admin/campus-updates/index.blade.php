@extends('layouts.app')

@section('title', 'Campus Updates')
@section('header', 'Campus Updates')
@section('subheader', 'Manage campus updates')

@section('content')

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Tab Buttons -->
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <button class="tab-btn active text-blue-600 border-b-2 border-blue-600 px-4 py-2 font-semibold transition" data-tab="announcements">
        Announcements
    </button>
</div>

<!-- Events Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="text-left py-3 px-4 font-semibold text-gray-700">TITLE</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">CONTENT</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">DATES</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS/PRIORITY</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">CREATOR</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <!-- Announcements Rows -->
            @forelse($announcements as $announcement)
                <tr class="item-card item-announcements border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <p class="font-semibold text-gray-800">{{ $announcement->title }}</p>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-xs text-gray-500">{{ Str::limit($announcement->content, 50) }}</p>
                    </td>
                    <td class="py-3 px-4 text-gray-600 text-xs">
                        @if($announcement->published_at)
                            <div><strong>Published:</strong> {{ $announcement->published_at->format('M d, Y H:i') }}</div>
                        @else
                            <div><em>Not published yet</em></div>
                        @endif
                        @if($announcement->expires_at)
                            <div class="text-gray-500"><strong>Expires:</strong> {{ $announcement->expires_at->format('M d, Y H:i') }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex flex-col gap-1">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                @if($announcement->priority === 'high') bg-red-100 text-red-800
                                @elseif($announcement->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif" style="width: fit-content;">
                                {{ ucfirst($announcement->priority) }} Priority
                            </span>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                @if($announcement->status === 'active') bg-green-100 text-green-800
                                @elseif($announcement->status === 'inactive') bg-gray-100 text-gray-800
                                @else bg-blue-100 text-blue-800
                                @endif" style="width: fit-content;">
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        {{ $announcement->creator->name ?? 'System' }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            @php
                                $editAnnouncementUrl = auth()->user()->role === 'hr'
                                    ? url('hr/announcements/' . ($announcement->id ?? '') . '/edit')
                                    : route('admin.announcements.edit', $announcement);
                            @endphp
                            <a href="{{ $editAnnouncementUrl }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if($announcement && $announcement->id)
                                @php
                                    $deleteAnnouncementUrl = auth()->user()->role === 'hr'
                                        ? route('hr.announcements.destroy', ['id' => $announcement->id])
                                        : route('admin.announcements.destroy', $announcement);
                                @endphp
                                <form action="{{ $deleteAnnouncementUrl }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $announcement->id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse

            @if($announcements->count() === 0)
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        No announcements found. <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.create') : route('admin.announcements.create') }}" class="text-blue-600 hover:underline">Create an announcement</a>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="flex justify-end mt-6">
    <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.create') : route('admin.announcements.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <i class="fas fa-plus"></i>
        Add Announcement
    </a>
</div>

<script>
    // Initialize tab filtering on page load
    function initializeTabFilter() {
        const items = document.querySelectorAll('.item-card');
        // Show all items initially
        items.forEach(item => {
            item.style.display = 'table-row';
        });
    }

    // Run initialization when page loads
    document.addEventListener('DOMContentLoaded', function () {
        initializeTabFilter();

    });
    
    // Tab click handlers
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active tab styling
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                b.classList.add('text-gray-600', 'border-transparent');
            });
            this.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
            this.classList.remove('text-gray-600', 'border-transparent');
            
            // Filter items
            const tab = this.dataset.tab;
            const items = document.querySelectorAll('.item-card');
            
            items.forEach(item => {
                if (tab === 'all' || item.classList.contains(`item-${tab}`)) {
                    item.style.display = 'table-row';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</div>

@endsection
