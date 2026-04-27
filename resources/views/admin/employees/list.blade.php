@extends('layouts.app')

@section('title', 'Employees')
@section('header', 'Employees')
@section('subheader', 'View and manage all registered employees')

@section('content')

<!-- Header with Search and Add Button -->
<div class="mb-6">
    <div class="flex gap-3 items-center">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            <input 
                type="text" 
                id="employeeSearchInput"
                placeholder="Search by name, email, position..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <a href="{{ route('admin.employees.create') }}" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold whitespace-nowrap">
            <i class="fas fa-user-plus mr-2"></i> Add Employee
        </a>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-300 bg-gray-50">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">NAME</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">EMAIL</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">DEPARTMENT</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">FACULTY ID</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">STATUS</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition employee-row" data-employee-name="{{ strtolower($employee->name ?? '') }}" data-employee-email="{{ strtolower($employee->email ?? '') }}" data-employee-position="{{ strtolower($employee->position ?? '') }}">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-gray-800">{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->email }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->department?->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->faculty_id ?? 'N/A' }}</td>
                        <td class="py-3 px-4">
                            @if($employee->face_enrolled)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i> Enrolled
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm space-x-3">
                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">View</a>
                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Edit</a>
                            <form method="POST" action="{{ route('admin.employees.reset_face', $employee->id) }}" style="display:inline;" onsubmit="return confirm('Reset face enrollment?');">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-700 font-semibold">Reset</button>
                            </form>
                            <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}" style="display:inline;" onsubmit="return confirm('Delete employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">No employees found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($employees->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $employees->links() }}
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('employeeSearchInput');
        
        if (!searchInput) return;
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr.employee-row');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const employeeName = row.getAttribute('data-employee-name') || '';
                const employeeEmail = row.getAttribute('data-employee-email') || '';
                const employeePosition = row.getAttribute('data-employee-position') || '';
                const searchText = employeeName + ' ' + employeeEmail + ' ' + employeePosition;
                
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
                noResultsRow.innerHTML = '<td colspan="7" class="py-8 text-center text-gray-500"><i class="fas fa-search text-2xl mb-2 block"></i><p class="font-medium">No employees match your search</p></td>';
                tbody.appendChild(noResultsRow);
            } else if (visibleCount > 0 && noResultsRow) {
                noResultsRow.remove();
            }
        });
    });
</script>

@endsection
