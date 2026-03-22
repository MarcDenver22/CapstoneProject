@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'Employee Portal')

@section('content')

<div class="space-y-6">
    <!-- Profile Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="flex items-start gap-8">
            <div class="w-32 h-32 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="text-white text-5xl font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $profile['position'] ?? 'N/A' }} • {{ $profile['faculty_id'] ?? 'N/A' }} • {{ $profile['department'] ?? 'N/A' }}</p>
                
                <div class="flex items-center gap-3 mt-4">
                    @if($user->face_enrolled)
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Face Enrolled
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">{{ ucfirst($user->role) }}</span>
                </div>
            </div>

            <!-- Right Stats -->
            <div class="flex gap-8">
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $daysPresent ?? 0 }}</p>
                    <p class="text-gray-600 text-sm mt-1">Days Present<br>This Month</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $absences ?? 0 }}</p>
                    <p class="text-gray-600 text-sm mt-1">Absences<br>This Month</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-orange-600">{{ $lateArrivals ?? 0 }}</p>
                    <p class="text-gray-600 text-sm mt-1">Late<br>This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Time In -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-3">TODAY — TIME IN</p>
            <div class="border-t border-gray-300 pt-3">
                @if($todayTimeIn)
                    <p class="text-2xl font-bold text-green-600">{{ $todayTimeIn->format('H:i') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $todayTimeIn->format('A') }}</p>
                @else
                    <p class="text-gray-600 text-sm">Not checked in yet</p>
                @endif
            </div>
        </div>

        <!-- Time Out -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-3">TODAY — TIME OUT</p>
            <div class="border-t border-gray-300 pt-3">
                @if($todayTimeOut)
                    <p class="text-2xl font-bold text-red-600">{{ $todayTimeOut->format('H:i') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $todayTimeOut->format('A') }}</p>
                @else
                    <p class="text-gray-600 text-sm">Not checked out yet</p>
                @endif
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-3">TODAY — STATUS</p>
            <div class="border-t border-gray-300 pt-3">
                @if($todayStatus && $todayStatus !== 'No status')
                    @if($todayStatus === 'present')
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                            <i class="fas fa-check-circle"></i> Present
                        </span>
                    @elseif($todayStatus === 'late')
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold bg-orange-100 text-orange-700">
                            <i class="fas fa-exclamation-circle"></i> Late
                        </span>
                    @elseif($todayStatus === 'absent')
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            <i class="fas fa-times-circle"></i> Absent
                        </span>
                    @else
                        <p class="text-gray-600 text-sm">{{ ucfirst($todayStatus) }}</p>
                    @endif
                @else
                    <p class="text-gray-600 text-sm">No status yet</p>
                @endif
            </div>
        </div>
    </div>

    <!-- DTR and Leave Request Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- DTR - Left Side (2 columns on lg) -->
        <div class="lg:col-span-2">
            @include('components.attendance-history', [
                'records' => $attendanceRecords,
                'title' => 'Daily Time Record (DTR)',
                'subtitle' => now()->format('F Y') . ' — Last 15 Records'
            ])
        </div>

        <!-- Leave Request Card - Right Side (1 column on lg) -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
            <!-- Gradient Header -->
            <div class="bg-gradient-to-br from-purple-600 to-blue-600 px-6 py-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2">
                            <i class="fas fa-calendar-times text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Leave Requests</h3>
                    </div>
                    <p class="text-blue-100 text-xs">Manage your time off</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="p-4 space-y-3 flex-1">
                <!-- Pending -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-3 border border-yellow-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide font-semibold text-yellow-700">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingLeaves ?? 0 }}</p>
                        </div>
                        <div class="text-yellow-100">
                            <i class="fas fa-hourglass-half text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-3 border border-green-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide font-semibold text-green-700">Approved</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedLeaves ?? 0 }}</p>
                        </div>
                        <div class="text-green-100">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-lg p-3 border border-red-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide font-semibold text-red-700">Rejected</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ $rejectedLeaves ?? 0 }}</p>
                        </div>
                        <div class="text-red-100">
                            <i class="fas fa-ban text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests (if any) -->
                @if($allLeaveRequests->count() > 0)
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <p class="text-xs font-bold text-gray-700 mb-2">Latest Request</p>
                        @php $latestRequest = $allLeaveRequests->first(); @endphp
                        <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-lg">
                                    @if($latestRequest->leave_type === 'sick') 🏥
                                    @elseif($latestRequest->leave_type === 'vacation') 🏖️
                                    @elseif($latestRequest->leave_type === 'personal') 👤
                                    @elseif($latestRequest->leave_type === 'emergency') 🚨
                                    @else 📋
                                    @endif
                                </span>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $latestRequest->leave_type)) }}</p>
                                </div>
                                @if($latestRequest->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-hourglass text-xs mr-1"></i> Pending
                                    </span>
                                @elseif($latestRequest->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <i class="fas fa-check text-xs mr-1"></i> OK
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fas fa-ban text-xs mr-1"></i> Denied
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600">{{ $latestRequest->start_date->format('M d') }} - {{ $latestRequest->end_date->format('M d') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 p-4">
                <a href="{{ route('employee.leave-requests.create') }}" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2.5 rounded-lg hover:shadow-md transition flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-plus"></i> New Request
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
