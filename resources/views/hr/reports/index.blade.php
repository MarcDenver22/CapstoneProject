@extends('layouts.app')

@section('header', 'DTR Report')
@section('subheader', 'Generate and export attendance reports per employee')

@section('content')
<div class="space-y-6">
    <!-- Per Employee Report Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="perEmployeeForm">
        <h3 class="font-bold text-lg text-gray-800 mb-4">Employee Attendance Report</h3>
        <form action="{{ route('hr.reports.per-employee') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                        <select name="employee_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select an employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->subMonths(1)->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}" required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                    <a href="{{ route('hr.reports.per-employee') }}?employee_id={{ request('employee_id') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}&export=csv" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium" onclick="return confirm('Export as CSV?')">
                        <i class="fas fa-download mr-2"></i>Export as CSV
                    </a>
                    <a href="{{ route('hr.reports.per-employee') }}?employee_id={{ request('employee_id') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}&export=pdf" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium" onclick="return confirm('Export as PDF?')">
                        <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                    </a>
                </div>
        </form>
    </div>
</div>
@endsection
