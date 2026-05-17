<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">

    <!-- Logo -->
    <div class="flex items-center space-x-2">
        <x-application-logo class="h-8 w-auto" />
        <span class="font-bold text-green-700 ml-2">SmartShamba</span>
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-4">

        <!-- User Info -->
        <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600">
            <i class="fas fa-user-circle"></i>
            <span>{{ Auth::user()->name ?? 'Farmer' }}</span>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-red-500 hover:text-red-700 text-sm font-medium flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden md:inline">Logout</span>
            </button>
        </form>

    </div>

</nav>