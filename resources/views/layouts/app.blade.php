<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Faculty Employee Portal</title>

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
        <aside class="glass-sidebar w-[72px] rounded-2xl flex flex-col items-center py-5 flex-shrink-0 shadow-sm">

            <!-- Logo -->
            <div class="mb-6">
                <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="w-10 h-10 rounded-xl object-contain">
            </div>

            <!-- Divider -->
            <div class="sidebar-divider"></div>

            <!-- Main Nav Icons -->
            <nav class="flex flex-col items-center gap-1.5 mt-3 flex-1">

                <!-- Dashboard (active) -->
                <div class="nav-item relative">
                    <a href="{{ route('dashboard') }}" class="nav-icon-btn active">
                        <i class="fas fa-th-large text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Dashboard</span>
                </div>

                <!-- Employee -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-users text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Employees</span>
                </div>

                <!-- System Logs -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-clipboard-list text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">System Logs</span>
                </div>

                <!-- Attendance -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-calendar-check text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Attendance</span>
                </div>

                <!-- Reports -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-chart-bar text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Reports</span>
                </div>

                <!-- Divider -->
                <div class="sidebar-divider my-2"></div>

                <!-- Schedules -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-calendar-alt text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Schedules</span>
                </div>

                <!-- Departments -->
                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-building text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Departments</span>
                </div>

            </nav>

            <!-- Bottom: Settings & Profile -->
            <div class="flex flex-col items-center gap-1.5 mt-auto">
                <div class="sidebar-divider mb-2"></div>

                <div class="nav-item relative">
                    <a href="#" class="nav-icon-btn">
                        <i class="fas fa-cog text-[17px]"></i>
                    </a>
                    <span class="nav-tooltip">Settings</span>
                </div>

                <!-- Logout -->
                <div class="nav-item relative">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-icon-btn hover:!bg-red-50 hover:!text-red-500">
                            <i class="fas fa-sign-out-alt text-[17px]"></i>
                        </button>
                    </form>
                    <span class="nav-tooltip">Logout</span>
                </div>

                <!-- Profile Avatar -->
                <div class="mt-2">
                    <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm cursor-pointer hover:ring-indigo-200 transition">
                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                    </div>
                </div>
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
                    <!-- Search -->
                    <div class="hidden md:flex items-center bg-white/60 border border-gray-200/60 rounded-xl px-3 py-2 gap-2">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Search..." class="bg-transparent text-sm outline-none w-40 placeholder-gray-400">
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative text-gray-400 hover:text-gray-600 transition w-10 h-10 bg-white/60 border border-gray-200/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bell text-[15px]"></i>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                    </button>

                    <!-- Admin Avatar -->
                    <div class="flex items-center gap-3 ml-2 bg-white/60 border border-gray-200/50 rounded-xl px-3 py-1.5 cursor-pointer hover:bg-white/80 transition">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600 text-sm"></i>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-gray-700 leading-tight">Admin</p>
                            <p class="text-[11px] text-gray-400 leading-tight">Administrator</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-[10px] ml-1"></i>
                    </div>
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