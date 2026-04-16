@extends('layouts.app')

@section('title', 'Attendance Reports')
@section('header', 'Attendance Reports')
@section('subheader', 'Generate and export attendance reports')

@section('content')
<div class="space-y-6">
    <!-- Report Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-lg text-gray-800 mb-6">Generate Attendance Report</h3>
        
        <form id="reportForm" method="POST" action="{{ route('hr.reports.export-pdf') }}" class="space-y-6">
            @csrf
            
            <!-- Report Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Report Type</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition" id="perEmployeeLabel">
                        <input type="radio" name="report_type" value="per-employee" class="w-5 h-5 text-blue-600" checked onchange="toggleEmployeeSelect()">
                        <span class="ml-3">
                            <span class="block font-medium text-gray-800">Per Employee</span>
                            <span class="block text-sm text-gray-600">Generate report for a specific employee</span>
                        </span>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition" id="allEmployeesLabel">
                        <input type="radio" name="report_type" value="monthly" class="w-5 h-5 text-blue-600" onchange="toggleEmployeeSelect()">
                        <span class="ml-3">
                            <span class="block font-medium text-gray-800">All Employees</span>
                            <span class="block text-sm text-gray-600">Generate report for everyone in a selected month</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employee Selection (for Per Employee) -->
                <div id="employeeSelectDiv">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                    <select name="employee_id" id="employeeSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Choose an employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter (for All Employees) -->
                <div id="departmentSelectDiv" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                    <select name="department_id" id="departmentSelect" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month & Year Selection -->
                <div id="monthSelectDiv">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month & Year</label>
                    <input type="month" name="month" id="monthSelect" value="{{ request('month', now()->format('Y-m')) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Hidden input to store the type value for export-pdf -->
            <input type="hidden" name="type" id="typeInput" value="per-employee">

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                <button type="submit" formaction="{{ route('hr.reports.export-pdf') }}" name="action" value="export" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium inline-flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" onclick="viewReport()" 
                    class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition font-medium inline-flex items-center gap-2">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="reset" 
                    class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition font-medium inline-flex items-center gap-2">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Report Info Panel -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
            <div>
                <p class="font-medium text-blue-900">Report Information</p>
                <ul class="text-sm text-blue-800 mt-2 space-y-1">
                    <li>• <strong>Per Employee:</strong> Select a specific employee and month/year to generate a detailed DTR</li>
                    <li>• <strong>All Employees:</strong> Generate reports for all employees in a selected month (optionally filtered by department)</li>
                    <li>• Reports include attendance records, hours worked, and undertime calculations</li>
                    <li>• Click "Print" to open the report in a print preview where you can print or save as PDF</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEmployeeSelect() {
        const reportType = document.querySelector('input[name="report_type"]:checked').value;
        const typeInput = document.getElementById('typeInput');
        
        if (reportType === 'per-employee') {
            // Show employee-specific fields
            document.getElementById('employeeSelectDiv').style.display = 'block';
            document.getElementById('departmentSelectDiv').style.display = 'none';
            
            typeInput.value = 'per-employee';
            
            // Make employee required
            document.getElementById('employeeSelect').required = true;
        } else {
            // Show all employees fields
            document.getElementById('employeeSelectDiv').style.display = 'none';
            document.getElementById('departmentSelectDiv').style.display = 'block';
            
            typeInput.value = 'monthly';
            
            // Make employee not required
            document.getElementById('employeeSelect').required = false;
        }
    }

    function viewReport() {
        const reportType = document.querySelector('input[name="report_type"]:checked').value;
        const form = document.getElementById('reportForm');
        
        // Validate required fields
        if (reportType === 'per-employee') {
            const employeeId = document.getElementById('employeeSelect').value;
            if (!employeeId) {
                alert('Please select an employee');
                return;
            }
        }

        // You can implement preview logic here if needed
        alert('Preview feature coming soon. For now, use "Export as PDF" to view the report.');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleEmployeeSelect();
    });
</script>
@endsection
