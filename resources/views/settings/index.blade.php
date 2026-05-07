<x-layout.app-layout title="Settings - SmartShamba">

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Settings Form -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Alert Settings -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">🚨 Alert Settings</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Temperature Threshold (°C)
                            </label>
                            <input type="number" name="temperature_threshold"
                                   value="{{ $settings['temperature_threshold'] ?? '' }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Moisture Minimum (%)
                            </label>
                            <input type="number" name="moisture_min"
                                   value="{{ $settings['moisture_min'] ?? '' }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                    </div>
                </div>

                <!-- System Settings -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">⚙️ System Settings</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Dashboard Refresh Rate (seconds)
                            </label>
                            <input type="number" name="dashboard_refresh_rate"
                                   value="{{ $settings['dashboard_refresh_rate'] ?? '' }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="flex items-center space-x-3 mt-6">
                            <input type="checkbox" name="rain_alert_enabled"
                                   {{ isset($settings['rain_alert_enabled']) && $settings['rain_alert_enabled'] ? 'checked' : '' }}
                                   class="h-5 w-5 text-emerald-600 border-gray-300 rounded">
                            <label class="text-sm text-gray-600">Enable Rain Alerts</label>
                        </div>

                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg shadow transition">
                        Save Settings
                    </button>
                </div>

            </form>
        </div>

    </div>

</x-layout.app-layout>