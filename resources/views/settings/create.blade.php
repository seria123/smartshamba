<x-layout.app-layout title="Create Setting - SmartShamba">

    <div class="space-y-6 max-w-3xl">

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
        <div class="bg-white rounded-2xl shadow-lg p-6">

            <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
                @csrf

                <!-- Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Category / Group
                    </label>

                    <select name="group"
                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="general">General</option>
                        <option value="farm">Farm</option>
                        <option value="crop">Crop</option>
                        <option value="livestock">Livestock</option>
                        <option value="financial">Financial</option>
                        <option value="notifications">Notifications</option>
                    </select>
                </div>

                <!-- Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Setting Key
                    </label>

                    <input type="text"
                        name="key"
                        value="{{ old('key') }}"
                        placeholder="e.g. sms_enabled"
                        required
                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Type
                    </label>

                    <select name="type" id="typeSelect"
                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">

                        <option value="string">String</option>
                        <option value="number">Number</option>
                        <option value="boolean">Boolean</option>

                    </select>
                </div>

                <!-- Value (string/number) -->
                <div id="valueField">
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Value
                    </label>

                    <input type="text"
                        name="value"
                        value="{{ old('value') }}"
                        placeholder="Enter value"
                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Boolean Value -->
                <div id="booleanField" class="hidden">
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Boolean Value
                    </label>

                    <select name="value_boolean"
                        class="w-full border-gray-300 rounded-lg">

                        <option value="1">True</option>
                        <option value="0">False</option>

                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Description
                    </label>

                    <textarea name="description"
                        placeholder="Explain what this setting controls..."
                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"></textarea>
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

    <!-- JS toggle -->
    <script>
        const typeSelect = document.getElementById('typeSelect');
        const valueField = document.getElementById('valueField');
        const booleanField = document.getElementById('booleanField');

        typeSelect.addEventListener('change', function () {
            if (this.value === 'boolean') {
                valueField.classList.add('hidden');
                booleanField.classList.remove('hidden');
            } else {
                valueField.classList.remove('hidden');
                booleanField.classList.add('hidden');
            }
        });
    </script>

</x-layout.app-layout>

  