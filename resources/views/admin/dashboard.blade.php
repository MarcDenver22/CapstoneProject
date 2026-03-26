@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'Weekly Attendance Overview')

@section('content')

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    <!-- Total Employees -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-blue-200 transition">
            <i class="fas fa-users text-blue-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Total Employees</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalEmployees ?? 0 }}</p>
        </div>
    </div>

    <!-- Present Today -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-green-200 transition">
            <i class="fas fa-user-check text-green-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Present Today</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $presentToday ?? 0 }}</p>
        </div>
    </div>

    <!-- Absent Today -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-red-100 to-red-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-red-200 transition">
            <i class="fas fa-user-times text-red-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Absent Today</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $absentToday ?? 0 }}</p>
        </div>
    </div>

    <!-- Late Arrivals -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-yellow-200 transition">
            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Late Arrivals</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $lateArrivals ?? 0 }}</p>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Main Content (Left Side) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Weekly Attendance Overview -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 px-8 py-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                            <h2 class="text-2xl font-bold text-white">Weekly Attendance Overview</h2>
                        </div>
                        <p class="text-blue-100 text-sm">This week: Present / Late / Absent</p>
                    </div>
                    <a href="#" class="px-4 py-2 bg-white bg-opacity-20 text-white text-sm font-bold rounded-lg hover:bg-opacity-30 transition backdrop-blur-sm ring-1 ring-white ring-opacity-30">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </a>
                </div>
            </div>
            
            <!-- Attendance Chart -->
            <div class="p-6">
                <div class="flex items-end justify-around h-48 gap-4 mb-6">
                    {{-- Chart data will be populated dynamically --}}
                    <p class="text-gray-500 text-center w-full py-24">No attendance data available yet</p>
                </div>
                
                <!-- Legend -->
                <div class="flex justify-center gap-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 font-medium">Present</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-yellow-400 rounded-full"></div>
                        <span class="text-sm text-gray-600 font-medium">Late</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 font-medium">Absent</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Log -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 px-8 py-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-list text-white text-xl"></i>
                        <h2 class="text-2xl font-bold text-white">Today's Attendance Log</h2>
                    </div>
                    <p class="text-indigo-100 text-sm">Read-only - No direct editing permitted</p>
                </div>
            </div>
            
            <div class="p-6">
                <div class="relative mb-4">
                    <input type="text" placeholder="Search employee..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
                
                <!-- Attendance Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-indigo-300 bg-gradient-to-r from-indigo-50 to-purple-50">
                                <th class="text-left py-4 px-4 font-bold text-gray-800">EMPLOYEE</th>
                                <th class="text-left py-4 px-4 font-bold text-gray-800">DEPT.</th>
                                <th class="text-left py-4 px-4 font-bold text-gray-800">TIME-IN</th>
                                <th class="text-left py-4 px-4 font-bold text-gray-800">TIME-OUT</th>
                                <th class="text-left py-4 px-4 font-bold text-gray-800">STATUS</th>
                                <th class="text-center py-4 px-4 font-bold text-gray-800">LIVENESS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAttendance as $record)
                                <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-150">
                                    <td class="py-3 px-4 font-semibold text-gray-900">{{ $record->user->name }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $record->user->department_name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600 font-medium">
                                        @if($record->time_in)
                                            {{ $record->time_in->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 font-medium">
                                        @if($record->time_out)
                                            {{ $record->time_out->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold ring-1 {{ $record->getStatusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($record->liveness_verified)
                                            <span class="text-green-600 font-bold text-lg">✓</span>
                                        @else
                                            <span class="text-red-600 font-bold text-lg">✕</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b border-gray-100">
                                    <td colspan="6" class="py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                                        <p class="font-medium">No attendance records to display</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (Right Side) -->
    <div class="space-y-6">
        
        <!-- Upcoming Events & Announcements -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-bell text-blue-600"></i>
                <h2 class="text-lg font-bold text-gray-800">Campus Updates</h2>
            </div>
            
            <!-- Filter Tabs -->
            <div class="flex gap-2 mb-4 border-b border-gray-200">
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
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($upcomingEvents as $event)
                    <div class="item-card item-events p-3 border border-gray-200 rounded-lg hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $event->start_date->format('M d') }} • {{ $event->location ?? 'TBD' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse

                @forelse($activeAnnouncements as $announcement)
                    <div class="item-card item-announcements p-3 border border-gray-200 rounded-lg hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bullhorn text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $announcement->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-flag text-xs"></i> 
                                    {{ ucfirst($announcement->priority ?? 'normal') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse

                @if($upcomingEvents->isEmpty() && $activeAnnouncements->isEmpty())
                    <p class="text-center text-gray-500 text-sm py-8" id="empty-state">No items to display</p>
                @endif
            </div>
            
            <!-- Footer Actions -->
            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.events.index') }}" class="text-blue-600 text-sm font-semibold hover:underline inline-flex items-center gap-1 flex-1">
                    <i class="fas fa-calendar"></i> Manage Calendar
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="text-green-600 text-sm font-semibold hover:underline inline-flex items-center gap-1 flex-1">
                    <i class="fas fa-pen"></i> Manage Announcements
                </a>
            </div>
        </div>

        <script>
            // Initialize tab filtering on page load
            function initializeTabFilter() {
                const items = document.querySelectorAll('.item-card');
                const emptyState = document.getElementById('empty-state');
                
                // Show all items initially
                items.forEach(item => {
                    item.style.display = 'block';
                });
                
                // Hide empty state if items exist
                if (items.length > 0) {
                    if (emptyState) emptyState.classList.add('hidden');
                } else {
                    if (emptyState) emptyState.classList.remove('hidden');
                }
            }

            // Run initialization when page loads
            document.addEventListener('DOMContentLoaded', initializeTabFilter);
            
            // Also run on tab click
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active tab
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.classList.remove('active', 'text-blue-600', 'border-blue-600');
                        b.classList.add('text-gray-600', 'border-transparent');
                    });
                    this.classList.add('active', 'text-blue-600', 'border-blue-600');
                    this.classList.remove('text-gray-600', 'border-transparent');
                    
                    // Filter items
                    const tab = this.dataset.tab;
                    const items = document.querySelectorAll('.item-card');
                    const emptyState = document.getElementById('empty-state');
                    let visibleCount = 0;
                    
                    items.forEach(item => {
                        if (tab === 'all' || item.classList.contains(`item-${tab}`)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    emptyState.classList.toggle('hidden', visibleCount > 0);
                });
            });
        </script>

        <!-- Recent Audit Log -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-history text-purple-600"></i>
                <h2 class="text-lg font-bold text-gray-800">Recent Audit Log</h2>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentLogs as $log)
                    <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                        <a href="{{ route('admin.audit_logs.show', $log) }}" class="flex items-start gap-3 hover:opacity-80 transition">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $log->getActionBgColor() }}">
                                <i class="{{ $log->getActionIcon() }} text-xs" style="color: {{ $log->getActionIconColor() }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ ucfirst($log->action) }} {{ Str::lower($log->model_type ?? 'System') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <strong>{{ $log->user?->name ?? 'System' }}</strong> • 
                                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $log->model_type ?? 'System' }}</span>
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-8">No audit logs yet</p>
                @endforelse
            </div>
            
            <div class="mt-4 pt-3 border-t border-gray-200">
                <a href="{{ route('admin.audit_logs.index') }}" class="text-blue-600 text-sm font-semibold hover:underline inline-flex items-center gap-1">
                    View All Logs →
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
