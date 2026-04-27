@extends('layouts.app')

@section('title', 'Profile')
@section('header', 'Profile')
@section('subheader', 'Manage your personal information')

@section('content')

<div class="max-w-2xl mx-auto">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Your Profile</h2>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                <p class="text-red-700 font-semibold mb-2">Please fix the errors below:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success') || session('status'))
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-600 text-xl mt-0.5"></i>
                    <div>
                        <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                        <p class="text-green-600 text-sm mt-1">You can use your new password the next time you log in.</p>
                    </div>
                </div>
            @else
                <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                    <p class="text-blue-700 font-semibold">✓ Profile updated successfully</p>
                </div>
            @endif
        @endif

        <form method="POST" action="{{ auth()->user()->role === 'hr' ? route('hr.profile.update') : (auth()->user()->role === 'admin' ? route('admin.profile.update') : route('profile.update')) }}" class="space-y-6" id="profileForm">
            @csrf
            @method('PATCH')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    placeholder="John Doe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}" 
                    placeholder="john@example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Employee ID (read-only) -->
            <div>
                <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee ID</label>
                <input 
                    type="text" 
                    id="employee_id" 
                    name="employee_id" 
                    value="{{ $user->faculty_id ?? 'N/A' }}" 
                    placeholder="EMP001"
                    disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                >
                <p class="text-gray-500 text-xs mt-1">Your Employee ID is auto-generated and cannot be changed</p>
            </div>

            <!-- Department -->
            @if(isset($departments))
            <div>
                <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                <select 
                    id="department_id" 
                    name="department_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">Select a department</option>
                    @forelse($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @empty
                        <option value="" disabled>No departments available</option>
                    @endforelse
                </select>
            </div>
            @elseif($user->department)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                <input 
                    type="text" 
                    value="{{ $user->department->name ?? 'N/A' }}" 
                    disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                >
            </div>
            @endif

            <!-- Password Change Section -->
            <div class="border-t border-gray-300 pt-6 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Change Password</h3>

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password <span class="text-gray-500 font-normal">(required to change)</span></label>
                    <p class="text-xs text-gray-600 mb-2">🔒 This is the password you currently use to log in. It's required for security verification.</p>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            placeholder="Enter your current login password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('current_password') border-red-500 @enderror pr-12"
                            autocomplete="off"
                        >
                        <button 
                            type="button"
                            onclick="togglePasswordVisibility('current_password')"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700 transition"
                            title="Toggle password visibility"
                        >
                            <i class="fas fa-eye" id="toggle-current_password"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password <span class="text-gray-500 font-normal">(min. 8 characters)</span></label>
                    <p class="text-xs text-gray-600 mb-2">🆕 Create a strong, new password. Leave blank if you don't want to change it.</p>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter new password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror"
                        autocomplete="off"
                    >
                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-shield-alt text-blue-500"></i>
                        Password strength: <span id="passwordStrength">—</span>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                    <p class="text-xs text-gray-600 mb-2">✓ Re-enter your new password to confirm it matches.</p>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Confirm new password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        autocomplete="off"
                    >
                    <div id="passwordMatch" class="mt-2 text-xs hidden">
                        <i class="fas fa-check text-green-500 mr-1"></i><span class="text-green-600">Passwords match ✓</span>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Email Address (Read-only)</p>
                    <p class="text-sm text-gray-700">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Account Role</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $user->role }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Member Since</p>
                    <p class="text-sm text-gray-700">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Submit / Cancel -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ auth()->user()->role === 'hr' ? route('hr.dashboard') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard')) }}" class="flex-1 px-6 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = document.getElementById('toggle-' + fieldId);
    
    if (field.type === 'password') {
        field.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[!@#$%^&*]/.test(password)) strength++;
    
    return strength;
}

// Update password strength indicator
function updatePasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthElement = document.getElementById('passwordStrength');
    const strength = calculatePasswordStrength(password);
    
    if (password.length === 0) {
        strengthElement.textContent = '—';
        strengthElement.className = 'text-gray-500';
    } else if (strength <= 1) {
        strengthElement.innerHTML = '<span class="text-red-600">Weak</span>';
        strengthElement.className = 'text-red-600';
    } else if (strength <= 2) {
        strengthElement.innerHTML = '<span class="text-yellow-600">Fair</span>';
        strengthElement.className = 'text-yellow-600';
    } else if (strength <= 3) {
        strengthElement.innerHTML = '<span class="text-blue-600">Good</span>';
        strengthElement.className = 'text-blue-600';
    } else {
        strengthElement.innerHTML = '<span class="text-green-600">Strong</span>';
        strengthElement.className = 'text-green-600';
    }
}

// Clear password fields when page loads if there are no errors
document.addEventListener('DOMContentLoaded', function() {
    const errorMessages = document.querySelectorAll('.text-red-600');
    const hasErrors = errorMessages.length > 0;
    
    // Clear password fields if no errors (successful submission)
    if (!hasErrors) {
        document.getElementById('current_password').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
    }
    
    // Add real-time validation for passwords
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const passwordMatchDiv = document.getElementById('passwordMatch');
    const profileForm = document.getElementById('profileForm');
    
    // Password strength on input
    passwordField.addEventListener('input', updatePasswordStrength);
    
    // Password confirmation validation
    confirmField.addEventListener('input', function() {
        if (passwordField.value && confirmField.value) {
            if (passwordField.value === confirmField.value) {
                confirmField.style.borderColor = '#10b981';
                passwordMatchDiv.classList.remove('hidden');
            } else {
                confirmField.style.borderColor = '#ef4444';
                passwordMatchDiv.classList.add('hidden');
            }
        } else {
            confirmField.style.borderColor = '#d1d5db';
            passwordMatchDiv.classList.add('hidden');
        }
    });
    
    // Form submission handler to debug
    profileForm.addEventListener('submit', function(e) {
        console.log('Form submitted');
        console.log('Current Password:', document.getElementById('current_password').value ? 'filled' : 'empty');
        console.log('New Password:', document.getElementById('password').value ? 'filled' : 'empty');
        console.log('Confirm Password:', document.getElementById('password_confirmation').value ? 'filled' : 'empty');
        
        // Validate password match before submission
        const pwd = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        
        if (pwd !== '' && pwd !== confirm) {
            e.preventDefault();
            alert('❌ Passwords do not match! Please verify and try again.');
            return false;
        }
    });
});
</script>

@endsection
