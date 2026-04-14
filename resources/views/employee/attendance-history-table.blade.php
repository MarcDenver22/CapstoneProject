@extends('employee.layouts.app')

@section('header', 'Attendance History')
@section('subheader', 'Daily Time Record - History View')

@section('content')

<div class="space-y-6 screen-only">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-1">DAILY TIME RECORD</h1>
            <p class="text-sm text-gray-600">Civil Service Form No. 48</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Employee Name</p>
                <p class="text-lg font-semibold text-gray-800 border-b-2 border-gray-300 pb-2">{{ $user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Month & Year</p>
                <p class="text-lg font-semibold text-gray-800 border-b-2 border-gray-300 pb-2">{{ now()->format('F Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Department</p>
                <p class="text-gray-800 border-b border-gray-300 pb-1">{{ $user->department?->department_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Position</p>
                <p class="text-gray-800 border-b border-gray-300 pb-1">{{ $user->position ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Office Hours</p>
                <p class="text-gray-800 border-b border-gray-300 pb-1">8:00 AM - 5:00 PM</p>
            </div>
        </div>
    </div>

    <!-- DTR Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto border border-gray-300 rounded-lg">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-50 border-b-2 border-gray-400">
                        <th rowspan="2" class="text-center py-4 px-3 font-bold text-gray-800 border-r border-gray-300">Day</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-gray-800 border-r border-gray-300">A.M.</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-gray-800 border-r border-gray-300">P.M.</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-gray-800">Undertime</th>
                    </tr>
                    <tr class="bg-gray-50 border-b-2 border-gray-400">
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-gray-300">Arrival</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-gray-300">Departure</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-gray-300">Arrival</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-gray-300">Departure</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-gray-300">Hrs</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs">Min</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $recordsByDate = $attendanceRecords->groupBy(fn($r) => $r->attendance_date?->day ?? null);
                    @endphp
                    @for ($day = 1; $day <= 31; $day++)
                        @php
                            $record = isset($daysData[$day]) ? (object)$daysData[$day] : null;
                            $amIn = $record?->am_arrival ?? '';
                            $amOut = $record?->am_depart ?? '';
                            $pmIn = $record?->pm_arrival ?? '';
                            $pmOut = $record?->pm_depart ?? '';
                            $utHours = $record?->undertime_hours ?? '';
                            $utMinutes = $record?->undertime_minutes ?? '';
                        @endphp
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition even:bg-gray-50">
                            <td class="py-3 px-3 text-center text-gray-800 font-semibold border-r border-gray-200">{{ $day }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-200">{{ $amIn ?: '—' }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-200">{{ $amOut ?: '—' }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-200">{{ $pmIn ?: '—' }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-200">{{ $pmOut ?: '—' }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-200">{{ $utHours ?: '—' }}</td>
                            <td class="py-3 px-2 text-center text-gray-700 font-medium">{{ $utMinutes ?: '—' }}</td>
                        </tr>
                    @endfor
                    <!-- Total Row -->
                    <tr class="border-t-2 border-gray-400 bg-gradient-to-r from-gray-100 to-gray-50 font-bold">
                        <td colspan="5" class="py-4 px-4 text-right text-gray-800">Total Undertime:</td>
                        <td class="py-4 px-2 text-center text-gray-800 border-l border-gray-300">{{ $totalHours ?? 0 }}</td>
                        <td class="py-4 px-2 text-center text-gray-800">{{ $totalMinutes ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export and Print Buttons -->
    <div class="flex gap-3 justify-end no-print">
        <button onclick="printDTR()" class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition inline-flex items-center gap-2 shadow-sm">
            <i class="fas fa-print"></i> Print DTR
        </button>
        <a href="{{ route('employee.attendance-export-pdf') }}" class="px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition inline-flex items-center gap-2 shadow-sm">
            <i class="fas fa-file-pdf"></i> Export DTR
        </a>
    </div>

    <script>
        function printDTR() {
            // Create a hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = '{{ route('employee.attendance-print-pdf') }}';
            document.body.appendChild(iframe);
            
            iframe.onload = function() {
                // Trigger print on the iframe content
                iframe.contentWindow.print();
                
                // Remove the iframe after a short delay to ensure print dialog opens
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 1000);
            };
        }
    </script>
</div>

@endsection
