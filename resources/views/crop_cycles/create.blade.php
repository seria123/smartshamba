@extends('layouts.MainLayout')
@section('title', 'Add New Crop Cycle - SmartShamba')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Add New Crop Cycle
                    </h1>
                </div>

                <div class="p-6">
                    <form action="{{ route('crop_cycles.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Field Selection -->
                            <div>
                                <label for="field_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Field
                                </label>
                                <select id="field_id" name="field_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a field</option>
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->id }}">
                                            {{ $field->name }} ({{ $field->farm->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Crop Selection -->
                            <div>
                                <label for="crop_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Crop
                                </label>
                                <select id="crop_id" name="crop_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a crop</option>
                                    @foreach ($crops as $crop)
                                        <option value="{{ $crop->id }}">
                                            {{ $crop->name }} {{ $crop->variety ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Start Date (Planting Date)
                                </label>
                                <input type="date" id="start_date" name="start_date" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Expected Harvest Date -->
                            <div>
                                <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Expected Harvest Date (Optional)
                                </label>
                                <input type="date" id="expected_harvest_date" name="expected_harvest_date"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="btn btn-primary w-flex items-center justify-center">
                                Create Crop Cycle
                            </button>
                            <a href="{{ route('crop_cycles.index') }}"
                               class="btn btn-outline ml-3 w-flex items-center justify-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
             </div>
         </div>
     </div>
@endsection