<?php

namespace App\Http\Controllers\HR;

use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class EventController extends Controller
{
    /**
     * Display a listing of events and announcements.
     */
    public function index()
    {
        $events = Event::orderBy('start_date', 'asc')->get();
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.events.index', compact('events', 'announcements'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after:start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $validated['created_by'] = Auth::id();

        $event = Event::create($validated);

        // Log event creation
        AuditLogger::logCreate('Event', $event->id, $validated);

        return redirect()->route('hr.events.show', $event->id)->with('success', 'Event created successfully');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after:start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $event->update($validated);

        // Log event update
        AuditLogger::logUpdate('Event', $event->id, $validated);

        return redirect()->route('hr.events.show', $event->id)->with('success', 'Event updated successfully');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        // Log event deletion
        AuditLogger::logDelete('Event', $event->id);

        return redirect()->route('hr.events.index')->with('success', 'Event deleted successfully');
    }
}
