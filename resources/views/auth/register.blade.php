<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ __('auth.create_account') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('auth.join_smartshamba') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('auth.full_name')" class="text-gray-700 font-medium" />
            <x-text-input id="name" class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="{{ __('auth.enter_full_name') }}" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-6">
            <x-input-label for="email" :value="__('auth.email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="{{ __('auth.enter_email') }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('auth.password')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors"
                          type="password"
                          name="password"
                          required autocomplete="new-password"
                          placeholder="{{ __('auth.create_password') }}" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-6">
            <x-input-label for="password_confirmation" :value="__('auth.confirm_password')" class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 transition-colors"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password"
                          placeholder="{{ __('auth.confirm_your_password') }}" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-3 text-base font-medium">
                {{ __('auth.register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <p class="text-gray-600">
            {{ __('auth.already_have_account') }}
            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-medium ml-1">
                {{ __('auth.sign_in') }}
            </a>
        </p>
    </div>
</x-guest-layout>