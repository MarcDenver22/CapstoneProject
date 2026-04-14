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
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-blue-200 transition">
            <i class="fas fa-users text-blue-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Total Users</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</p>
        </div>
    </div>

    <!-- Active Today -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-green-200 transition">
            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Active Today</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $activeTodayCount }}</p>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-purple-200 transition">
            <i class="fas fa-heartbeat text-purple-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">System Health</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">98%</p>
        </div>
    </div>

    <!-- Recognition Rate -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-indigo-200 transition">
            <i class="fas fa-face-smile text-indigo-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Recognition Rate</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">96%</p>
        </div>
    </div>

    <!-- Database Size -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex items-center gap-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-14 h-14 bg-gradient-to-br from-pink-100 to-pink-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-pink-200 transition">
            <i class="fas fa-database text-pink-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Database Size</p>
            <p class="text-3xl font-bold text-pink-600 mt-1">245 MB</p>
        </div>
    </div>

</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- System Alerts -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="bg-gradient-to-br from-yellow-600 via-orange-600 to-yellow-700 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="relative z-10 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                <h3 class="text-2xl font-bold text-white">System Alerts</h3>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200 hover:border-yellow-300 transition">
                <p class="text-sm font-bold text-yellow-800">Database Backup Pending</p>
                <p class="text-xs text-yellow-700 mt-1">Last backup was 2 days ago</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 hover:border-blue-300 transition">
                <p class="text-sm font-bold text-blue-800">Scheduled Maintenance</p>
                <p class="text-xs text-blue-700 mt-1">Scheduled for tomorrow at 2:00 AM</p>
            </div>
            <div class="p-4 rounded-xl bg-green-50 border border-green-200 hover:border-green-300 transition">
                <p class="text-sm font-bold text-green-800">All Systems Operational</p>
                <p class="text-xs text-green-700 mt-1">No critical issues detected</p>
            </div>
        </div>
    </div>

    <!-- Storage Usage -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="bg-gradient-to-br from-orange-600 via-red-600 to-orange-700 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="relative z-10 flex items-center gap-2">
                <i class="fas fa-hard-drive text-white text-xl"></i>
                <h3 class="text-2xl font-bold text-white">Storage Usage</h3>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <div class="flex justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-800">Database</p>
                    <p class="text-sm font-bold text-gray-700">245 MB / 500 MB</p>
                </div>
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden ring-1 ring-gray-300">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600" style="width: 49%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-800">Face Data</p>
                    <p class="text-sm font-bold text-gray-700">156 MB / 300 MB</p>
                </div>
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden ring-1 ring-gray-300">
                    <div class="h-full bg-gradient-to-r from-green-500 to-green-600" style="width: 52%"></div>
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
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="bg-gradient-to-br from-cyan-600 via-blue-600 to-cyan-700 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="relative z-10 flex items-center gap-2">
                <i class="fas fa-bolt text-white text-xl"></i>
                <h3 class="text-2xl font-bold text-white">Quick Actions</h3>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <button class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-600 font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 border border-blue-200 hover:border-blue-300 hover:shadow-md">
                <i class="fas fa-shield-alt text-lg"></i> Backup Database
            </button>
            <button class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 text-green-600 font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 border border-green-200 hover:border-green-300 hover:shadow-md">
                <i class="fas fa-sync text-lg"></i> Sync System
            </button>
            <button class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 text-purple-600 font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 border border-purple-200 hover:border-purple-300 hover:shadow-md">
                <i class="fas fa-file-export text-lg"></i> Export Reports
            </button>
            <button class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-600 font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 border border-red-200 hover:border-red-300 hover:shadow-md">
                <i class="fas fa-tools text-lg"></i> System Settings
            </button>
        </div>
    </div>

</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <!-- Recent Activities -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-700 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-white text-xl"></i>
                    <h3 class="text-2xl font-bold text-white">Recent Activities</h3>
                </div>
                <a href="{{ route('super_admin.audit_logs') }}" class="text-indigo-100 text-sm font-bold hover:text-white transition">View All →</a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3 max-h-96 scrollable-on-hover pr-2">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition @if(!$loop->last) border-b border-gray-100 @endif">
                        <!-- Action Icon -->
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold
                            @if($activity->action === 'create')
                                bg-green-100 text-green-600
                            @elseif($activity->action === 'update')
                                bg-yellow-100 text-yellow-600
                            @elseif($activity->action === 'delete')
                                bg-red-100 text-red-600
                            @elseif($activity->action === 'login')
                                bg-blue-100 text-blue-600
                            @else
                                bg-gray-100 text-gray-600
                            @endif
                        ">
                            <i class="fas
                                @if($activity->action === 'create')
                                    fa-plus
                                @elseif($activity->action === 'update')
                                    fa-edit
                                @elseif($activity->action === 'delete')
                                    fa-trash
                                @elseif($activity->action === 'login')
                                    fa-sign-in-alt
                                @elseif($activity->action === 'logout')
                                    fa-sign-out-alt
                                @elseif($activity->action === 'export')
                                    fa-download
                                @else
                                    fa-circle
                                @endif
                            "></i>
                        </div>
                        
                        <!-- Activity Details -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800">
                                {{ ucfirst($activity->action) }} - {{ $activity->model_type }}
                            </p>
                            <p class="text-xs text-gray-600">
                                <span class="font-semibold">{{ $activity->user?->name ?? 'System' }}</span>
                                @if($activity->changes && count($activity->changes) > 0)
                                    made changes to {{ $activity->model_type }}
                                @else
                                    performed {{ $activity->action }} on {{ $activity->model_type }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-sm font-medium">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- System Performance -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
        <div class="bg-gradient-to-br from-green-600 via-emerald-600 to-green-700 px-8 py-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="relative z-10 flex items-center gap-2">
                <i class="fas fa-chart-line text-white text-xl"></i>
                <h3 class="text-2xl font-bold text-white">System Performance</h3>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <div class="flex justify-between mb-3">
                    <p class="text-sm font-bold text-gray-800">CPU Usage</p>
                    <p class="text-sm font-bold text-gray-700">45%</p>
                </div>
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden ring-1 ring-gray-300">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600" style="width: 45%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-3">
                    <p class="text-sm font-bold text-gray-800">Memory Usage</p>
                    <p class="text-sm font-bold text-gray-700">62%</p>
                </div>
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden ring-1 ring-gray-300">
                    <div class="h-full bg-gradient-to-r from-orange-500 to-orange-600" style="width: 62%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-3">
                    <p class="text-sm font-bold text-gray-800">Disk Usage</p>
                    <p class="text-sm font-bold text-gray-700">38%</p>
                </div>
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden ring-1 ring-gray-300">
                    <div class="h-full bg-gradient-to-r from-green-500 to-green-600" style="width: 38%"></div>
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
        <a href="{{ route('super_admin.users.index') }}" class="text-blue-600 text-sm font-semibold hover:underline">View All →</a>
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
