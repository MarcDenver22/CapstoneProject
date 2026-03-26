@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'HR Management Portal')

@section('content')

<div class="space-y-6">
    <!-- Profile Section -->
    <div class="bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 rounded-2xl shadow-lg border border-purple-200 p-8 relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-purple-200 to-transparent opacity-10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-gradient-to-tr from-indigo-200 to-transparent opacity-5 rounded-full -ml-48 -mb-48"></div>
        
        <div class="relative z-10">
            <div class="flex items-start gap-8">
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg ring-3 ring-white ring-opacity-50">
                        <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <!-- HR Badge -->
                    <div class="absolute -bottom-2 -right-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white bg-purple-600 shadow-lg ring-2 ring-white flex items-center gap-1">
                            <i class="fas fa-shield-alt text-xs"></i> HR
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-4xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-lg font-medium mb-4">{{ $user->position ?? 'N/A' }} • <span class="text-gray-500">{{ $user->faculty_id ?? 'N/A' }}</span> • <span class="text-gray-500">{{ $profile->department->name ?? 'No Department' }}</span></p>
                    
                    <div class="flex items-center gap-3 mt-6 flex-wrap">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700 ring-1 ring-purple-200 flex items-center gap-2">
                            <i class="fas fa-user-tie"></i> Human Resources
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

    <!-- Events & Announcements Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col hover:shadow-2xl transition-all duration-300">
            <!-- Gradient Header -->
            <div class="bg-gradient-to-br from-purple-600 to-indigo-600 px-6 py-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-2">
                            <i class="fas fa-bullhorn text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Campus Updates</h3>
                    </div>
                    <p class="text-indigo-100 text-xs">Latest news and announcements</p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="px-6 pt-4 flex gap-2 border-b border-gray-200">
                <button class="tab-btn active px-3 py-2 text-sm font-medium text-purple-600 border-b-2 border-purple-600 hover:text-purple-700" data-tab="all">
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
                @forelse($eventsAndAnnouncements as $item)
                    @if($item->type === 'event')
                        <div class="item-card item-events bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200 hover:border-blue-400 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $item->display_date }} • {{ $item->location ?? 'TBD' }}</p>
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

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 p-4 space-y-2">
                <a href="{{ route('hr.events.index') }}" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2.5 rounded-lg hover:shadow-md transition flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-calendar"></i> Manage Calendar
                </a>
            </div>
        </div>

    <!-- DTR and Leave Request Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- DTR - Left Side (2 columns on lg) -->
        <div class="lg:col-span-2 h-fit">
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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-fit hover:shadow-2xl transition-all duration-300">
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
        </div>
    </div>
</div>

<script>
    function initializeTabFilter() {
        const container = document.getElementById('items-container');
        if (!container) return;
        
        const items = container.querySelectorAll('.item-card');
        const emptyState = document.getElementById('empty-state');
        
        items.forEach(item => {
            item.style.display = 'flex';
        });
        
        if (emptyState) {
            emptyState.style.display = items.length > 0 ? 'none' : 'block';
        }
    }

    function attachTabHandlers() {
        const container = document.getElementById('items-container');
        if (!container) return;
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('active', 'text-purple-600', 'border-purple-600');
                    b.classList.add('text-gray-600', 'border-transparent');
                });
                this.classList.add('active', 'text-purple-600', 'border-purple-600');
                this.classList.remove('text-gray-600', 'border-transparent');
                
                const tab = this.dataset.tab;
                const items = container.querySelectorAll('.item-card');
                const emptyState = document.getElementById('empty-state');
                let visibleCount = 0;
                
                items.forEach(item => {
                    if (tab === 'all' || item.classList.contains(`item-${tab}`)) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                if (emptyState) {
                    emptyState.style.display = visibleCount > 0 ? 'none' : 'block';
                }
            });
        });
    }

    function setupDashboard() {
        initializeTabFilter();
        attachTabHandlers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupDashboard);
    } else {
        setupDashboard();
    }

    window.addEventListener('pageshow', setupDashboard);
    window.addEventListener('popstate', setupDashboard);
</script>

@endsection
