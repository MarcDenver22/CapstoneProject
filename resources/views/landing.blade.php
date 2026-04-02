<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PSU - Asingan Campus Employee Attendance Management System Using Real-Time Face Recognition - Streamline attendance tracking, leave management, and employee engagement">
    <title>PSU - Asingan Campus Employee Attendance Management System Using Real-Time Face Recognition</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/psu logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/psu logo.png') }}">

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

        /* Gradient Backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-bg-alt {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-effect-dark {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Feature Cards */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.98) !important;
            border: 1px solid rgba(200, 200, 200, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15);
        }

        /* Ensure feature cards are visible */
        .feature-card > div {
            min-height: auto;
        }

        /* Icon Boxes */
        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover .icon-box {
            transform: scale(1.1) rotateY(10deg);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-weight: 600;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid white;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-25px); }
        }

        .float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 30px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 50px rgba(102, 126, 234, 0.6); }
        }

        .pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
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

        .animate-slide-in-left {
            animation: slideInLeft 0.6s ease-out forwards;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Counter animation */
        @keyframes count-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-number {
            animation: count-up 0.6s ease-out forwards;
        }

        .counter {
            display: inline-block;
        }

        /* Role Card Enhancements */
        .role-card {
            transition: all 0.4s ease;
            position: relative;
        }

        .role-card:hover {
            transform: translateY(-10px);
        }

        .role-icon-circle {
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon-circle {
            transform: scale(1.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Star Rating */
        .star {
            color: #fbbf24;
            margin-right: 2px;
        }

        /* Navigation */
        nav {
            transition: all 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.98) !important;
        }

        /* Badges */
        .badge-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            color: white;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            opacity: 0;
            animation: scrollFadeInUp 0.6s ease-out 0.1s forwards;
        }

        /* Section Title */
        .section-title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            opacity: 0;
            animation: scrollFadeInUp 0.6s ease-out 0.2s forwards;
        }

        /* Section description text */
        section > div > p, section > div > .text-center > p {
            opacity: 0;
            animation: scrollFadeInUp 0.6s ease-out 0.3s forwards;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
        }

        /* Scroll Animation Styles */
        @keyframes scrollFadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scrollFadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scrollFadeInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scrollScaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Scroll Animation Trigger */
        .scroll-animate {
            opacity: 0;
            transform: translateY(30px);
            animation: scrollFadeInUp 0.6s ease-out forwards;
            animation-play-state: paused;
        }

        .scroll-animate.visible {
            animation-play-state: running;
        }

        /* Feature Cards - staggered animations */
        .feature-card {
            animation-delay: 0s;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
        .feature-card:nth-child(5) { animation-delay: 0.5s; }
        .feature-card:nth-child(6) { animation-delay: 0.6s; }

        /* Role Cards - staggered animations */
        .role-card {
            animation-delay: 0s;
        }

        .role-card:nth-child(1) { animation-delay: 0.1s; }
        .role-card:nth-child(2) { animation-delay: 0.2s; }
        .role-card:nth-child(3) { animation-delay: 0.3s; }
        .role-card:nth-child(4) { animation-delay: 0.4s; }

        /* Glass effect cards with smooth transitions */
        .glass-effect, .glass-effect-dark {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* FAQ styling */
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .faq-question:hover {
            color: #667eea;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            color: #6b7280;
            line-height: 1.6;
        }

        .faq-answer.open {
            max-height: 500px;
        }

        .faq-icon {
            transition: transform 0.3s ease;
            font-size: 1.25rem;
        }

        .faq-icon.open {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white overflow-x-hidden">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 glass-effect transition-all duration-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3 group">
                    <img src="{{ asset('images/PSU-LABEL-LOGO.png') }}" alt="PSU Logo" class="h-8 transition-transform group-hover:scale-110">
                </div>
                <div class="hidden md:flex gap-8">
                    <a href="#features" class="text-gray-700 font-medium hover:gradient-text transition duration-300 relative group">
                        Features
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#roles" class="text-gray-700 font-medium hover:gradient-text transition duration-300 relative group">
                        Roles
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#contact" class="text-gray-700 font-medium hover:gradient-text transition duration-300 relative group">
                        Contact
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="px-6 py-2 text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg font-medium hover:shadow-lg transition-all duration-300">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Enhanced Design -->
    <section class="text-white pt-28 pb-16 px-4 relative overflow-hidden" style="background-image: url('{{ asset('images/psu-background.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/70 to-purple-600/70"></div>
        
        <!-- Animated Background Elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-white opacity-5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-300 opacity-5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>

        <div class="container mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="animate-slide-in-left">
                    <div class="badge-gradient mb-4">✨ Smart Attendance Management</div>
                    <h1 class="section-title mb-6 leading-tight">
                        PSU - Asingan Campus
                        <span class="block bg-gradient-to-r from-blue-200 to-purple-200 bg-clip-text text-transparent">
                            Employee Attendance Management System Using Real-Time Face Recognition
                        </span>
                    </h1>
                    <p class="text-xl text-gray-100 mb-8 leading-relaxed opacity-90">
                        Streamline attendance tracking, leave management, and employee engagement with our intelligent faculty portal system. Track, manage, and analyze with ease.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}" class="btn-primary text-white px-8 py-4 rounded-lg text-center inline-flex items-center justify-center gap-2">
                            <span>Get Started</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <button class="btn-secondary text-white px-8 py-4 rounded-lg text-center inline-flex items-center justify-center gap-2">
                            <span>Learn More</span>
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                    <div class="flex gap-6 mt-8 pt-6 border-t border-white border-opacity-20">
                        <div>
                            <div class="text-3xl font-bold text-blue-200">1250+</div>
                            <p class="text-gray-200">Active Users</p>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-purple-200">99.9%</div>
                            <p class="text-gray-200">Uptime</p>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-blue-200">24/7</div>
                            <p class="text-gray-200">Support</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center animate-slide-in-right">
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 px-4 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="badge-gradient mx-auto mb-3">🎯 POWERFUL FEATURES</div>
                <h2 class="section-title gradient-text mb-3">Everything You Need</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Comprehensive tools and features designed to simplify faculty management and improve organizational efficiency</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl">
                    <div class="icon-box bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-6">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-clock text-blue-600"></i> Real-time Attendance</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Track attendance in real-time with accurate timestamps, biometric verification, and instant status monitoring for complete visibility.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Instant verification</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Accurate timestamps</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Real-time monitoring</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-blue-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl" style="animation-delay: 0.1s;">
                    <div class="icon-box bg-gradient-to-br from-green-500 to-cyan-600 text-white mb-6">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-calendar-check text-green-600"></i> Leave Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Streamlined leave request submission, approval process, and balance tracking with automatic notifications and compliance tracking.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Easy submission</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Approval workflow</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Auto-notifications</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-green-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl" style="animation-delay: 0.2s;">
                    <div class="icon-box bg-gradient-to-br from-amber-500 to-orange-600 text-white mb-6">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-chart-pie text-amber-600"></i> Analytics & Reports</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Comprehensive dashboards, visual analytics, and customizable reports for data-driven decision making and strategic insights.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-amber-500"></i>
                            <span>Interactive dashboards</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-amber-500"></i>
                            <span>Custom reports</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-amber-500"></i>
                            <span>Data visualization</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-amber-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl" style="animation-delay: 0.3s;">
                    <div class="icon-box bg-gradient-to-br from-pink-500 to-red-600 text-white mb-6">
                        <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="h-8 w-8 object-contain">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-face-id text-pink-600"></i> Biometric Enrollment</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Secure face enrollment and recognition using advanced AI technology for enhanced attendance verification and fraud prevention.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-pink-500"></i>
                            <span>AI-powered recognition</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-pink-500"></i>
                            <span>High accuracy</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-pink-500"></i>
                            <span>Fraud prevention</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-pink-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl" style="animation-delay: 0.4s;">
                    <div class="icon-box bg-gradient-to-br from-indigo-500 to-blue-600 text-white mb-6">
                        <i class="fas fa-bell text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-bullhorn text-indigo-600"></i> Smart Announcements</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Keep faculty informed with targeted announcements, important updates, and real-time notifications with read receipts.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-indigo-500"></i>
                            <span>Targeted messaging</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-indigo-500"></i>
                            <span>Real-time delivery</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-indigo-500"></i>
                            <span>Read receipts</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-indigo-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card glass-effect p-6 rounded-2xl hover:shadow-2xl" style="animation-delay: 0.5s;">
                    <div class="icon-box bg-gradient-to-br from-purple-500 to-pink-600 text-white mb-6">
                        <i class="fas fa-lock text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2"><i class="fas fa-shield-check text-purple-600"></i> Enterprise Security</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Enterprise-grade security, encryption, comprehensive audit logging, compliance tracking, and transparent accountability measures.</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-purple-500"></i>
                            <span>End-to-end encryption</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-purple-500"></i>
                            <span>Audit logging</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-check-circle text-purple-500"></i>
                            <span>Compliance ready</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="#" class="text-purple-600 font-semibold hover:text-purple-600 transition inline-flex items-center gap-2">
                            Learn more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- User Roles Section -->
    <section id="roles" class="py-12 px-4 relative overflow-hidden">
        <!-- Background gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500 opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 opacity-5 rounded-full blur-3xl"></div>

        <div class="container mx-auto relative z-10">
            <div class="text-center mb-8">
                <div class="badge-gradient mx-auto mb-2">👥 USER ROLES</div>
                <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">Tailored for Everyone</h2>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">Role-based access designed specifically for your organization's needs</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Employee Role -->
                <div class="role-card glass-effect-dark p-5 rounded-2xl text-center text-white hover:shadow-2xl">
                    <div class="role-icon-circle w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Faculty Members</h3>
                    <p class="text-gray-300 mb-3 leading-relaxed text-xs">View attendance records, submit leave requests, manage your schedule, and stay updated with important announcements.</p>
                    <ul class="text-left text-xs text-gray-300 space-y-1 mb-0">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Personal attendance dashboard</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Easy leave request submission</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Push notifications</span>
                        </li>
                    </ul>
                </div>

                <!-- HR Role -->
                <div class="role-card glass-effect-dark p-5 rounded-2xl text-center text-white hover:shadow-2xl" style="animation-delay: 0.1s;">
                    <div class="role-icon-circle w-16 h-16 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">HR Personnel</h3>
                    <p class="text-gray-300 mb-3 leading-relaxed text-xs">Manage announcements, process leave approvals, generate reports, and oversee faculty engagement metrics.</p>
                    <ul class="text-left text-xs text-gray-300 space-y-1 mb-0">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Leave approval management</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Advanced reporting</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Employee announcements</span>
                        </li>
                    </ul>
                </div>

                <!-- Admin Role -->
                <div class="role-card glass-effect-dark p-5 rounded-2xl text-center text-white hover:shadow-2xl" style="animation-delay: 0.2s;">
                    <div class="role-icon-circle w-16 h-16 mx-auto bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Administrators</h3>
                    <p class="text-gray-300 mb-3 leading-relaxed text-xs">Full system control, user management, attendance oversight, comprehensive auditing, and administrative settings.</p>
                    <ul class="text-left text-xs text-gray-300 space-y-1 mb-0">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>User management</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>System configuration</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Audit logs</span>
                        </li>
                    </ul>
                </div>

                <!-- Super Admin Role -->
                <div class="role-card glass-effect-dark p-5 rounded-2xl text-center text-white hover:shadow-2xl" style="animation-delay: 0.3s;">
                    <div class="role-icon-circle w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-crown text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Super Admins</h3>
                    <p class="text-gray-300 mb-3 leading-relaxed text-xs">System-wide configuration, role management, administrative oversight, and strategic decision-making access.</p>
                    <ul class="text-left text-xs text-gray-300 space-y-1 mb-0">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Role configuration</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>System analytics</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-400 mt-1 text-xs"></i>
                            <span>Integration settings</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 px-4 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-30 -translate-x-1/2"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-100 rounded-full blur-3xl opacity-30 translate-x-1/2"></div>

        <div class="container mx-auto relative z-10">
            <div class="text-center mb-12">
                <div class="badge-gradient mx-auto mb-3">📊 BY THE NUMBERS</div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Trusted Across the Board</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Join thousands of institutions using our system daily</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat 1: Faculty Members -->
                <div class="glass-effect p-6 rounded-2xl text-center hover:shadow-xl transition-all duration-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <div class="stat-number text-5xl lg:text-6xl font-bold gradient-text mb-2">
                        <span class="counter" data-target="1250">0</span>+
                    </div>
                    <p class="text-gray-600 font-semibold">Active Users</p>
                    <p class="text-sm text-gray-500 mt-2">Across all institutions</p>
                </div>

                <!-- Stat 2: Daily Check-ins -->
                <div class="glass-effect p-8 rounded-2xl text-center hover:shadow-xl transition-all duration-300" style="animation-delay: 0.1s;">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl mb-4">
                        <i class="fas fa-check-circle text-2xl text-white"></i>
                    </div>
                    <div class="stat-number text-5xl lg:text-6xl font-bold text-green-600 mb-2">
                        <span class="counter" data-target="2847">0</span>+
                    </div>
                    <p class="text-gray-600 font-semibold">Daily Check-ins</p>
                    <p class="text-sm text-gray-500 mt-2">Every working day</p>
                </div>

                <!-- Stat 3: Leave Requests Processed -->
                <div class="glass-effect p-8 rounded-2xl text-center hover:shadow-xl transition-all duration-300" style="animation-delay: 0.2s;">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl mb-4">
                        <i class="fas fa-file-alt text-2xl text-white"></i>
                    </div>
                    <div class="stat-number text-5xl lg:text-6xl font-bold text-purple-600 mb-2">
                        <span class="counter" data-target="3456">0</span>+
                    </div>
                    <p class="text-gray-600 font-semibold">Requests Processed</p>
                    <p class="text-sm text-gray-500 mt-2">Successfully managed</p>
                </div>

                <!-- Stat 4: System Uptime -->
                <div class="glass-effect p-8 rounded-2xl text-center hover:shadow-xl transition-all duration-300" style="animation-delay: 0.3s;">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl mb-4">
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <div class="stat-number text-5xl lg:text-6xl font-bold text-orange-600 mb-2">
                        <span class="counter" data-target="99.9">0</span>%
                    </div>
                    <p class="text-gray-600 font-semibold">System Uptime</p>
                    <p class="text-sm text-gray-500 mt-2">24/7 reliability</p>
                </div>
            </div>
        </div>
    </section>

    <!-- System Quote Section -->
    <section class="py-20 px-4 bg-gradient-to-r from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Main Quote -->
                <div class="mb-12">
                    <i class="fas fa-quote-left text-6xl text-purple-400 mb-8 opacity-60"></i>
                    <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                        Transforming Faculty Attendance Management
                    </h2>
                    <p class="text-xl lg:text-2xl text-gray-200 mb-8 leading-relaxed">
                        One system. Complete visibility. Infinite possibilities. Our Faculty Employee Attendance System empowers institutions to streamline operations, enhance accountability, and foster a culture of transparency and trust.
                    </p>
                    <i class="fas fa-quote-right text-6xl text-purple-400 opacity-60"></i>
                </div>

                <!-- Three Key Values -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16 pt-12 border-t border-gray-700">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Empower Educators</h3>
                        <p class="text-gray-300">Give faculty members and administrators the tools they need to focus on what matters most—education and institutional excellence.</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-sync-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Streamline Operations</h3>
                        <p class="text-gray-300">Automate attendance tracking, leave management, and reporting to save countless hours and reduce administrative burden.</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Build Trust</h3>
                        <p class="text-gray-300">Create transparency through comprehensive auditing, real-time visibility, and secure data management for institutional integrity.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto">
            <div class="gradient-bg rounded-3xl p-12 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">Ready to Transform Your Faculty Management?</h2>
                    <p class="text-lg text-gray-100 mb-8 max-w-2xl mx-auto">
                        Join thousands of institutions that have streamlined their attendance and leave management. Start your free trial today.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}" class="btn-primary text-white px-10 py-4 rounded-lg font-semibold text-lg inline-flex items-center justify-center gap-2">
                            <span>Get Started Now</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <button class="btn-secondary text-white px-10 py-4 rounded-lg font-semibold text-lg inline-flex items-center justify-center gap-2">
                            <span>Schedule Demo</span>
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-200 mt-8">✓ No credit card required  •  ✓ Free for 30 days  •  ✓ Cancel anytime</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-16 px-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500 opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 opacity-5 rounded-full blur-3xl"></div>

        <div class="container mx-auto relative z-10 text-center">
            <!-- Social Media Icons -->
            <div class="flex gap-4 justify-center mb-8">
                <a href="#" class="w-12 h-12 rounded-lg bg-blue-600 hover:bg-blue-700 flex items-center justify-center transition transform hover:scale-110">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-lg bg-blue-400 hover:bg-blue-500 flex items-center justify-center transition transform hover:scale-110">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-lg bg-blue-700 hover:bg-blue-800 flex items-center justify-center transition transform hover:scale-110">
                    <i class="fab fa-linkedin text-lg"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-lg bg-pink-600 hover:bg-pink-700 flex items-center justify-center transition transform hover:scale-110">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
            </div>

            <!-- PSU Label Logo -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/PSU-LABEL-LOGO.png') }}" alt="PSU Label Logo" class="h-24 transition-transform hover:scale-105">
            </div>

            <!-- Developer Credits -->
            <div class="text-center text-gray-500 text-xs pt-8 mt-8 border-t border-gray-800">
                <p class="mb-2">Developed by: Aguilar, Larry Sykioco • Buaya, Reden Ines • Ducsa, Neslyn Arcarte • Nazaire, Edleen Grace Arenas • Riturban, Marc Denver Fernandez</p>
            </div>
        </div>
    </footer>

    <script>
        // Navigation scrolled effect
        const nav = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Counter animation function
        const animateCounters = () => {
            const counters = document.querySelectorAll('.counter');
            const speed = 30; // milliseconds between updates

            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target'));
                const increment = target / (1000 / speed);
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        if (counter.getAttribute('data-target').includes('.')) {
                            counter.textContent = current.toFixed(1);
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                        setTimeout(updateCounter, speed);
                    } else {
                        counter.textContent = counter.getAttribute('data-target');
                    }
                };

                updateCounter();
            });
        };

        // Trigger animation when section is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Animate counters when stats section is visible
                    if (entry.target.querySelector('.counter')) {
                        animateCounters();
                    }
                    // Animate scroll elements - add visible class to card children
                    const scrollElements = entry.target.querySelectorAll('.scroll-animate');
                    scrollElements.forEach(element => {
                        element.classList.add('visible');
                    });
                }
            });
        }, { threshold: 0.1 });

        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });

        // Animate elements on scroll
        const setupScrollAnimation = () => {
            const elements = document.querySelectorAll('.feature-card, .role-card, .stat-number');
            
            elements.forEach((element) => {
                // Only add if not already added
                if (!element.classList.contains('scroll-animate')) {
                    element.classList.add('scroll-animate');
                }
            });
        };

        setupScrollAnimation();

        // Set body visible by default, fade in if needed
        if (document.readyState === 'loading') {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease-in';
            window.addEventListener('load', () => {
                document.body.style.opacity = '1';
            });
        } else {
            document.body.style.opacity = '1';
        }

        // Debounce function for scroll events
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Parallax scroll effect on hero section
        const heroSection = document.querySelector('.gradient-bg');
        if (heroSection) {
            window.addEventListener('scroll', debounce(() => {
                const scrollPosition = window.scrollY;
                heroSection.style.backgroundPosition = `0 ${scrollPosition * 0.5}px`;
            }, 10));
        }

        // Add ripple effect to buttons
        document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
            button.addEventListener('click', function (e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
            });
        });

        // FAQ functionality (if needed in future)
        const setupFAQ = () => {
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question');
                if (question) {
                    question.addEventListener('click', () => {
                        const answer = item.querySelector('.faq-answer');
                        const icon = question.querySelector('.faq-icon');
                        
                        // Close other open items
                        document.querySelectorAll('.faq-item').forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.querySelector('.faq-answer').classList.remove('open');
                                otherItem.querySelector('.faq-icon').classList.remove('open');
                            }
                        });

                        // Toggle current item
                        answer.classList.toggle('open');
                        icon.classList.toggle('open');
                    });
                }
            });
        };

        setupFAQ();

        // Image lazy loading for better performance
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('loading');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    </script>
</body>
</html>
