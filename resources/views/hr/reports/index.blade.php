@extends('layouts.app')

@section('header', 'DTR Report')
@section('subheader', 'Generate and export DTR reports')

@section('content')
<div class="space-y-6">
    <!-- Report Type Selection -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Daily Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer" onclick="document.getElementById('dailyForm').scrollIntoView({behavior: 'smooth'})">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 rounded-lg p-3 flex-shrink-0">
                    <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Daily Report</h3>
                    <p class="text-sm text-gray-600 mt-2">View attendance for a specific date</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-blue-600">Generate →</span>
                </div>
            </div>
        </div>

        <!-- Weekly Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer" onclick="document.getElementById('weeklyForm').scrollIntoView({behavior: 'smooth'})">
            <div class="flex items-start gap-4">
                <div class="bg-green-100 rounded-lg p-3 flex-shrink-0">
                    <i class="fas fa-calendar-week text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Weekly Report</h3>
                    <p class="text-sm text-gray-600 mt-2">View attendance for a full week</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-green-600">Generate →</span>
                </div>
            </div>
        </div>

        <!-- Monthly Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer" onclick="document.getElementById('monthlyForm').scrollIntoView({behavior: 'smooth'})">
            <div class="flex items-start gap-4">
                <div class="bg-purple-100 rounded-lg p-3 flex-shrink-0">
                    <i class="fas fa-calendar text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Monthly Report</h3>
                    <p class="text-sm text-gray-600 mt-2">View attendance for a full month</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-purple-600">Generate →</span>
                </div>
            </div>
        </div>

        <!-- Per Employee Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer" onclick="document.getElementById('perEmployeeForm').scrollIntoView({behavior: 'smooth'})">
            <div class="flex items-start gap-4">
                <div class="bg-orange-100 rounded-lg p-3 flex-shrink-0">
                    <i class="fas fa-user text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Per Employee</h3>
                    <p class="text-sm text-gray-600 mt-2">View attendance for a specific employee</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-orange-600">Generate →</span>
                </div>
            </div>
        </div>

        <!-- Per Department Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer" onclick="document.getElementById('perDepartmentForm').scrollIntoView({behavior: 'smooth'})">
            <div class="flex items-start gap-4">
                <div class="bg-red-100 rounded-lg p-3 flex-shrink-0">
                    <i class="fas fa-building text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Per Department</h3>
                    <p class="text-sm text-gray-600 mt-2">View attendance by department/unit</p>
                    <span class="inline-block mt-3 text-sm font-semibold text-red-600">Generate →</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Forms -->
    <div class="space-y-6">
        <!-- Daily Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="dailyForm">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Daily Attendance Report</h3>
            <form action="{{ route('hr.reports.daily') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select name="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Weekly Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="weeklyForm">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Weekly Attendance Report</h3>
            <form action="{{ route('hr.reports.weekly') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select name="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Monthly Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="monthlyForm">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Monthly Attendance Report</h3>
            <form action="{{ route('hr.reports.monthly') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                        <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select name="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Per Employee Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="perEmployeeForm">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Per-Employee Report</h3>
            <form action="{{ route('hr.reports.per-employee') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                        <select name="employee_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select an employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->subMonths(1)->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Per Department Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="perDepartmentForm">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Per-Department Report</h3>
            <form action="{{ route('hr.reports.per-department') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select a department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->subMonths(1)->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
