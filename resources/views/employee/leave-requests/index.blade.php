@extends('layouts.app')

@section('header', 'Leave Requests')
@section('subheader', 'Manage your leave requests')

@section('content')

<div class="space-y-6">
    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800">My Leave Requests</h3>
            <p class="text-sm text-gray-600 mt-1">View and manage all your leave requests</p>
        </div>
        <a href="{{ route('employee.leave-requests.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-300 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">TYPE</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">START DATE</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">END DATE</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">DAYS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">SUBMITTED</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $request)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ ucfirst($request->leave_type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->start_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->end_date->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-gray-600 font-medium">{{ $request->days_count }} days</td>
                            <td class="py-3 px-4">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600 text-xs">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('employee.leave-requests.show', $request->id) }}" class="px-2 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition text-xs font-semibold">
                                        View
                                    </a>
                                    @if($request->status === 'pending')
                                        <a href="{{ route('employee.leave-requests.edit', $request->id) }}" class="px-2 py-1 rounded bg-orange-100 text-orange-700 hover:bg-orange-200 transition text-xs font-semibold">
                                            Edit
                                        </a>
                                        <form action="{{ route('employee.leave-requests.cancel', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 transition text-xs font-semibold">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p class="mt-2">No leave requests yet</p>
                                <a href="{{ route('employee.leave-requests.create') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm mt-2 inline-block">
                                    Create your first request
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
