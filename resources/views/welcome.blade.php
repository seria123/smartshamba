<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartShamba - Welcome</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .grain-bg {
            background: radial-gradient(circle at top, #e8f5e9, #f1f8e9, #ffffff);
        }
    </style>
</head>

<body class="grain-bg min-h-screen flex flex-col">

    <!-- NAV -->
    <nav class="flex items-center justify-between px-8 py-5 bg-white/70 backdrop-blur shadow-sm">
        <h1 class="text-2xl font-bold text-green-700 tracking-wide">
            🌿 SmartShamba
        </h1>

        <div class="space-x-4">
            <a href="{{ route('login') }}" class="text-green-700 hover:text-green-900 font-medium">
                Login
            </a>
            <a href="{{ route('register') }}"
               class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
                Get Started
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <main class="flex-1 flex items-center justify-center px-6">
        <div class="max-w-4xl text-center">

            <h2 class="text-4xl md:text-6xl font-extrabold text-gray-800 leading-tight">
                Grow Smarter with <span class="text-green-600">SmartShamba</span>
            </h2>

            <p class="mt-6 text-lg text-gray-600">
                Your intelligent farming companion for livestock tracking, crop analysis,
                fertilizer insights, and real-time farm decisions — all in one place.
            </p>

            <div class="mt-10 flex justify-center gap-4">
                <a href="{{ route('register') }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-green-700 transition">
                    Start Farming Smart 🌱
                </a>

                <a href="#features"
                   class="border border-green-600 text-green-700 px-6 py-3 rounded-xl hover:bg-green-50 transition">
                    Explore Features
                </a>
            </div>

            <!-- Decorative stats -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">

                <div class="bg-white shadow rounded-xl p-6">
                    <p class="text-3xl font-bold text-green-700">📊</p>
                    <p class="mt-2 font-semibold">Smart Analytics</p>
                    <p class="text-sm text-gray-500">Crop & livestock insights</p>
                </div>

                <div class="bg-white shadow rounded-xl p-6">
                    <p class="text-3xl font-bold text-green-700">🐄</p>
                    <p class="mt-2 font-semibold">Livestock Tracking</p>
                    <p class="text-sm text-gray-500">Health & productivity monitoring</p>
                </div>

                <div class="bg-white shadow rounded-xl p-6">
                    <p class="text-3xl font-bold text-green-700">🌦️</p>
                    <p class="mt-2 font-semibold">Farm Intelligence</p>
                    <p class="text-sm text-gray-500">Data-driven farming decisions</p>
                </div>

            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-6 text-gray-500 text-sm">
        © {{ date('Y') }} SmartShamba. Cultivating intelligence in every harvest 🌾
    </footer>

</body>
</html>