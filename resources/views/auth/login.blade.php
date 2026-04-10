<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pangasinan State University - Asingan Campus Employee Attendance Management System Using Real-Time Face Recognition</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        .bg-blur-1 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            blur: 80px;
            filter: blur(80px);
            animation: float 8s ease-in-out infinite;
        }

        .bg-blur-2 {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            blur: 80px;
            filter: blur(80px);
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-30px) translateX(20px); }
        }

        /* Glass effect card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .glass-card:hover {
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        /* Logo animation */
        .logo-container {
            animation: fadeInDown 0.8s ease-out forwards;
        }

        .logo-icon {
            display: inline-block;
            animation: pulse-scale 2s ease-in-out infinite;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Text animations */
        .heading-text {
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
            opacity: 0;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .subheading-text {
            animation: fadeInUp 0.8s ease-out 0.4s forwards;
            opacity: 0;
        }

        .signin-text {
            animation: fadeInUp 0.8s ease-out 0.6s forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form card animation */
        .form-card {
            animation: fadeInUp 0.8s ease-out 0.8s forwards;
            opacity: 0;
        }

        /* Input enhancement */
        .form-input {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f4f8 100%);
            border: 2px solid transparent;
        }

        .form-input:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        /* Button enhancement */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.4s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        /* Checkbox enhancement */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #ffffff;
            flex-shrink: 0;
            position: relative;
        }

        .custom-checkbox:hover {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .custom-checkbox:checked {
            background-color: #667eea;
            border-color: #667eea;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 11-1.06-1.06l7.25-7.25a.75.75 0 011.06 0z'/%3e%3cpath d='M3.22 10.22a.75.75 0 001 1.06l4-4a.75.75 0 00-1.06-1.06l-4 4z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 16px;
        }

        .custom-checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        /* Error message enhancement */
        .error-alert {
            animation: slideInDown 0.5s ease-out forwards;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-alert {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.1);
        }

        /* Footer text */
        .footer-text {
            animation: fadeInUp 0.8s ease-out 1s forwards;
            opacity: 0;
        }

        /* Input icons */
        .input-icon {
            transition: all 0.3s ease;
        }

        .form-input:focus ~ .input-icon {
            color: #667eea;
        }

        /* Label styling */
        .form-label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        /* Back button - pinned like kiosk */
        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .back-button:hover {
            color: #764ba2;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4 relative">

    {{-- Animated background elements --}}
    <div class="bg-blur-1 top-20 left-10"></div>
    <div class="bg-blur-2 bottom-20 right-10"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Logo & heading --}}
        <div class="text-center mb-4 logo-container">
            <div class="inline-flex items-center justify-center mb-4 bg-white/20 backdrop-blur-xl border border-white/30 p-4 rounded-2xl logo-icon">
                <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="w-16 h-16 object-contain drop-shadow-lg">
            </div>
            <h1 class="text-3xl font-bold heading-text">PSU - Asingan Campus</h1>
            <p class="text-white subheading-text mt-3 text-sm font-semibold leading-relaxed tracking-wide">Employee Attendance Management System Using Real-Time Face Recognition</p>
            <p class="text-white/80 signin-text mt-4 text-sm">Sign in to your account</p>
        </div>

        {{-- Card --}}
        <div class="glass-card rounded-3xl p-8 form-card">

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl p-4 text-sm error-alert">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle flex-shrink-0 mt-0.5"></i>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm form-label mb-2">Email address</label>
                    <div class="relative group">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="you@psu.edu"
                            class="form-input w-full pl-11 pr-4 py-3 text-sm rounded-xl focus:outline-none transition"
                        >
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 input-icon">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm form-label mb-2">Password</label>
                    <div class="relative group">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="form-input w-full pl-11 pr-4 py-3 text-sm rounded-xl focus:outline-none transition"
                        >
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 input-icon">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center pt-2">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        value="1" 
                        class="custom-checkbox"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember" class="ml-2.5 text-sm text-gray-700 font-medium cursor-pointer select-none">
                        Remember me
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="btn-login w-full py-3 text-white text-sm font-semibold rounded-xl shadow-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-400"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign in to your account
                </button>

                {{-- Back to Home --}}
                <a href="{{ url('/') }}" class="back-button text-purple-600 hover:text-purple-800" title="Go back to home page">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </form>
        </div>

        <p class="text-center text-xs text-white/60 mt-6 footer-text">
            <span class="text-white/50 text-xs mt-2 block">Developed by: Aguilar, Larry Sykioco • Buaya, Reden Ines • Ducsa, Neslyn Arcarte • Nazaire, Edleen Grace Arenas • Riturban, Marc Denver Fernandez</span>
        </p>
    </div>

</body>
</html>
