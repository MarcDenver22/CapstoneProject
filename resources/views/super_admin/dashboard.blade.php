@extends('layouts.app')

@section('header', 'Super Admin Dashboard')
@section('subheader', 'System Overview & Monitoring')

@section('content')

<style>
    .scrollable-on-hover {
        overflow-y: scroll;
        scrollbar-width: none; /* Firefox */
    }
    
    .scrollable-on-hover::-webkit-scrollbar {
        display: none; /* Chrome, Safari */
    }
    
    .scrollable-on-hover:hover {
        scrollbar-width: auto; /* Firefox - show on hover */
    }
    
    .scrollable-on-hover:hover::-webkit-scrollbar {
        display: block; /* Chrome, Safari - show on hover */
        width: 6px;
    }
    
    .scrollable-on-hover:hover::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .scrollable-on-hover:hover::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    
    .scrollable-on-hover:hover::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
</style>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">

    <!-- Total Users -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Users</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
        </div>
    </div>

    <!-- Active Today -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Active Today</p>
            <p class="text-2xl font-bold text-gray-800">{{ $activeTodayCount }}</p>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-heartbeat text-purple-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">System Health</p>
            <p class="text-2xl font-bold text-gray-800">98%</p>
        </div>
    </div>

    <!-- Recognition Rate -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-face-smile text-indigo-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Recognition Rate</p>
            <p class="text-2xl font-bold text-gray-800">96%</p>
        </div>
    </div>

    <!-- Database Size -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-database text-pink-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Database Size</p>
            <p class="text-2xl font-bold text-gray-800">245 MB</p>
        </div>
    </div>

</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- System Alerts -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            <h3 class="text-lg font-bold text-gray-800">System Alerts</h3>
        </div>
        <div class="space-y-3">
            <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                <p class="text-sm font-semibold text-yellow-800">Database Backup Pending</p>
                <p class="text-xs text-yellow-700 mt-1">Last backup was 2 days ago</p>
            </div>
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-200">
                <p class="text-sm font-semibold text-blue-800">Scheduled Maintenance</p>
                <p class="text-xs text-blue-700 mt-1">Scheduled for tomorrow at 2:00 AM</p>
            </div>
            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                <p class="text-sm font-semibold text-green-800">All Systems Operational</p>
                <p class="text-xs text-green-700 mt-1">No critical issues detected</p>
            </div>
        </div>
    </div>

    <!-- Storage Usage -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-hard-drive text-orange-600"></i>
            <h3 class="text-lg font-bold text-gray-800">Storage Usage</h3>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm text-gray-600">Database</p>
                    <p class="text-sm font-semibold text-gray-800">245 MB / 500 MB</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600" style="width: 49%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm text-gray-600">Face Data</p>
                    <p class="text-sm font-semibold text-gray-800">156 MB / 300 MB</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-green-600" style="width: 52%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm text-gray-600">Logs & Reports</p>
                    <p class="text-sm font-semibold text-gray-800">89 MB / 200 MB</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-600" style="width: 45%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-bolt text-cyan-600"></i>
            <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
        </div>
        <div class="space-y-2">
            <button class="w-full px-4 py-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-shield-alt"></i> Backup Database
            </button>
            <button class="w-full px-4 py-2 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 font-semibold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-sync"></i> Sync System
            </button>
            <button class="w-full px-4 py-2 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600 font-semibold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-file-export"></i> Export Reports
            </button>
            <button class="w-full px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-tools"></i> System Settings
            </button>
        </div>
    </div>

</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i>
                <h3 class="text-lg font-bold text-gray-800">Recent Activities</h3>
            </div>
            <a href="{{ route('super_admin.audit_logs') }}" class="text-blue-600 text-sm font-semibold hover:underline">View All →</a>
        </div>
        <div class="space-y-3 max-h-96 scrollable-on-hover pr-2">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3 pb-3 @if(!$loop->last) border-b border-gray-200 @endif">
                    <!-- Action Icon -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        @if($activity->action === 'create')
                            bg-green-100
                        @elseif($activity->action === 'update')
                            bg-yellow-100
                        @elseif($activity->action === 'delete')
                            bg-red-100
                        @elseif($activity->action === 'login')
                            bg-blue-100
                        @else
                            bg-gray-100
                        @endif
                    ">
                        <i class="fas
                            @if($activity->action === 'create')
                                fa-plus text-green-600
                            @elseif($activity->action === 'update')
                                fa-edit text-yellow-600
                            @elseif($activity->action === 'delete')
                                fa-trash text-red-600
                            @elseif($activity->action === 'login')
                                fa-sign-in-alt text-blue-600
                            @elseif($activity->action === 'logout')
                                fa-sign-out-alt text-gray-600
                            @elseif($activity->action === 'export')
                                fa-download text-purple-600
                            @else
                                fa-circle text-gray-600
                            @endif
                        text-sm"></i>
                    </div>
                    
                    <!-- Activity Details -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ ucfirst($activity->action) }} - {{ $activity->model_type }}
                        </p>
                        <p class="text-xs text-gray-600">
                            <span class="font-medium">{{ $activity->user?->name ?? 'System' }}</span>
                            @if($activity->changes && count($activity->changes) > 0)
                                made changes to $activity->model_type
                            @else
                                performed {{ $activity->action }} on {{ $activity->model_type }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500">
                    <i class="fas fa-inbox text-2xl mb-2"></i>
                    <p class="text-sm">No recent activities</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- System Performance -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-6">
            <i class="fas fa-chart-line text-green-600"></i>
            <h3 class="text-lg font-bold text-gray-800">System Performance</h3>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-800">CPU Usage</p>
                    <p class="text-sm text-gray-600">45%</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600" style="width: 45%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-800">Memory Usage</p>
                    <p class="text-sm text-gray-600">62%</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-600" style="width: 62%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-800">Disk Usage</p>
                    <p class="text-sm text-gray-600">38%</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-green-600" style="width: 38%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-800">Network I/O</p>
                    <p class="text-sm text-gray-600">28%</p>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-600" style="width: 28%"></div>
                </div>
            </div>
            <div class="mt-4 p-4 rounded-lg bg-green-50 border border-green-200">
                <p class="text-sm text-green-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> All Systems Running Smoothly
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Recent Users Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Recently Registered Users</h2>
            <p class="text-sm text-gray-500">Latest 10 user registrations</p>
        </div>
        <a href="{{ route('super_admin.users') }}" class="text-blue-600 text-sm font-semibold hover:underline">View All →</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-300 bg-gray-50">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">USER NAME</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">EMAIL</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">ROLE</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">REGISTERED</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-gray-600 text-sm">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                {{ $user->role === 'super_admin' ? 'bg-red-100 text-red-700' : ($user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="flex items-center gap-2 text-sm">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="text-gray-600">Active</span>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 text-sm">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="py-3 px-4">
                            <a href="#" class="text-blue-600 text-sm font-semibold hover:underline">Edit</a>
                            <span class="text-gray-400 mx-2">•</span>
                            <a href="#" class="text-red-600 text-sm font-semibold hover:underline">Delete</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            No users registered yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
