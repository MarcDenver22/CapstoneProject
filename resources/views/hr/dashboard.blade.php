@extends('layouts.app')

@section('header', 'Dashboard')
@section('subheader', 'HR Management Portal')

@section('content')

<div class="space-y-6">
    <!-- Profile Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="flex items-start gap-8">
            <div class="w-32 h-32 rounded-full bg-purple-600 flex items-center justify-center flex-shrink-0">
                <span class="text-white text-5xl font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $user->position ?? 'N/A' }} • {{ $user->faculty_id ?? 'N/A' }} • {{ $profile->department->name ?? 'No Department' }}</p>
                
                <div class="flex items-center gap-3 mt-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-700">HR</span>
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
                <p class="text-gray-600 text-sm">Time-in tracking available</p>
            </div>
        </div>

        <!-- Time Out -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-3">TODAY — TIME OUT</p>
            <div class="border-t border-gray-300 pt-3">
                <p class="text-gray-600 text-sm">Time-out tracking available</p>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <p class="text-xs uppercase font-semibold text-gray-500 mb-3">COMPANY STATUS</p>
            <div class="border-t border-gray-300 pt-3">
                <p class="text-gray-600 text-sm">{{ $totalEmployees ?? 0 }} Employees</p>
            </div>
        </div>
    </div>

    <!-- DTR and Events/Announcements Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- DTR - Left Side (2 columns on lg) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daily Time Record (DTR)</h3>
                        <p class="text-sm text-gray-600">Company-wide - Last 15 Records</p>
                    </div>
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 bg-gray-50">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">EMPLOYEE</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">DEPT.</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME-IN</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME-OUT</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">LIVENESS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords as $record)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $record->user->name }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $record->user->department->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        @if($record->time_in)
                                            {{ $record->time_in->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        @if($record->time_out)
                                            {{ $record->time_out->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                            @if($record->status === 'present') bg-green-100 text-green-800
                                            @elseif($record->status === 'absent') bg-red-100 text-red-800
                                            @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($record->liveness_verified)
                                            <span class="text-green-600 font-semibold">✓</span>
                                        @else
                                            <span class="text-red-600 font-semibold">✕</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-2xl mb-2"></i>
                                        <p class="mt-2">No attendance records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Events & Announcements - Right Side (1 column on lg) -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col">
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
                        <div class="item-card item-events p-3 border border-gray-200 rounded-lg hover:shadow-md transition" style="display: flex;">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $item->display_date }} • {{ $item->location ?? 'TBD' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="item-card item-announcements p-3 border border-gray-200 rounded-lg hover:shadow-md transition" style="display: flex;">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bullhorn text-green-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
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
                <a href="{{ route('hr.announcements.index') }}" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-2.5 rounded-lg hover:shadow-md transition flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-pen"></i> Manage Announcements
                </a>
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
