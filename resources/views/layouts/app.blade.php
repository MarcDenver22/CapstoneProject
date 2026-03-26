<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Faculty Employee Portal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/psu logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/psu logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #edeef0;
        }

        /* Glass sidebar container */
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* Glass card for main content header */
        .glass-header {
            background: rgba(255, 255, 255, 0.70);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* Nav icon button base */
        .nav-icon-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: #9ca3af;
            position: relative;
        }

        .nav-icon-btn:hover {
            background: rgba(99, 102, 241, 0.08);
            color: #6366f1;
        }

        .nav-icon-btn.active {
            background: rgba(99, 102, 241, 0.12);
            color: #6366f1;
            box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.15);
        }

        .nav-icon-btn.active::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: #6366f1;
            border-radius: 0 4px 4px 0;
        }

        /* Tooltip on hover */
        .nav-item:hover .nav-tooltip {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .nav-tooltip {
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%) translateX(-8px);
            background: #1f2937;
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 50;
        }

        .nav-tooltip::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
            width: 8px;
            height: 8px;
            background: #1f2937;
        }

        /* Divider line in sidebar */
        .sidebar-divider {
            width: 24px;
            height: 1px;
            background: rgba(0, 0, 0, 0.08);
            margin: 4px 0;
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden p-4 gap-4">

        <!-- ======== GLASS SIDEBAR ======== -->
        <aside class="glass-sidebar w-64 rounded-2xl flex flex-col py-6 px-4 flex-shrink-0 shadow-sm overflow-y-auto">

            <!-- Logo -->
            <div class="mb-8 flex items-center gap-3">
                <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="w-10 h-10 rounded-lg object-contain">
                <div>
                    @if(auth()->user() && auth()->user()->role === 'super_admin')
                        <h1 class="text-lg font-bold text-red-700">Super Admin</h1>
                        <p class="text-xs text-red-600">System Control</p>
                    @elseif(auth()->user() && auth()->user()->role === 'admin')
                        <h1 class="text-lg font-bold text-indigo-700">Admin</h1>
                        <p class="text-xs text-indigo-600">Dashboard</p>
                    @elseif(auth()->user() && auth()->user()->role === 'hr')
                        <h1 class="text-lg font-bold text-purple-700">HR Portal</h1>
                        <p class="text-xs text-purple-600">Management</p>
                    @else
                        <h1 class="text-lg font-bold text-blue-700">FaceTrack</h1>
                        <p class="text-xs text-blue-600">Employee Portal</p>
                    @endif
                </div>
            </div>

            <!-- Navigation Sections -->
            <nav class="overflow-y-auto space-y-6">

                <!-- EMPLOYEE SECTION (for employees and non-HR users) -->
                @if(auth()->user() && auth()->user()->role === 'employee')
                <div>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-3 px-2">My Account</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <i class="fas fa-chart-line w-5 text-center text-gray-400 group-hover:text-blue-600"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <!-- Attendance History -->
                        <a href="{{ route('employee.attendance-history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <i class="fas fa-history w-5 text-center text-gray-400 group-hover:text-blue-600"></i>
                            <span class="text-sm font-medium">Attendance History</span>
                        </a>
                        <!-- Leave Requests -->
                        <a href="{{ route('employee.leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <i class="fas fa-calendar-times w-5 text-center text-gray-400 group-hover:text-blue-600"></i>
                            <span class="text-sm font-medium">Leave Requests</span>
                        </a>
                        <!-- Profile -->
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <i class="fas fa-user w-5 text-center text-gray-400 group-hover:text-blue-600"></i>
                            <span class="text-sm font-medium">Profile</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- HR SECTION (for HR users) -->
                @if(auth()->user() && auth()->user()->role === 'hr')
                <div>
                    <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-3 px-2">My Account</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('hr.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-chart-line w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <!-- Attendance History -->
                        <a href="{{ route('employee.attendance-history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-history w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Attendance History</span>
                        </a>
                        <!-- Leave Requests -->
                        <a href="{{ route('employee.leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-calendar-times w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Leave Requests</span>
                        </a>
                        <!-- Profile -->
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-user w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Profile</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-3 px-2">Management</p>
                    <div class="space-y-1">
                        <!-- Reports -->
                        <a href="{{ route('hr.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-chart-bar w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Attendance Reports</span>
                        </a>
                        <!-- Campus Updates -->
                        <a href="{{ route('hr.events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition group">
                            <i class="fas fa-bullhorn w-5 text-center text-gray-400 group-hover:text-purple-600"></i>
                            <span class="text-sm font-medium">Campus Updates</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- SUPER ADMIN Section (Full system control) -->
                @if(auth()->user() && auth()->user()->role === 'super_admin')
                <div>
                    <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-3 px-2">Super Admin</p>
                    <div class="space-y-1">
                        <!-- System Dashboard -->
                        <a href="{{ route('super_admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-tachometer-alt w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">System Dashboard</span>
                        </a>
                        <!-- User Management -->
                        <a href="{{ route('super_admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-users-cog w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">User Management</span>
                        </a>
                        <!-- System Config -->
                        <a href="{{ route('super_admin.system_config') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-sliders-h w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">System Config</span>
                        </a>
                        <!-- Audit Logs -->
                        <a href="{{ route('super_admin.audit_logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-history w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">Audit Logs</span>
                        </a>
                        <!-- System Health -->
                        <a href="{{ route('super_admin.system_health') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-heartbeat w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">System Health</span>
                        </a>
                        <!-- Profile -->
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition group">
                            <i class="fas fa-user w-5 text-center text-gray-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium">Profile</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- ADMIN MANAGEMENT (Super Admin can also do everything Admin does) -->
                @if(auth()->user() && auth()->user()->role === 'super_admin')
                <div>
                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-3 px-2">Admin Management</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-chart-line w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <!-- Employees -->
                        <a href="{{ route('admin.employees.list') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-users w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Employees</span>
                        </a>
                        <!-- Attendance Log -->
                        <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-calendar-check w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Attendance Log</span>
                        </a>
                    </div>
                </div>

                <!-- REPORTS Section (for Super Admin) -->
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2">Reports</p>
                    <div class="space-y-1">
                        <!-- DTR Reports -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-file-alt w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">DTR Reports</span>
                        </a>
                        <!-- Campus Updates -->
                        <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-bullhorn w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Campus Updates</span>
                        </a>
                        <!-- Export -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-download w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Export</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- ADMIN MAIN Section (only for admin, not super_admin) -->
                @if(auth()->user() && auth()->user()->role === 'admin')
                <div>
                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-3 px-2">Main</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-chart-line w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <!-- Employees -->
                        <a href="{{ route('admin.employees.list') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-users w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Employees</span>
                        </a>
                        <!-- Attendance Log -->
                        <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-calendar-check w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Attendance Log</span>
                        </a>
                        <!-- Profile -->
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-user w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Profile</span>
                        </a>
                    </div>
                </div>

                <!-- REPORTS Section -->
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2">Reports</p>
                    <div class="space-y-1">
                        <!-- DTR Reports -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-file-alt w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">DTR Reports</span>
                        </a>
                        <!-- Campus Updates -->
                        <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-bullhorn w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Campus Updates</span>
                        </a>
                        <!-- Export -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-download w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Export</span>
                        </a>
                    </div>
                </div>

                <!-- SYSTEM Section -->
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2">System</p>
                    <div class="space-y-1">
                        <!-- Audit Logs -->
                        <a href="{{ route('admin.audit_logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-history w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Audit Logs</span>
                        </a>
                        <!-- Settings -->
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-cog w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Settings</span>
                        </a>
                    </div>
                </div>
                @endif

            </nav>

            <!-- Bottom: Profile -->
            <div class="mt-auto border-t border-gray-200 pt-4">
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition group">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>

        </aside>

        <!-- ======== MAIN CONTENT ======== -->
        <div class="flex-1 flex flex-col overflow-hidden rounded-2xl">

            <!-- Top Header Bar (Glass) -->
            <header class="glass-header rounded-t-2xl px-8 py-4 flex items-center justify-between flex-shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                    <p class="text-sm text-gray-400 mt-0.5">@yield('subheader', 'Faculty Employee Portal')</p>
                </div>
                <div class="flex items-center gap-4">
                    @if(auth()->user() && (auth()->user()->role === 'employee' || auth()->user()->role === 'hr'))
                        <!-- Enroll Face Button for Employees -->
                        <a href="{{ route('employee.face_enrollment.show') }}" class="px-4 py-2.5 rounded-lg bg-cyan-500 hover:bg-cyan-600 text-white font-semibold text-sm transition flex items-center gap-2">
                            <i class="fas fa-camera"></i> Enroll Face
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-white/40 rounded-b-2xl p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>