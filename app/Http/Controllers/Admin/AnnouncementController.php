<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:active,inactive,archived',
            'published_at' => 'nullable|date_format:Y-m-d\TH:i',
            'expires_at' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $validated['created_by'] = Auth::id();

        if (!$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        // Log announcement creation
        AuditLogger::logCreate('Announcement', $announcement->id, [
            'title' => $announcement->title,
            'priority' => $announcement->priority,
            'status' => $announcement->status,
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:active,inactive,archived',
            'published_at' => 'nullable|date_format:Y-m-d\TH:i',
            'expires_at' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        // Track changes
        $changes = [];
        if ($announcement->title !== $validated['title']) {
            $changes['title'] = ['from' => $announcement->title, 'to' => $validated['title']];
        }
        if ($announcement->priority !== $validated['priority']) {
            $changes['priority'] = ['from' => $announcement->priority, 'to' => $validated['priority']];
        }
        if ($announcement->status !== $validated['status']) {
            $changes['status'] = ['from' => $announcement->status, 'to' => $validated['status']];
        }

        $announcement->update($validated);

        // Log announcement update if there are changes
        if (!empty($changes)) {
            AuditLogger::logUpdate('Announcement', $announcement->id, $changes);
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcementData = $announcement->toArray();
        $announcement->delete();

        // Log announcement deletion
        AuditLogger::logDelete('Announcement', $announcement->id, $announcementData);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully!');
    }
}
