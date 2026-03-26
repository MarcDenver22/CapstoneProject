<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Dashboard - Faculty Employee Portal</title>

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

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.70);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        }

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

        <!-- ======== GLASS SIDEBAR (Employee) ======== -->
        <aside class="glass-sidebar w-64 rounded-2xl flex flex-col py-6 px-4 flex-shrink-0 shadow-sm overflow-y-auto">

            <!-- Logo -->
            <div class="mb-8 flex items-center gap-3">
                <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="w-10 h-10 rounded-lg object-contain">
                <div>
                    <h1 class="text-lg font-bold text-blue-700">Employee</h1>
                    <p class="text-xs text-blue-600">Portal</p>
                </div>
            </div>

            <!-- Navigation Sections -->
            <nav class="overflow-y-auto space-y-6">

                <!-- MY ACCOUNT Section -->
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2">My Account</p>
                    <div class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group @yield('nav-dashboard')">
                            <i class="fas fa-chart-line w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <!-- Attendance History -->
                        <a href="{{ route('employee.attendance-history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-history w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Attendance History</span>
                        </a>
                        <!-- Leave Requests -->
                        <a href="{{ route('employee.leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-calendar-times w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Leave Requests</span>
                        </a>
                        <!-- Profile -->
                        <a href="{{ route('employee.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition group">
                            <i class="fas fa-user w-5 text-center text-gray-400 group-hover:text-indigo-600"></i>
                            <span class="text-sm font-medium">Profile</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Bottom: Profile Section -->
            <div class="mt-auto border-t border-gray-200 pt-4">
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
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

            <!-- Top Header Bar -->
            <header class="glass-header rounded-t-2xl px-8 py-4 flex items-center justify-between flex-shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                    <p class="text-sm text-gray-500 mt-0.5">@yield('subheader', 'Employee Portal')</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Face Enrollment Button -->
                    @if (!auth()->user()->face_enrolled)
                    <a href="{{ route('employee.face_enrollment.show') }}" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <i class="fas fa-face-smile"></i>
                        <span>Enroll Face</span>
                    </a>
                    @else
                    <div class="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm font-medium rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span>Face Enrolled</span>
                    </div>
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
