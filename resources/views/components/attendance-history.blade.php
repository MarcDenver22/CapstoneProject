<!-- Attendance History Component -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ $title ?? 'Attendance History' }}</h3>
            <p class="text-sm text-gray-600">{{ $subtitle ?? now()->format('F Y') }} — View Only</p>
        </div>
        <button class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition flex items-center gap-2">
            <i class="fas fa-download"></i> Export
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-300 bg-gray-50">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">DATE</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">DAY</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME IN</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">TIME OUT</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">HOURS WORKED</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">VERIFIED</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-3 px-4 text-gray-800 font-medium">
                            {{ $record->attendance_date ? $record->attendance_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            {{ $record->attendance_date ? $record->attendance_date->format('l') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            @if($record->time_in)
                                {{ $record->time_in->format('H:i') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            @if($record->time_out)
                                {{ $record->time_out->format('H:i') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            @if($record->time_in && $record->time_out)
                                @php
                                    $hours = $record->time_in->diffInHours($record->time_out);
                                    $minutes = $record->time_in->diffInMinutes($record->time_out) % 60;
                                @endphp
                                {{ $hours }}h {{ $minutes }}m
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($record->status === 'present')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle"></i> Present
                                </span>
                            @elseif($record->status === 'late')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                    <i class="fas fa-exclamation-circle"></i> Late
                                </span>
                            @elseif($record->status === 'absent')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle"></i> Absent
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">{{ ucfirst($record->status ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($record->liveness_verified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    <i class="fas fa-shield-alt"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <i class="fas fa-circle"></i> Unverified
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p class="mt-2">No attendance records found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-600">
            <p>Showing <span class="font-semibold">{{ $records->count() }}</span> records</p>
        </div>
    @endif
</div>
