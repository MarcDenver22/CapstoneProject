@extends('layouts.app')

@section('header', 'Campus Updates')
@section('subheader', 'Manage campus updates')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Campus Updates</h1>
    <div class="flex gap-2">
        <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.create') : route('admin.announcements.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Announcement
        </a>
        <a href="{{ auth()->user()->role === 'hr' ? route('hr.events.create') : route('admin.events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Event
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Tab Buttons -->
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <button class="tab-btn active text-blue-600 border-b-2 border-blue-600 px-4 py-2 font-semibold transition" data-tab="all">
        All
    </button>
    <button class="tab-btn text-gray-600 border-transparent px-4 py-2 font-semibold hover:text-gray-800 transition" data-tab="events">
        Events
    </button>
    <button class="tab-btn text-gray-600 border-transparent px-4 py-2 font-semibold hover:text-gray-800 transition" data-tab="announcements">
        Announcements
    </button>
</div>

<!-- Events Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="text-left py-3 px-4 font-semibold text-gray-700">TITLE</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">TYPE</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">DATE/DETAILS</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS/PRIORITY</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">CREATOR</th>
                <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <!-- Events Rows -->
            @forelse($events as $event)
                <tr class="item-card item-events border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <p class="font-semibold text-gray-800">{{ $event->title }}</p>
                        @if($event->description)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($event->description, 50) }}</p>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar-alt mr-1"></i>Event
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600 text-xs">
                        <div>{{ $event->start_date->format('M d, Y H:i') }}</div>
                        @if($event->end_date)
                            <div class="text-gray-500">to {{ $event->end_date->format('M d, Y H:i') }}</div>
                        @endif
                        @if($event->location)
                            <div class="text-gray-500"><i class="fas fa-map-marker"></i> {{ $event->location }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            @if($event->status === 'upcoming') bg-blue-100 text-blue-800
                            @elseif($event->status === 'ongoing') bg-purple-100 text-purple-800
                            @elseif($event->status === 'completed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        {{ $event->creator->name ?? 'System' }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ auth()->user()->role === 'hr' ? route('hr.events.edit', $event) : route('admin.events.edit', $event) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ auth()->user()->role === 'hr' ? route('hr.events.destroy', $event) : route('admin.events.destroy', $event) }}" method="POST" style="display:inline;">
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
            @endforelse

            <!-- Announcements Rows -->
            @forelse($announcements as $announcement)
                <tr class="item-card item-announcements border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <p class="font-semibold text-gray-800">{{ $announcement->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($announcement->content, 50) }}</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-bullhorn mr-1"></i>Announcement
                        </span>
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
            @endforelse

            @if($events->count() === 0 && $announcements->count() === 0)
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        No items found. <a href="{{ auth()->user()->role === 'hr' ? route('hr.events.create') : route('admin.events.create') }}" class="text-blue-600 hover:underline">Create an event</a> or <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.create') : route('admin.announcements.create') }}" class="text-green-600 hover:underline">announcement</a>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
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
    document.addEventListener('DOMContentLoaded', initializeTabFilter);
    
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
</script>

@endsection
