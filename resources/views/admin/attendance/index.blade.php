@extends('layouts.app')

@section('title', 'Attendance Log')
@section('header', 'Attendance Log')
@section('subheader', 'View and manage employee attendance records')

@section('content')

<!-- Attendance Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
  

    <div class="p-6">
        <div class="relative mb-4">
            <input 
                type="text" 
                id="searchInput"
                placeholder="Search employee..." 
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
            />
            <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
        </div>

        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-300 bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">EMPLOYEE</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">DATE</th>
                            <th colspan="2" class="text-center py-3 px-4 font-semibold text-gray-700 border-r border-gray-300">A.M.</th>
                            <th colspan="2" class="text-center py-3 px-4 font-semibold text-gray-700 border-r border-gray-300">P.M.</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">LIVENESS</th>
                        </tr>
                        <tr class="border-b border-gray-300 bg-gray-50">
                            <th colspan="2"></th>
                            <th class="text-center py-2 px-4 font-semibold text-gray-700 border-r border-gray-300 text-xs">Arrival</th>
                            <th class="text-center py-2 px-4 font-semibold text-gray-700 border-r border-gray-300 text-xs">Departure</th>
                            <th class="text-center py-2 px-4 font-semibold text-gray-700 border-r border-gray-300 text-xs">Arrival</th>
                            <th class="text-center py-2 px-4 font-semibold text-gray-700 border-r border-gray-300 text-xs">Departure</th>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $record)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition attendance-row" data-employee-name="{{ strtolower($record->user->name ?? '') }}" data-employee-email="{{ strtolower($record->user->email ?? '') }}">
                                <td class="py-3 px-4">
                                    <p class="font-medium text-gray-900">{{ $record->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $record->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-gray-600 border-r border-gray-300">
                                    {{ $record->attendance_date->format('M d, Y') }}
                                </td>
                                <!-- A.M. Arrival -->
                                <td class="py-3 px-4 text-center border-r border-gray-300">
                                    @php
                                        $timeIn = $record->time_in;
                                        $amArrival = null;
                                        if ($timeIn) {
                                            $time = \Carbon\Carbon::createFromFormat('H:i:s', $timeIn) ?? \Carbon\Carbon::createFromFormat('H:i', $timeIn);
                                            $amArrival = $time && $time->hour < 12 ? $time->format('H:i') : null;
                                        }
                                    @endphp
                                    @if($amArrival)
                                        <span class="text-gray-600 font-medium">{{ $amArrival }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <!-- A.M. Departure -->
                                <td class="py-3 px-4 text-center border-r border-gray-300">
                                    <span class="text-gray-400">—</span>
                                </td>
                                <!-- P.M. Arrival -->
                                <td class="py-3 px-4 text-center border-r border-gray-300">
                                    <span class="text-gray-400">—</span>
                                </td>
                                <!-- P.M. Departure -->
                                <td class="py-3 px-4 text-center border-r border-gray-300">
                                    @php
                                        $timeOut = $record->time_out;
                                        if ($timeOut) {
                                            echo '<span class="text-gray-600 font-medium">' . substr($timeOut, 0, 5) . '</span>';
                                        } else {
                                            echo '<span class="text-gray-400">—</span>';
                                        }
                                    @endphp
                                </td>
                                <td class="py-3 px-4 border-r border-gray-300">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $record->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($record->liveness_verified)
                                        <span class="text-green-600 font-semibold text-sm">✓</span>
                                    @else
                                        <span class="text-red-600 font-semibold text-sm">✕</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-4xl mb-3 block"></i>
                <p class="text-gray-600 text-lg font-medium">No attendance records found</p>
                <p class="text-gray-500 text-sm">Attendance records will appear here once they are created in the system.</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($attendances->hasPages())
    <div class="mt-6">
        {{ $attendances->links() }}
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        
        if (!searchInput) return;
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr.attendance-row');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const employeeName = row.getAttribute('data-employee-name') || '';
                const employeeEmail = row.getAttribute('data-employee-email') || '';
                const searchText = employeeName + ' ' + employeeEmail;
                
                // Show/hide row based on search match
                if (searchText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show "no results" message if no rows match
            const tbody = document.querySelector('tbody');
            let noResultsRow = document.querySelector('tbody .no-results-row');
            
            if (visibleCount === 0 && tableRows.length > 0 && tbody && !noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = '<td colspan="8" class="py-8 text-center text-gray-500"><i class="fas fa-search text-2xl mb-2 block"></i><p class="font-medium">No employees match your search</p></td>';
                tbody.appendChild(noResultsRow);
            } else if (visibleCount > 0 && noResultsRow) {
                noResultsRow.remove();
            }
        });
    });
</script>

@endsection
