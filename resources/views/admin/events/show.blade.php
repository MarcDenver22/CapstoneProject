@extends('layouts.app')

@section('header', $event->title)
@section('subheader', 'Event Details')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        <div class="mb-6">
            <div class="mb-4">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    @if($event->status === 'upcoming') bg-blue-100 text-blue-800
                    @elseif($event->status === 'ongoing') bg-purple-100 text-purple-800
                    @elseif($event->status === 'completed') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($event->status) }}
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $event->title }}</h1>

            <div class="text-gray-600 space-y-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar text-blue-600"></i>
                    <span>{{ $event->start_date->format('l, F j, Y \a\t g:i A') }}</span>
                    @if($event->end_date)
                        <span> → {{ $event->end_date->format('l, F j, Y \a\t g:i A') }}</span>
                    @endif
                </div>
                @if($event->location)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-600"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <i class="fas fa-user text-gray-600"></i>
                    <span>Created by {{ $event->creator->name ?? 'System' }} on {{ $event->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        @if($event->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Description</h2>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $event->description }}</p>
            </div>
        @endif

        <div class="flex gap-4 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ auth()->user()->role === 'hr' ? route('hr.events.edit', $event) : route('admin.events.edit', $event) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-edit mr-2"></i> Edit Event
            </a>
            <form action="{{ auth()->user()->role === 'hr' ? route('hr.events.destroy', $event) : route('admin.events.destroy', $event) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold" onclick="return confirm('Are you sure you want to delete this event?')">
                    <i class="fas fa-trash mr-2"></i> Delete Event
                </button>
            </form>
            <a href="{{ auth()->user()->role === 'hr' ? route('hr.events.index') : route('admin.events.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                Back to Events
            </a>
        </div>
    </div>
</div>

@endsection
