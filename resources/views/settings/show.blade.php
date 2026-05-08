<x-layout.app-layout title="View Setting - SmartShamba">

    <div class="space-y-6 max-w-3xl">

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

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">

   <!-- Group -->
            @if(isset($setting->group))
            <div>
                <p class="text-sm text-gray-500">Category</p>
                <span class="inline-block bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-medium">
                    {{ ucfirst($setting->group) }}
                </span>
            </div>
            @endif

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
         @if($setting->type === 'boolean')
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $setting->value ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $setting->value ? 'Enabled' : 'Disabled' }}
                    </span>
                @else
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $setting->value ?? 'N/A' }}
                    </p>
                @endif
            </div>

            <!-- Type -->
            @if(isset($setting->type))
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                    {{ ucfirst($setting->type) }}
                </span>
            </div>
            @endif
      <!-- Description -->
            @if(isset($setting->description) && $setting->description)
            <div>
                <p class="text-sm text-gray-500">Description</p>
                <p class="text-gray-700">
                    {{ $setting->description }}
                </p>
            </div>
            @endif

            <!-- Divider -->
            <hr>

            <!-- Metadata -->
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <p class="text-sm text-gray-500">Created At</p>
                    <p class="text-gray-800">
                        {{ $setting->created_at->format('M d, Y H:i') }}
                    </p>
                 </div>

                <div>
                    <p class="text-sm text-gray-500">Last Updated</p>
                    <p class="text-gray-800">
                        {{ $setting->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>

            </div>

        </div>

    </div>

</x-layout.app-layout>           