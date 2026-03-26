@extends('layouts.app')

@section('header', 'Export DTR (Daily Time Record)')
@section('subheader', 'Civil Service Form No. 48')

@section('content')
<div class="space-y-6">
    <!-- Export Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Employee Selection Form -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Select Employee DTR</h3>
                
                <form id="dtrForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
                        <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Month</label>
                            <select name="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}" {{ $month == now()->month ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::createFromFormat('m', $month)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
                            <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                @for ($year = now()->year; $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Export Options -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Export Options</h3>
                
                <div class="space-y-3">
                    <button type="button" onclick="exportDtr('pdf')" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </button>
                    <a href="{{ route('hr.dtr-template-upload') }}" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-file-upload"></i> Template Management
                    </a>
                    <a href="{{ route('hr.reports.index') }}" class="w-full bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>

                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Civil Service Format</h4>
                    <p class="text-sm text-blue-800">The exported DTR follows the Civil Service Form No. 48 standard format, including:</p>
                    <ul class="text-sm text-blue-800 mt-2 ml-4 list-disc">
                        <li>Employee information</li>
                        <li>Daily time records</li>
                        <li>Hours worked calculation</li>
                        <li>Attendance status</li>
                        <li>Signature fields</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Export Format Preview</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr style="background: #d3d3d3; border: 1px solid #000;">
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Day</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Date</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Time In</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Time Out</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Hours Worked</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border: 1px solid #000;">
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">1</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Mon</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">08:00</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">17:00</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">9.00</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">PRESENT</td>
                    </tr>
                    <tr style="border: 1px solid #000; background: #fafafa;">
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">2</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Tue</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">08:15</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">17:00</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">8.75</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">LATE</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportDtr(format) {
    const form = document.getElementById('dtrForm');
    const formData = new FormData(form);

    // Validate form
    if (!formData.get('user_id') || !formData.get('month') || !formData.get('year')) {
        alert('Please select employee, month, and year');
        return;
    }

    const params = new URLSearchParams(formData);
    const route = format === 'excel' 
        ? "{{ route('hr.dtr.export-excel') }}"
        : "{{ route('hr.dtr.export-pdf') }}";

    window.location.href = route + '?' + params.toString();
}
</script>
@endsection
