<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PSU - Asingan Campus Attendance Management System - Reset Password">
    <title>Forgot Password - PSU Attendance System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/psu logo.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Input Styling */
        .custom-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .custom-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Button Styling */
        .btn-submit {
            width: 100%;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Animation */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideInDown 0.6s ease;
        }

        /* Error Message */
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Success Message */
        .success-message {
            color: #10b981;
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: gap 8px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 min-h-screen flex items-center justify-center overflow-x-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-300 opacity-10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-300 opacity-10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="flex justify-center items-center min-h-screen">
            <div class="w-full max-w-md animate-slide-in">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <img src="{{ asset('images/PSU-LABEL-LOGO.png') }}" alt="PSU Logo" class="h-16">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-gray-600">Enter your email to receive a password reset link</p>
                </div>

                <!-- Reset Form -->
                <div class="glass-effect rounded-2xl p-8 shadow-xl">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="success-message mb-6">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center gap-3 text-red-700">
                                <i class="fas fa-exclamation-circle"></i>
                                <div>
                                    <p class="font-semibold text-sm">Error</p>
                                    <p class="text-sm mt-1">{{ $errors->first() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Info Message -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-blue-700 text-sm leading-relaxed">
                            <i class="fas fa-info-circle mr-2"></i>
                            No problem. Just let us know your email address and we'll send you a password reset link.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                class="custom-input @error('email') border-red-500 @enderror"
                                placeholder="Enter your email address"
                                required 
                                autofocus 
                            />
                            @error('email')
                                <div class="error-message">
                                    <i class="fas fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit mt-6">
                            <i class="fas fa-envelope mr-2"></i>Send Reset Link
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Remember your password?</span>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <a 
                        href="{{ route('login') }}" 
                        class="block w-full text-center py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </a>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-8">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Home</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
