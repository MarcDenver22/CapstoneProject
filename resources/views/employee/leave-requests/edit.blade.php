@extends('employee.layouts.app')

@section('header', 'Edit Leave Request')
@section('subheader', 'Modify your request details')

@section('content')

<div class="max-w-lg">
    <!-- Form -->
    <form action="{{ route('employee.leave-requests.update', $leaveRequest->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Leave Type -->
        <div>
            <label for="leave_type" class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                <i class="fas fa-tag text-blue-600 mr-2 w-4"></i>
                Leave Type <span class="text-red-500 ml-1">*</span>
            </label>
            <select id="leave_type" name="leave_type" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition bg-white cursor-pointer @error('leave_type') border-red-500 @enderror">
                <option value="sick" @selected($leaveRequest->leave_type === 'sick')>🏥 Sick Leave</option>
                <option value="vacation" @selected($leaveRequest->leave_type === 'vacation')>🏖️ Vacation</option>
                <option value="personal" @selected($leaveRequest->leave_type === 'personal')>👤 Personal Leave</option>
                <option value="emergency" @selected($leaveRequest->leave_type === 'emergency')>🚨 Emergency Leave</option>
                <option value="other" @selected($leaveRequest->leave_type === 'other')>📋 Other</option>
            </select>
            @error('leave_type')
                <p class="mt-2 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Date Range -->
        <div class="space-y-3">
            <label class="flex items-center text-sm font-semibold text-gray-800">
                <i class="fas fa-calendar text-blue-600 mr-2 w-4"></i>
                Date Range <span class="text-red-500 ml-1">*</span>
            </label>
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="text-xs text-gray-600 font-medium mb-1 block">From</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-sm @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="text-xs text-gray-600 font-medium mb-1 block">To</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-sm @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Days Count -->
        <div class="text-sm">
            <p class="text-gray-600">Total Duration: <span id="days-count" class="font-bold text-blue-600">{{ $leaveRequest->days_count }}</span> days</p>
        </div>

        <!-- Reason -->
        <div>
            <label for="reason" class="flex items-center text-sm font-semibold text-gray-800 mb-3">
                <i class="fas fa-file-alt text-blue-600 mr-2 w-4"></i>
                Reason <span class="text-red-500 ml-1">*</span>
            </label>
            <textarea id="reason" name="reason" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none text-sm @error('reason') border-red-500 @enderror">{{ old('reason', $leaveRequest->reason) }}</textarea>
            <div class="flex justify-between mt-2">
                <p class="text-xs text-gray-500">Minimum 10 characters required</p>
                <p class="text-xs text-gray-500"><span id="char-count">{{ strlen($leaveRequest->reason) }}</span>/1000</p>
            </div>
            @error('reason')
                <p class="mt-2 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="flex-1 px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('employee.leave-requests.show', $leaveRequest->id) }}" class="flex-1 px-4 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition text-center">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const reasonInput = document.getElementById('reason');
    const daysCount = document.getElementById('days-count');
    const charCount = document.getElementById('char-count');

    function calculateDays() {
        if (startDateInput.value && endDateInput.value) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            daysCount.textContent = diffDays;
        }
    }

    reasonInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);
</script>

@endsection
