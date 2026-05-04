<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Preferences & Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Manage your application preferences, notifications, and AI features.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.preferences.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Preferred Language -->
            <div>
                <x-input-label for="preferred_language" :value="__('Preferred Language')" />
                <select id="preferred_language" name="preferred_language" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="en" {{ old('preferred_language', $user->preferred_language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                    <option value="sw" {{ old('preferred_language', $user->preferred_language) == 'sw' ? 'selected' : '' }}>Swahili</option>
                    <option value="fr" {{ old('preferred_language', $user->preferred_language) == 'fr' ? 'selected' : '' }}>French</option>
                    <option value="lu" {{ old('preferred_language', $user->preferred_language) == 'lu' ? 'selected' : '' }}>Kikuyu</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('preferred_language')" />
                <p class="text-xs text-gray-500 mt-1">Choose your preferred language for the interface.</p>
            </div>

            <!-- Main Purpose (from farm) -->
            <div>
                <x-input-label for="main_purpose" :value="__('Farm Main Purpose')" />
                <select id="main_purpose" name="main_purpose" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select purpose...</option>
                    <option value="commercial" {{ old('main_purpose', $user->farm->main_purpose ?? '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="subsistence" {{ old('main_purpose', $user->farm->main_purpose ?? '') == 'subsistence' ? 'selected' : '' }}>Subsistence</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('main_purpose')" />
                <p class="text-xs text-gray-500 mt-1">Primary objective of your farming operation.</p>
            </div>
        </div>

        <hr class="my-4">

        <!-- Notification Preferences (JSON-based) -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-3">Notification Preferences</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $notif = old('notification_preferences', $user->notification_preferences ?? []);
                    if (!is_array($notif)) {
                        $notif = json_decode($notif, true) ?? [];
                    }
                @endphp
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_email" name="notification_preferences[email]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['email']) ? ($notif['email'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_email" class="text-sm text-gray-700">Email Notifications</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_weather" name="notification_preferences[weather]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['weather']) ? ($notif['weather'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_weather" class="text-sm text-gray-700">Weather Updates</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_crop" name="notification_preferences[crop]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['crop']) ? ($notif['crop'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_crop" class="text-sm text-gray-700">Crop Alerts</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_livestock" name="notification_preferences[livestock]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['livestock']) ? ($notif['livestock'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_livestock" class="text-sm text-gray-700">Livestock Alerts</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_finance" name="notification_preferences[finance]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['finance']) ? ($notif['finance'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_finance" class="text-sm text-gray-700">Financial Alerts</label>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="notif_marketing" name="notification_preferences[marketing]" 
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ isset($notif['marketing']) ? ($notif['marketing'] ? 'checked' : '') : 'checked' }}>
                    <label for="notif_marketing" class="text-sm text-gray-700">Marketing & Promo</label>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Feature Toggles -->
        <div>
            <h3 class="text-md font-medium text-gray-900 mb-3">Feature Toggles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Weather Alerts -->
                <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Weather Alerts</p>
                        <p class="text-sm text-gray-600">Receive automatic weather warnings for your farm location.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="weather_alerts" class="sr-only peer" 
                               {{ old('weather_alerts', $user->weather_alerts ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- AI Recommendations -->
                <div class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">AI Recommendations</p>
                        <p class="text-sm text-gray-600">Get AI-powered suggestions for crop/livestock health & operations.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ai_recommendations" class="sr-only peer" 
                               {{ old('ai_recommendations', $user->ai_recommendations ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t">
            <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>

            @if (session('status') === 'preferences-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
