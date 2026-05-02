<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ __('auth.welcome_back') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('auth.sign_in_to_continue') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('auth.email')" class="text-gray-700 font-medium" />

            <x-text-input
                id="email"
                class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="{{ __('auth.enter_email') }}"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('auth.password')" class="text-gray-700 font-medium" />

            <x-text-input
                id="password"
                class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="{{ __('auth.enter_password') }}"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                    name="remember"
                >

                <span class="ms-2 text-sm text-gray-600">
                    {{ __('auth.remember_me') }}
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm text-green-600 hover:text-green-800 font-medium"
                    href="{{ route('password.request') }}"
                >
                    {{ __('auth.forgot_password') }}
                </a>
            @endif
        </div>

        <!-- Actions -->
        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-3 text-base font-medium">
                {{ __('auth.login') }}
            </x-primary-button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="mt-8 text-center">
            <p class="text-gray-600">
                {{ __('auth.dont_have_account') }}
                <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800 font-medium ml-1">
                    {{ __('auth.sign_up') }}
                </a>
            </p>
        </div>
    @endif
</x-guest-layout>