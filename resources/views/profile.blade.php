@extends('layouts.MainLayout')

@section('title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-primary text-white flex items-center justify-center rounded-full text-2xl">
                <i class="fas fa-user"></i>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $user->name ?? Auth::user()->name ?? 'User' }}
                </h2>

                <p class="text-gray-500">
                    {{ $user->email ?? Auth::user()->email ?? '' }}
                </p>

                <span class="inline-block mt-2 px-3 py-1 text-sm rounded-full 
                    {{ (Auth::user()->role ?? 'user') === 'admin' ? 'bg-yellow-500' : 'bg-blue-500' }} text-white">
                    {{ ucfirst(Auth::user()->role ?? 'User') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Profile Grid -->
    <div class="grid md:grid-cols-3 gap-6">

        <!-- Account Info -->
        <div class="bg-white shadow rounded-lg p-5">
            <h3 class="font-semibold text-lg mb-4">
                <i class="fas fa-id-card mr-2 text-primary"></i>Account Info
            </h3>

            <div class="space-y-3 text-sm text-gray-700">
                <p><span class="font-semibold">Name:</span> {{ $user->name ?? Auth::user()->name ?? 'User' }}</p>
                <p><span class="font-semibold">Email:</span> {{ $user->email ?? Auth::user()->email ?? '' }}</p>
                <p><span class="font-semibold">Role:</span> {{ ucfirst($user->role ?? Auth::user()->role ?? 'user') }}</p>
                <p><span class="font-semibold">Joined:</span> {{ isset($user->created_at) ? $user->created_at->format('d M Y') : (Auth::user()->created_at->format('d M Y') ?? '') }}</p>
            </div>
        </div>

        <!-- Quick Stats (future-ready placeholders) -->
        <div class="bg-white shadow rounded-lg p-5">
            <h3 class="font-semibold text-lg mb-4">
                <i class="fas fa-chart-bar mr-2 text-primary"></i>Activity
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span>Farms</span>
                    <span class="font-bold">--</span>
                </div>

                <div class="flex justify-between">
                    <span>Crops</span>
                    <span class="font-bold">--</span>
                </div>

                <div class="flex justify-between">
                    <span>Tasks</span>
                    <span class="font-bold">--</span>
                </div>

                <div class="flex justify-between">
                    <span>Alerts</span>
                    <span class="font-bold">--</span>
                </div>
            </div>
        </div>

        <!-- Settings / Actions -->
        <div class="bg-white shadow rounded-lg p-5">
            <h3 class="font-semibold text-lg mb-4">
                <i class="fas fa-cog mr-2 text-primary"></i>Settings
            </h3>

            <div class="space-y-3">
                <button onclick="document.getElementById('edit-profile-panel').classList.remove('hidden')"
                    class="block w-full text-left px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                    Edit Profile
                </button>

                <button onclick="document.getElementById('password-panel').classList.remove('hidden')"
                    class="block w-full text-left px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                    Change Password
                </button>

                <a href="{{ route('dashboard') }}" class="block px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">
                    Back to Dashboard
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Change Password Mini Panel -->
<div id="password-panel" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">
                Change Password
            </h2>

            <button onclick="document.getElementById('password-panel').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="text-sm">Current Password</label>
                <input type="password" name="current_password"
                       class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="text-sm">New Password</label>
                <input type="password" name="password"
                       class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="text-sm">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded p-2" required>
            </div>

            <button class="w-full bg-primary text-white py-2 rounded">
                Update Password
            </button>
        </form>
    </div>
</div>
<!-- Edit Profile Mini Panel -->
<div id="edit-profile-panel" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Edit Profile</h2>

            <button onclick="document.getElementById('edit-profile-panel').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="text-sm">Name</label>
                <input type="text" name="name"
                       value="{{ $user->name ?? Auth::user()->name ?? '' }}"
                       class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="text-sm">Email</label>
                <input type="email" name="email"
                       value="{{ $user->email ?? Auth::user()->email ?? '' }}"
                       class="w-full border rounded p-2" required>
            </div>

            <button class="w-full bg-primary text-white py-2 rounded">
                Save Changes
            </button>
        </form>

    </div>
</div>

@endsection