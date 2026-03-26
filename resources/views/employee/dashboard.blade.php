@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'Employee Portal')

@section('content')

<div class="space-y-6">
    <!-- Profile Section -->
    <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl shadow-lg border border-gradient-to-r from-blue-200 to-purple-200 p-8 relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-200 to-transparent opacity-10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-gradient-to-tr from-purple-200 to-transparent opacity-5 rounded-full -ml-48 -mb-48"></div>
        
        <div class="relative z-10">
            <div class="flex items-start gap-8">
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg ring-3 ring-white ring-opacity-50">
                        <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <!-- Today's Status Indicator Dot -->
                    @php
                        $statusColor = match($todayStatus) {
                            'present' => 'bg-green-500',
                            'late' => 'bg-orange-500',
                            'absent' => 'bg-red-500',
                            default => 'bg-gray-400'
                        };
                    @endphp
                    <div class="absolute -top-1 -right-1">
                        <div class="w-5 h-5 {{ $statusColor }} rounded-full shadow-lg ring-2 ring-white animate-pulse"></div>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-4xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-lg font-medium mb-4">{{ $profile['position'] ?? 'N/A' }} • <span class="text-gray-500">{{ $profile['faculty_id'] ?? 'N/A' }}</span> • <span class="text-gray-500">{{ $profile['department'] ?? 'N/A' }}</span></p>
                    
                    <div class="flex items-center gap-3 mt-6 flex-wrap">
                        @if($user->face_enrolled)
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 flex items-center gap-2 ring-1 ring-green-200">
                                <i class="fas fa-check-circle"></i> Face Enrolled
                            </span>
                        @endif
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 ring-1 ring-blue-200">
                            <i class="fas fa-briefcase text-xs mr-1"></i>{{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                <!-- Right Stats -->
                <div class="flex gap-6 flex-shrink-0">
                    <div class="text-center bg-white rounded-xl p-5 shadow-md border border-gray-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 transform hover:-translate-y-1 min-w-max">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-600 mb-2">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="text-4xl font-bold text-green-600">{{ $daysPresent ?? 0 }}</p>
                        <p class="text-gray-600 text-xs font-semibold mt-2 uppercase tracking-wide">Days Present<br>This Month</p>
                    </div>
                    <div class="text-center bg-white rounded-xl p-5 shadow-md border border-gray-100 hover:shadow-lg hover:border-red-200 transition-all duration-300 transform hover:-translate-y-1 min-w-max">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 text-red-600 mb-2">
                            <i class="fas fa-times"></i>
                        </div>
                        <p class="text-4xl font-bold text-red-600">{{ $absences ?? 0 }}</p>
                        <p class="text-gray-600 text-xs font-semibold mt-2 uppercase tracking-wide">Absences<br>This Month</p>
                    </div>
                    <div class="text-center bg-white rounded-xl p-5 shadow-md border border-gray-100 hover:shadow-lg hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 min-w-max">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 text-orange-600 mb-2">
                            <i class="fas fa-clock"></i>
                        </div>
                        <p class="text-4xl font-bold text-orange-600">{{ $lateArrivals ?? 0 }}</p>
                        <p class="text-gray-600 text-xs font-semibold mt-2 uppercase tracking-wide">Late<br>This Month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campus Updates Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col hover:shadow-2xl transition-all duration-300">
        <!-- Gradient Header -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 px-6 py-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2">
                        <i class="fas fa-bullhorn text-xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Campus Updates</h3>
                </div>
                <p class="text-blue-100 text-xs">Latest news and announcements</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="px-6 pt-4 flex gap-2 border-b border-gray-200">
            <button class="tab-btn active px-3 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 hover:text-blue-700" data-tab="all">
                All
            </button>
            <button class="tab-btn px-3 py-2 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800" data-tab="events">
                <i class="fas fa-calendar-alt mr-1"></i>Events
            </button>
            <button class="tab-btn px-3 py-2 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-800" data-tab="announcements">
                <i class="fas fa-bullhorn mr-1"></i>Announcements
            </button>
        </div>

        <!-- Content List -->
        <div class="space-y-3 max-h-96 overflow-y-auto p-4 flex-1" id="items-container">
            @php
                // Combine announcements and events with type indicators
                $combinedItems = collect();
                foreach($announcements as $announcement) {
                    $announcement = (object)$announcement;
                    $announcement->type = 'announcement';
                    $combinedItems->push($announcement);
                }
                foreach($events as $event) {
                    $event = (object)$event;
                    $event->type = 'event';
                    $combinedItems->push($event);
                }
                $combinedItems = $combinedItems->sortByDesc(function($item) {
                    if($item->type === 'event') {
                        return $item->start_date->timestamp;
                    }
                    return $item->published_at->timestamp;
                })->values();
            @endphp

            @forelse($combinedItems as $item)
                @if($item->type === 'event')
                    <div class="item-card item-events bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 hover:border-blue-400 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->title }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $item->start_date->format('M d, Y') }} • {{ $item->location ?? 'TBD' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="item-card item-announcements bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200 hover:border-emerald-400 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bullhorn text-emerald-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->title }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <i class="fas fa-flag text-xs"></i> 
                                    {{ ucfirst($item->priority ?? 'normal') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-500 text-sm py-8" id="empty-state" style="display: block;">No items to display</p>
            @endforelse
        </div>
    </div>

    <!-- Tab Filtering Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const itemsContainer = document.getElementById('items-container');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const selectedTab = this.getAttribute('data-tab');
                    
                    // Update active tab styling
                    tabBtns.forEach(b => {
                        b.classList.remove('active', 'text-blue-600', 'border-b-blue-600');
                        b.classList.add('text-gray-600', 'border-b-transparent');
                    });
                    this.classList.add('active', 'text-blue-600', 'border-b-blue-600');
                    
                    // Filter items
                    const items = itemsContainer.querySelectorAll('.item-card');
                    const emptyState = document.getElementById('empty-state');
                    let visibleCount = 0;
                    
                    items.forEach(item => {
                        if(selectedTab === 'all' || item.classList.contains(`item-${selectedTab}`)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                });
            });
        });
    </script>

    <!-- DTR, Campus Updates, and Leave Request -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 auto-rows-max">
        <!-- Left Column: DTR -->
        <div class="lg:col-span-2">
            <!-- DTR -->
            @include('components.attendance-history', [
                'records' => $attendanceRecords,
                'daysData' => $daysData ?? [],
                'totalHours' => $totalHours ?? 0,
                'totalMinutes' => $totalMinutes ?? 0,
                'title' => 'Daily Time Record (DTR)',
                'subtitle' => now()->format('F Y') . ' — Current Month'
            ])
        </div>

        <!-- Right Column: Leave Request Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col hover:shadow-2xl transition-all duration-300">
            <!-- Gradient Header -->
            <div class="bg-gradient-to-br from-purple-600 via-purple-600 to-indigo-700 px-8 py-7 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-purple-400 opacity-5 rounded-full -ml-16 -mb-16"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3.5 mb-2">
                        <div class="bg-white bg-opacity-20 backdrop-blur-md rounded-xl p-2.5 ring-1 ring-white ring-opacity-30">
                            <i class="fas fa-calendar-times text-2xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Leave Requests</h3>
                    </div>
                    <p class="text-purple-100 text-sm font-medium ml-14">Manage your time off</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="p-6 space-y-3">

                <!-- All Leave Requests -->
                @if($allLeaveRequests->count() > 0)
                    <div class="max-h-96 overflow-y-auto space-y-2.5">
                        <p class="text-xs font-bold text-gray-700 mb-3 uppercase tracking-wide sticky top-0 bg-white">All Leave Requests</p>
                        @foreach($allLeaveRequests as $request)
                            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-3.5 border border-indigo-100 hover:border-indigo-300 transition-all duration-200">
                                <div class="flex items-center gap-2.5 mb-2">
                                    <span class="text-lg">
                                        @if($request->leave_type === 'sick') 🏥
                                        @elseif($request->leave_type === 'vacation') 🏖️
                                        @elseif($request->leave_type === 'personal') 👤
                                        @elseif($request->leave_type === 'emergency') 🚨
                                        @else 📋
                                        @endif
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $request->leave_type)) }}</p>
                                    </div>
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200">
                                            <i class="fas fa-hourglass text-xs mr-1"></i> Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 ring-1 ring-green-200">
                                            <i class="fas fa-check text-xs mr-1"></i> OK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 ring-1 ring-red-200">
                                            <i class="fas fa-ban text-xs mr-1"></i> Denied
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-700 font-semibold flex items-center gap-1.5">
                                    <i class="fas fa-calendar text-indigo-600"></i>{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm font-medium">No leave requests yet</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 p-6 bg-gradient-to-r from-gray-50/50 to-indigo-50/50">
                <a href="{{ route('employee.leave-requests.create') }}" class="w-full bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white font-bold py-3 px-4 rounded-xl hover:shadow-lg hover:from-blue-600 hover:via-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2.5 text-sm ring-1 ring-blue-400/50">
                    <i class="fas fa-plus text-lg"></i> New Leave Request
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
