<x-layout.app-layout title="Create Setting - SmartShamba">

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Create Setting</h1>
            <a href="{{ route('settings.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back
            </a>
        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
                @csrf

                <!-- Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Setting Key
                    </label>
                    <input type="text" name="key"
                           value="{{ old('key') }}"
                           placeholder="e.g. temperature_threshold"
                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                           required>
                </div>

                <!-- Value -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Value
                    </label>
                    <input type="text" name="value"
                           value="{{ old('value') }}"
                           placeholder="Enter value"
                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Type (optional upgrade) -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Type
                    </label>
                    <select name="type"
                            class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="string">String</option>
                        <option value="number">Number</option>
                        <option value="boolean">Boolean</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg shadow">
                        Save Setting
                    </button>
                </div>

            </form>
        </div>

    </div>

</x-layout.app-layout>