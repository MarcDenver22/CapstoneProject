@extends('layouts.app')

@section('title', $announcement->title)
@section('header', $announcement->title)
@section('subheader', 'Announcement Details')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        <div class="mb-6">
            <div class="flex gap-2 mb-4">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    @if($announcement->status === 'active') bg-green-100 text-green-800
                    @elseif($announcement->status === 'inactive') bg-gray-100 text-gray-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ ucfirst($announcement->status) }}
                </span>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    @if($announcement->priority === 'high') bg-red-100 text-red-800
                    @elseif($announcement->priority === 'medium') bg-yellow-100 text-yellow-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ ucfirst($announcement->priority) }} Priority
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $announcement->title }}</h1>

            <div class="text-gray-600 space-y-1 text-sm">
                <div>Published: {{ $announcement->published_at?->format('l, F j, Y \a\t g:i A') ?? 'Not published' }}</div>
                @if($announcement->expires_at)
                    <div>Expires: {{ $announcement->expires_at?->format('l, F j, Y \a\t g:i A') }}</div>
                @endif
                <div>Created by {{ $announcement->creator->name ?? 'System' }} on {{ $announcement->created_at?->format('M d, Y') ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Content</h2>
            <div class="text-gray-700 whitespace-pre-wrap">{{ $announcement->content }}</div>
        </div>

        <div class="flex gap-4 mt-6 pt-6 border-t border-gray-200">
            @php
                $editAnnouncementUrl = auth()->user()->role === 'hr'
                    ? url('hr/announcements/' . ($announcement->id ?? '') . '/edit')
                    : route('admin.announcements.edit', $announcement);
            @endphp
            <a href="{{ $editAnnouncementUrl }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold">
                <i class="fas fa-edit mr-2"></i> Edit Announcement
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
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold" onclick="return confirm('Are you sure you want to delete this announcement?')">
                    <i class="fas fa-trash mr-2"></i> Delete Announcement
                </button>
            </form>
            @endif
            <a href="{{ auth()->user()->role === 'hr' ? route('hr.announcements.index') : route('admin.announcements.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                Back to Announcements
            </a>
        </div>
    </div>
</div>

@endsection
