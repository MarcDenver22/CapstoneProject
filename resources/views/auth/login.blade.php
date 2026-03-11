<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Faculty Employee Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-indigo-50 via-white to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo & heading --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="{{ asset('images/psu logo.png') }}" alt="PSU Logo" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Faculty Employee Portal</h1>
            <p class="text-gray-500 mt-1 text-sm">Sign in to your account</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/70 backdrop-blur-xl border border-white/80 rounded-2xl shadow-lg p-8">

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="you@psu.edu"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                        >
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-200 transition focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2"
                >
                    Sign in
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} Pangasinan State University. All rights reserved.</p>
    </div>

</body>
</html>
