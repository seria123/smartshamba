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
          <form method="POST" action="{{ route('settings.bulkUpdate') }}" class="space-y-6">
    @csrf
  

                <!-- Core Settings -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">⚙️ Core Settings</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Default Unit - Weight
                            </label>
                            <select name="default_weight_unit"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="kg" {{ ($settings['default_weight_unit'] ?? 'kg') === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Default Unit - Area
                            </label>
                            <select name="default_area_unit"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="hectares" {{ ($settings['default_area_unit'] ?? 'hectares') === 'hectares' ? 'selected' : '' }}>Hectares</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Currency
                            </label>
                            <select name="currency"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="KES" {{ ($settings['currency'] ?? 'KES') === 'KES' ? 'selected' : '' }}>KES (Kenyan Shilling)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Timezone
                            </label>
                            <select name="timezone"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="Africa/Nairobi" {{ ($settings['timezone'] ?? 'Africa/Nairobi') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Date Format
                            </label>
                            <select name="date_format"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-01-15)</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Notifications -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">🔔 Notifications</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="low_stock_alerts"
                                   {{ isset($settings['low_stock_alerts']) && $settings['low_stock_alerts'] ? 'checked' : '' }}
                                   class="h-5 w-5 text-emerald-600 border-gray-300 rounded">
                            <label class="text-sm text-gray-600">Low Stock Alerts</label>
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="disease_alerts"
                                   {{ isset($settings['disease_alerts']) && $settings['disease_alerts'] ? 'checked' : '' }}
                                   class="h-5 w-5 text-emerald-600 border-gray-300 rounded">
                            <label class="text-sm text-gray-600">Disease Alerts</label>
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="reminders"
                                   {{ isset($settings['reminders']) && $settings['reminders'] ? 'checked' : '' }}
                                   class="h-5 w-5 text-emerald-600 border-gray-300 rounded">
                            <label class="text-sm text-gray-600">General Reminders</label>
                        </div>

                    </div>
                </div>

                <!-- Integrations -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">🌦️ Integrations</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Plant.id API Key
                            </label>
                            <input type="text" name="plantid_api_key"
                                   value="{{ $settings['plantid_api_key'] ?? '' }}"
                                   placeholder="Enter Plant.id API key"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Weather API Key
                            </label>
                            <input type="text" name="weather_api_key"
                                   value="{{ $settings['weather_api_key'] ?? '' }}"
                                   placeholder="Enter Weather API key"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                M-Pesa API Key
                            </label>
                            <input type="text" name="mpesa_api_key"
                                   value="{{ $settings['mpesa_api_key'] ?? '' }}"
                                   placeholder="Enter M-Pesa API key"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Sensor Connection Status
                            </label>
                            <select name="sensor_connection"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="active" {{ ($settings['sensor_connection'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($settings['sensor_connection'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Security -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">🔐 Security</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Minimum Password Length
                            </label>
                            <input type="number" name="password_min_length"
                                   value="{{ $settings['password_min_length'] ?? '8' }}"
                                   min="6" max="50"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Access Policy
                            </label>
                            <select name="access_policy"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="role_based" {{ ($settings['access_policy'] ?? 'role_based') === 'role_based' ? 'selected' : '' }}>Role Based</option>
                                <option value="open" {{ ($settings['access_policy'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- System Maintenance -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">🛠️ System Maintenance</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Backup Frequency
                            </label>
                            <select name="backup_frequency"
                                    class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="daily" {{ ($settings['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ ($settings['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="manual" {{ ($settings['backup_frequency'] ?? '') === 'manual' ? 'selected' : '' }}>Manual Only</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                Log Retention (days)
                            </label>
                            <input type="number" name="log_retention_days"
                                   value="{{ $settings['log_retention_days'] ?? '30' }}"
                                   min="1" max="365"
                                   class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="enable_import_export"
                                   {{ isset($settings['enable_import_export']) && $settings['enable_import_export'] ? 'checked' : '' }}
                                   class="h-5 w-5 text-emerald-600 border-gray-300 rounded">
                            <label class="text-sm text-gray-600">Enable Data Import/Export</label>
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