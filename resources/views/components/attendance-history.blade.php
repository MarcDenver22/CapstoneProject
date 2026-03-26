<!-- Attendance History Component -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col hover:shadow-2xl transition-all duration-300">
    <!-- Modern Header -->
    <div class="bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-700 px-8 py-7 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-400 opacity-5 rounded-full -ml-16 -mb-16"></div>
        
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3.5 mb-2">
                    <div class="bg-white bg-opacity-20 backdrop-blur-md rounded-xl p-2.5 ring-1 ring-white ring-opacity-30">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white">{{ $title ?? 'Daily Time Record' }}</h3>
                </div>
                <p class="text-indigo-100 text-sm font-medium ml-14">{{ $subtitle ?? now()->format('F Y') }} — View Only</p>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="flex-1 p-0">
        <div class="overflow-x-auto max-h-96 border-t border-gray-100">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-50 via-blue-50 to-indigo-50 border-b-2 border-indigo-300">
                        <th rowspan="2" class="text-center py-4 px-3 font-bold text-gray-800 border-r border-indigo-200">Day</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-indigo-700 border-r border-indigo-200">A.M.</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-indigo-700 border-r border-indigo-200">P.M.</th>
                        <th colspan="2" class="text-center py-3 px-3 font-bold text-orange-700">Undertime</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-50 border-b-2 border-indigo-200">
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-indigo-200">Arrival</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-indigo-200">Departure</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-indigo-200">Arrival</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-indigo-200">Departure</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs border-r border-indigo-200">Hrs</th>
                        <th class="text-center py-3 px-2 font-semibold text-gray-700 text-xs">Min</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $displayed = 0;
                    @endphp
                    @for ($day = 1; $day <= 31 && $displayed < 15; $day++)
                        @php
                            $record = isset($daysData[$day]) ? (object)$daysData[$day] : null;
                            $amIn = $record?->am_arrival ?? '';
                            $amOut = $record?->am_depart ?? '';
                            $pmIn = $record?->pm_arrival ?? '';
                            $pmOut = $record?->pm_depart ?? '';
                            $utHours = $record?->undertime_hours ?? '';
                            $utMinutes = $record?->undertime_minutes ?? '';
                            
                            // Only display rows with data
                            $hasData = ($amIn || $amOut || $pmIn || $pmOut);
                        @endphp
                        @if($hasData)
                            <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-150 group even:bg-gray-50/50">
                                <td class="py-3 px-3 text-center text-gray-800 font-bold border-r border-gray-100 group-hover:text-indigo-700">{{ $day }}</td>
                                <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-100 group-hover:text-gray-900">{{ $amIn ?: '—' }}</td>
                                <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-100 group-hover:text-gray-900">{{ $amOut ?: '—' }}</td>
                                <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-100 group-hover:text-gray-900">{{ $pmIn ?: '—' }}</td>
                                <td class="py-3 px-2 text-center text-gray-700 font-medium border-r border-gray-100 group-hover:text-gray-900">{{ $pmOut ?: '—' }}</td>
                                <td class="py-3 px-2 text-center text-orange-600 font-bold border-r border-gray-100 group-hover:text-orange-700">{{ $utHours ?: '—' }}</td>
                                <td class="py-3 px-2 text-center text-orange-600 font-bold group-hover:text-orange-700">{{ $utMinutes ?: '—' }}</td>
                                </tr>
                                @php $displayed++; @endphp
                            @endif
                        @endfor
                    
                        @if(count($daysData ?? []) === 0)
                            <tr>
                                <td colspan="7" class="py-16 text-center text-gray-500">
                                    <div class="mb-4">
                                        <i class="fas fa-inbox text-5xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">No attendance records found</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        @if($totalHours > 0 || $totalMinutes > 0)
                        <tr class="border-t-2 border-indigo-300 bg-gradient-to-r from-orange-50 to-amber-50 font-bold">
                            <td colspan="5" class="py-4 px-4 text-right text-gray-800">Total Undertime:</td>
                            <td class="py-4 px-2 text-center text-orange-700 font-bold border-l border-indigo-200 text-lg">{{ $totalHours }}</td>
                            <td class="py-4 px-2 text-center text-orange-700 font-bold text-lg">{{ $totalMinutes }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer -->
        @if(count($daysData ?? []) > 0)
            <div class="border-t border-gray-100 px-8 py-4 bg-gradient-to-r from-gray-50/50 to-indigo-50/30 text-sm text-gray-600 font-medium">
                <i class="fas fa-info-circle text-xs mr-2"></i> <span class="font-bold text-gray-800">{{ count($daysData ?? []) }}</span> attendance records for the current month
            </div>
        @endif
</div>
