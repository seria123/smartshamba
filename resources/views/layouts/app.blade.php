<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>{{ $title ?? 'SmartShamba' }}</title>

     @vite(['resources/css/app.css', 'resources/js/app.js'])

     <!-- Font Awesome -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 </head>
 <body class="bg-gray-100">

     <div class="flex min-h-screen">

         <!-- Sidebar -->
         <x-layout.sidebar />

         <!-- Main section -->
         <div class="flex-1 flex flex-col min-w-0">

             <!-- Navbar -->
             <x-layout.nav-bar />

             <!-- Page content -->
             <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
     @yield('content')
     {{ $slot ?? '' }}
 </main>

         </div>
     </div>

 </body>
</html>