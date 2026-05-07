<x-layout.app-layout title="View Setting - SmartShamba">

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Setting Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('settings.edit', $setting->id) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                    Edit
                </a>
                <a href="{{ route('settings.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back
                </a>
            </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white rounded-xl shadow-md p-6 space-y-6">

            <!-- Key -->
            <div>
                <p class="text-sm text-gray-500">Key</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $setting->key }}
                </p>
            </div>

            <!-- Value -->
            <div>
                <p class="text-sm text-gray-500">Value</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $setting->value ?? 'N/A' }}
                </p>
            </div>

            <!-- Type (if you added it) -->
            @if(isset($setting->type))
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ ucfirst($setting->type) }}
                </p>
            </div>
            @endif

            <!-- Created -->
            <div>
                <p class="text-sm text-gray-500">Created At</p>
                <p class="text-lg text-gray-800">
                    {{ $setting->created_at->format('M d, Y H:i') }}
                </p>
            </div>

            <!-- Updated -->
            <div>
                <p class="text-sm text-gray-500">Last Updated</p>
                <p class="text-lg text-gray-800">
                    {{ $setting->updated_at->format('M d, Y H:i') }}
                </p>
            </div>

        </div>

    </div>

</x-layout.app-layout>