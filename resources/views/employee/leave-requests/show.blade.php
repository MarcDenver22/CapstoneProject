@extends('layouts.app')

@section('title', 'Leave Request Details')
@section('header', 'Leave Request Details')
@section('subheader', 'View your request information')

@section('content')

<div class="max-w-lg">
    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ ucfirst($leaveRequest->leave_type) }} Leave</h2>
                <p class="text-sm text-gray-500">Submitted on {{ $leaveRequest->created_at->format('F d, Y') }}</p>
            </div>
            @if($leaveRequest->status === 'pending')
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                    <i class="fas fa-hourglass-half"></i> Pending
                </span>
            @elseif($leaveRequest->status === 'approved')
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                    <i class="fas fa-check-circle"></i> Approved
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                    <i class="fas fa-times-circle"></i> Rejected
                </span>
            @endif
        </div>
    </div>

    <!-- Details Section -->
    <div class="space-y-6">
        <!-- Leave Type -->
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Leave Type</label>
            <div class="flex items-center gap-2 mt-.5">
                @if($leaveRequest->leave_type === 'sick')
                    <span class="text-2xl">🏥</span>
                @elseif($leaveRequest->leave_type === 'vacation')
                    <span class="text-2xl">🏖️</span>
                @elseif($leaveRequest->leave_type === 'personal')
                    <span class="text-2xl">👤</span>
                @elseif($leaveRequest->leave_type === 'emergency')
                    <span class="text-2xl">🚨</span>
                @else
                    <span class="text-2xl">📋</span>
                @endif
                <p class="text-lg font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $leaveRequest->leave_type)) }}</p>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">From</label>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $leaveRequest->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">To</label>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $leaveRequest->end_date->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Duration -->
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</label>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ $leaveRequest->days_count }} {{ $leaveRequest->days_count == 1 ? 'day' : 'days' }}</p>
        </div>

        <!-- Reason -->
        <div class="border-t border-gray-200 pt-6">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Reason</label>
            <p class="text-gray-700 text-sm leading-relaxed mt-2 whitespace-pre-wrap">{{ $leaveRequest->reason }}</p>
        </div>

        <!-- Approval Info -->
        @if($leaveRequest->status === 'approved' && $leaveRequest->approved_by)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-xs font-bold text-green-700 uppercase tracking-wider mb-1">Approved By</p>
                <p class="text-lg font-bold text-green-900">{{ $leaveRequest->approvedBy->name }}</p>
                <p class="text-xs text-green-700 mt-1">{{ $leaveRequest->updated_at->format('F d, Y \a\t H:i') }}</p>
            </div>
        @elseif($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-xs font-bold text-red-700 uppercase tracking-wider mb-1">Rejection Reason</p>
                <p class="text-gray-900 text-sm">{{ $leaveRequest->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="space-y-3 mt-8 pt-6 border-t border-gray-200">
        @if($leaveRequest->status === 'pending')
            <a href="{{ route('employee.leave-requests.edit', $leaveRequest->id) }}" class="w-full px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-2">
                <i class="fas fa-edit"></i> Edit Request
            </a>
            <form action="{{ route('employee.leave-requests.cancel', $leaveRequest->id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                @csrf
                <button type="submit" class="w-full px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i> Cancel Request
                </button>
            </form>
        @endif
        <a href="{{ route('employee.leave-requests.index') }}" class="w-full px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@endsection
