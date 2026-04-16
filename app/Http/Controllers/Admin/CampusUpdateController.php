<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CampusUpdateController extends Controller
{
    /**
     * Show announcements index.
     */
    public function index()
    {
        // Show only non-archived announcements by default
        $announcements = Announcement::on('supabase')
            ->where('status', '!=', 'archived')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.campus-updates.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(string $type)
    {
        if ($type === 'announcement') {
            return view('admin.campus-updates.announcements.create');
        }

        abort(404);
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request, string $type)
    {
        if ($type === 'announcement') {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:active,inactive,archived',
                'published_at' => 'nullable|date_format:Y-m-d\\TH:i',
                'expires_at' => 'nullable|date_format:Y-m-d\\TH:i',
            ]);

            $validated['created_by'] = Auth::id();

            if (empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }

            $announcement = Announcement::create($validated);

            AuditLogger::logCreate('Announcement', $announcement->id, [
                'title' => $announcement->title,
                'priority' => $announcement->priority,
                'status' => $announcement->status,
            ]);
        } else {
            abort(404);
        }

        $user = Auth::user();
        $indexRoute = $user && $user->role === 'hr' ? 'hr.announcements.index' : 'admin.announcements.index';

        return redirect()->route($indexRoute)->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the specified announcement.
     */
    public function show(string $id, string $type = 'announcement')
    {
        if ($type === 'announcement') {
            $announcement = Announcement::findOrFail($id);
            return view('admin.campus-updates.announcements.show', compact('announcement'));
        }

        abort(404);
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(string $id, string $type = 'announcement')
    {
        if ($type === 'announcement') {
            $announcement = Announcement::findOrFail($id);
            return view('admin.campus-updates.announcements.edit', compact('announcement'));
        }

        abort(404);
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, string $id, string $type = 'announcement')
    {
        if ($type === 'announcement') {
            $announcement = Announcement::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:active,inactive,archived',
                'published_at' => 'nullable|date_format:Y-m-d\\TH:i',
                'expires_at' => 'nullable|date_format:Y-m-d\\TH:i',
            ]);

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

            if (!empty($changes)) {
                AuditLogger::logUpdate('Announcement', $announcement->id, $changes);
            }
        } else {
            abort(404);
        }

        $user = Auth::user();
        $indexRoute = $user && $user->role === 'hr' ? 'hr.announcements.index' : 'admin.announcements.index';

        return redirect()->route($indexRoute)->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(string $id, string $type = 'announcement')
    {
        if ($type === 'announcement') {
            $announcement = Announcement::findOrFail($id);
            $announcementData = $announcement->toArray();

            Log::info('Delete announcement started', ['id' => $announcement->id, 'current_status' => $announcement->status]);

            try {
                $announcement->status = 'archived';
                $announcement->save();

                Log::info('Delete announcement completed', ['id' => $announcement->id, 'new_status' => $announcement->status]);
            } catch (\Exception $e) {
                Log::error('Delete announcement failed', ['id' => $announcement->id, 'error' => $e->getMessage()]);

                $user = Auth::user();
                $indexRoute = $user && $user->role === 'hr' ? 'hr.announcements.index' : 'admin.announcements.index';

                return redirect()->route($indexRoute)
                    ->with('error', 'Failed to delete announcement: ' . $e->getMessage());
            }

            AuditLogger::logDelete('Announcement', $announcement->id, $announcementData);
        } else {
            abort(404);
        }

        $user = Auth::user();
        $indexRoute = $user && $user->role === 'hr' ? 'hr.announcements.index' : 'admin.announcements.index';

        return redirect()->route($indexRoute)->with('success', 'Announcement deleted successfully!');
    }
}

