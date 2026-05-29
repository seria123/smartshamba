<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmartShamba')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <x-layout.sidebar />

<!-- Main Content Area -->
         <div class="flex-1 flex flex-col min-w-0">

             <!-- 🔝 NAVBAR -->
             <nav class="bg-green-700 border-b border-green-800 shadow-sm sticky top-0 z-50">
                 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                     <div class="flex justify-between items-center h-16">

                         <!-- Logo -->
                         <div class="flex items-center space-x-2">
                             <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200">
                                 <i class="fas fa-leaf text-white"></i>
                             </div>
                             <span class="font-bold text-xl text-white hidden sm:block">SmartShamba</span>
                         </div>

                         <!-- User + Mobile Toggle -->
                         <div class="flex items-center space-x-3">
                             <!-- User -->
                             <a href="{{ route('profile.edit') }}" class="hidden md:flex items-center gap-2 hover:bg-green-600 px-2 py-1 rounded-lg transition">
                                 <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                     {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                 </div>
                                 <span class="text-sm text-green-100 hidden lg:block">{{ Auth::user()->name ?? 'User' }}</span>
                             </a>

                             <!-- Logout -->
                             <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                                 @csrf
                                 <button class="flex items-center gap-2 px-3 py-1.5 text-sm text-green-100 hover:text-white hover:bg-green-600 rounded-lg border border-green-600 transition-all duration-200">
                                     <i class="fas fa-sign-out-alt"></i>
                                     <span class="hidden sm:inline">Logout</span>
                                 </button>
                             </form>

                             <!-- Mobile Button -->
                             <button id="menu-btn" class="md:hidden p-2 text-green-200 hover:text-white hover:bg-green-600 rounded-lg transition-colors">
                                 <i class="fas fa-bars text-xl"></i>
                             </button>
                         </div>

                     </div>
                 </div>
             </nav>

             <!-- 📱 MOBILE MENU -->
             <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 pb-4 space-y-2">
                 <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                     <i class="fas fa-user-circle"></i>My Profile
                 </a>
                 <div class="border-t border-gray-200 pt-2 mt-2">
                     <p class="text-sm text-gray-500 px-4 mb-2">{{ Auth::user()->name ?? 'User' }}</p>
                     <form method="POST" action="{{ route('logout') }}">
                         @csrf
                         <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                             <i class="fas fa-sign-out-alt mr-2"></i>Logout
                         </button>
                     </form>
                 </div>
             </div>

<!-- Page Content -->
              <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                  <div class="max-w-4xl mx-auto space-y-6">
                      @yield('content')
                  </div>
              </main>
         </div>
    </div>

    @stack('scripts')
</body>
</html>