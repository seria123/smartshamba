<x-layout.app-layout title="Edit Setting - SmartShamba">

    <div class="space-y-6 max-w-3xl">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Edit Setting</h1>

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
        <div class="bg-white rounded-2xl shadow-lg p-6">

            <form method="POST" action="{{ route('settings.update', $setting) }}" class="space-y-6">
                @csrf
             

                <!-- Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Category / Group</label>
                    <input type="text" name="group"
                           value="{{ old('group', $setting->group) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Setting Key</label>
                    <input type="text" name="key"
                           value="{{ old('key', $setting->key) }}"
                           required
                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Type</label>
                    <select name="type" id="typeSelect"
                            class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">

                        <option value="string" {{ $setting->type == 'string' ? 'selected' : '' }}>String</option>
                        <option value="number" {{ $setting->type == 'number' ? 'selected' : '' }}>Number</option>
                        <option value="boolean" {{ $setting->type == 'boolean' ? 'selected' : '' }}>Boolean</option>

                    </select>
                </div>

                <!-- Value (string/number) -->
                <div id="valueField">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Value</label>
                    <input type="text" name="value"
                           value="{{ old('value', $setting->value) }}"
                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Boolean Value -->
                <div id="booleanField" class="hidden">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Boolean Value</label>
                    <select name="value_boolean"
                            class="w-full border-gray-300 rounded-lg">

                        <option value="1" {{ $setting->value ? 'selected' : '' }}>True</option>
                        <option value="0" {{ !$setting->value ? 'selected' : '' }}>False</option>

                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description"
                              class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $setting->description) }}</textarea>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg shadow">
                        Update Setting
                    </button>
                </div>

            </form>

        </div>

    </div>

    <!-- JS Toggle -->
    <script>
        const typeSelect = document.getElementById('typeSelect');
        const valueField = document.getElementById('valueField');
        const booleanField = document.getElementById('booleanField');

        function toggleFields() {
            if (typeSelect.value === 'boolean') {
                valueField.classList.add('hidden');
                booleanField.classList.remove('hidden');
            } else {
                valueField.classList.remove('hidden');
                booleanField.classList.add('hidden');
            }
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // run on load
    </script>

</x-layout.app-layout>