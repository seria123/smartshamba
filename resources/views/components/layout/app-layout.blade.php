<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SmartShamba' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <x-layout.sidebar />

        <!-- Main section -->
        <div class="flex-1 flex flex-col">

            <!-- Navbar -->
            <x-layout.nav-bar />

            <!-- Page content -->
            <main class="p-6">
                {{ $slot }}
            </main>

        </div>
    </div>

</body>
</html>