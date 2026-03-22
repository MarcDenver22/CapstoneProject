<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount = $leaveRequests->where('status', 'pending')->count();
        $approvedCount = $leaveRequests->where('status', 'approved')->count();
        $rejectedCount = $leaveRequests->where('status', 'rejected')->count();

        return view('employee.leave-requests.index', compact(
            'leaveRequests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function create()
    {
        return view('employee.leave-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:sick,vacation,personal,emergency,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();

        LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('employee.leave-requests.index')
            ->with('success', 'Leave request submitted successfully. Awaiting approval.');
    }

    public function show($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Ensure the user can only see their own requests
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403);
        }

        return view('employee.leave-requests.show', compact('leaveRequest'));
    }

    public function edit($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Only pending requests can be edited
        if ($leaveRequest->user_id !== Auth::id() || $leaveRequest->status !== 'pending') {
            abort(403);
        }

        return view('employee.leave-requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->user_id !== Auth::id() || $leaveRequest->status !== 'pending') {
            abort(403);
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,vacation,personal,emergency,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $leaveRequest->update($validated);

        return redirect()->route('employee.leave-requests.show', $leaveRequest->id)
            ->with('success', 'Leave request updated successfully.');
    }

    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->user_id !== Auth::id() || $leaveRequest->status !== 'pending') {
            abort(403);
        }

        $leaveRequest->delete();

        return redirect()->route('employee.leave-requests.index')
            ->with('success', 'Leave request cancelled.');
    }
}
