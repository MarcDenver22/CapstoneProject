<!-- Leave Request History Component -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Modern Gradient Header -->
    <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-500 px-6 py-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-5 rounded-full -ml-16 -mb-16"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2.5">
                    <i class="fas fa-history text-2xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $title ?? 'Leave Request History' }}</h3>
                    <p class="text-blue-100 text-sm">{{ $subtitle ?? 'Track all your leave requests' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if($records->count() > 0)
            <!-- Timeline View -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 to-transparent"></div>

                <!-- Timeline Items -->
                <div class="space-y-0">
                    @foreach($records as $request)
                        <div class="relative pl-20 pb-8 last:pb-0 group">
                            <!-- Timeline Dot -->
                            <div class="absolute left-0 top-0 w-12 h-12 rounded-full bg-white border-4 border-blue-400 flex items-center justify-center group-hover:border-blue-600 group-hover:shadow-md transition">
                                @if($request->status === 'pending')
                                    <i class="fas fa-hourglass text-yellow-600 text-lg"></i>
                                @elseif($request->status === 'approved')
                                    <i class="fas fa-check text-green-600 text-lg"></i>
                                @else
                                    <i class="fas fa-ban text-red-600 text-lg"></i>
                                @endif
                            </div>

                            <!-- Timeline Card -->
                            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-4 group-hover:border-blue-300 group-hover:shadow-md transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                                    <div class="flex items-start gap-3">
                                        <!-- Emoji -->
                                        <span class="text-3xl">
                                            @if($request->leave_type === 'sick') 🏥
                                            @elseif($request->leave_type === 'vacation') 🏖️
                                            @elseif($request->leave_type === 'personal') 👤
                                            @elseif($request->leave_type === 'emergency') 🚨
                                            @else 📋
                                            @endif
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $request->leave_type)) }} Leave</h4>
                                            <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                <i class="fas fa-hourglass-half text-sm"></i>
                                                <span>Pending</span>
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                                <i class="fas fa-check-circle text-sm"></i>
                                                <span>Approved</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                                <i class="fas fa-times-circle text-sm"></i>
                                                <span>Rejected</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3 pb-3 border-b border-gray-200">
                                    <!-- From Date -->
                                    <div>
                                        <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 mb-1">From</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $request->start_date->format('M d') }}</p>
                                    </div>

                                    <!-- To Date -->
                                    <div>
                                        <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 mb-1">To</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $request->end_date->format('M d, Y') }}</p>
                                    </div>

                                    <!-- Days Count -->
                                    <div>
                                        <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 mb-1">Duration</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $request->days_count }} @if($request->days_count == 1)day @else days @endif</p>
                                    </div>

                                    <!-- Status Label -->
                                    <div>
                                        <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 mb-1">Status</p>
                                        <p class="text-sm font-semibold">
                                            @if($request->status === 'pending')
                                                <span class="text-yellow-600">Pending</span>
                                            @elseif($request->status === 'approved')
                                                <span class="text-green-600">Approved</span>
                                            @else
                                                <span class="text-red-600">Rejected</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Reason -->
                                <div class="mb-3">
                                    <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 mb-1.5">Reason</p>
                                    <p class="text-sm text-gray-700 line-clamp-2">{{ $request->reason }}</p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                                    <a href="{{ route('employee.leave-requests.show', $request->id) }}" class="flex-1 text-center bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-2 px-3 rounded-lg hover:shadow-md transition text-xs sm:text-sm">
                                        <i class="fas fa-eye mr-1"></i> View Details
                                    </a>
                                    
                                    @if($request->status === 'pending')
                                        <a href="{{ route('employee.leave-requests.edit', $request->id) }}" class="flex-1 text-center bg-orange-100 text-orange-700 font-semibold py-2 px-3 rounded-lg hover:bg-orange-200 transition text-xs sm:text-sm border border-orange-200">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                    @endif
                                </div>

                                <!-- Approval Info (if approved) -->
                                @if($request->status === 'approved' && $request->approvedBy)
                                    <div class="mt-3 pt-3 border-t border-green-200 bg-green-50 rounded-lg p-2.5">
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-user-check text-green-600 mr-1"></i>
                                            Approved by <span class="font-semibold">{{ $request->approvedBy->name }}</span>
                                        </p>
                                    </div>
                                @endif

                                <!-- Rejection Info (if rejected) -->
                                @if($request->status === 'rejected' && $request->rejection_reason)
                                    <div class="mt-3 pt-3 border-t border-red-200 bg-red-50 rounded-lg p-2.5">
                                        <p class="text-xs text-gray-600 mb-1">
                                            <i class="fas fa-user-times text-red-600 mr-1"></i>
                                            <span class="font-semibold">Rejection Reason:</span>
                                        </p>
                                        <p class="text-xs text-red-700 ml-5">{{ $request->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 px-4">
                <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-4xl text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">No leave requests yet</h3>
                <p class="text-gray-600 text-sm mb-4">Start by submitting your first leave request</p>
                <a href="{{ route('employee.leave-requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:shadow-md transition">
                    <i class="fas fa-plus"></i> Submit Request
                </a>
            </div>
        @endif
    </div>
</div>
