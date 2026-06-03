@extends('layouts.MainLayout')

@section('title', 'Create Deworming Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Deworming Schedule</h1>
        <a href="{{ route('livestock_deworming_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock_deworming_schedules.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="livestock_id" class="block text-sm font-medium text-gray-700 mb-2">Animal</label>
                    <select name="livestock_id" id="livestock_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Animal</option>
                        @foreach($livestocks as $livestock)
                            <option value="{{ $livestock->id }}" {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>{{ $livestock->name ?? $livestock->tag_number ?? 'Animal #' . $livestock->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="livestock_type_id" class="block text-sm font-medium text-gray-700 mb-2">Animal Type</label>
                    <select name="livestock_type_id" id="livestock_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Type</option>
                        @foreach($livestockTypes as $type)
                            <option value="{{ $type->id }}" {{ old('livestock_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="breed" class="block text-sm font-medium text-gray-700 mb-2">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="group_herd_pen" class="block text-sm font-medium text-gray-700 mb-2">Group / Herd / Pen</label>
                    <input type="text" name="group_herd_pen" id="group_herd_pen" value="{{ old('group_herd_pen') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" value="{{ old('weight') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="dewormer_name" class="block text-sm font-medium text-gray-700 mb-2">Dewormer Name *</label>
                    <input type="text" name="dewormer_name" id="dewormer_name" value="{{ old('dewormer_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="dewormer_type" class="block text-sm font-medium text-gray-700 mb-2">Drug Type (Oral / Injection / Pour-on)</label>
                    <select name="dewormer_type" id="dewormer_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Type</option>
                        <option value="oral" {{ old('dewormer_type') == 'oral' ? 'selected' : '' }}>Oral</option>
                        <option value="injection" {{ old('dewormer_type') == 'injection' ? 'selected' : '' }}>Injection</option>
                        <option value="pour_on" {{ old('dewormer_type') == 'pour_on' ? 'selected' : '' }}>Pour-on</option>
                    </select>
                </div>

                <div>
                    <label for="manufacturer_brand" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer / Brand</label>
                    <input type="text" name="manufacturer_brand" id="manufacturer_brand" value="{{ old('manufacturer_brand') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', 'ml') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="deworming_frequency" class="block text-sm font-medium text-gray-700 mb-2">Deworming Frequency</label>
                    <select name="deworming_frequency" id="deworming_frequency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Frequency</option>
                        <option value="monthly" {{ old('deworming_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('deworming_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="custom" {{ old('deworming_frequency') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <div>
                    <label for="reminder_toggle" class="block text-sm font-medium text-gray-700 mb-2">Reminder</label>
                    <div class="flex items-center">
                        <input type="checkbox" name="reminder_toggle" id="reminder_toggle" {{ old('reminder_toggle') ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary">
                        <label for="reminder_toggle" class="ml-2 text-sm font-medium text-gray-700">Enable Reminder</label>
                    </div>
                </div>

                <div>
                    <label for="reminder_method" class="block text-sm font-medium text-gray-700 mb-2">Reminder Method</label>
                    <select name="reminder_method" id="reminder_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Select Method</option>
                        <option value="sms" {{ old('reminder_method') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="app_notification" {{ old('reminder_method') == 'app_notification' ? 'selected' : '' }}>App Notification</option>
                        <option value="email" {{ old('reminder_method') == 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="mt-6">
                <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">Administered By</label>
                <input type="text" name="administered_by" id="administered_by" value="{{ old('administered_by') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="supervised_by" class="block text-sm font-medium text-gray-700 mb-2">Supervised By (Optional Vet Oversight)</label>
                <input type="text" name="supervised_by" id="supervised_by" value="{{ old('supervised_by') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="administration_method" class="block text-sm font-medium text-gray-700 mb-2">Administration Method</label>
                <select name="administration_method" id="administration_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Method</option>
                    <option value="drenching" {{ old('administration_method') == 'drenching' ? 'selected' : '' }}>Drenching</option>
                    <option value="injection" {{ old('administration_method') == 'injection' ? 'selected' : '' }}>Injection</option>
                    <option value="feed_mix" {{ old('administration_method') == 'feed_mix' ? 'selected' : '' }}>Feed Mix</option>
                </select>
            </div>

            <div class="mt-6">
                <label for="supervised_by" class="block text-sm font-medium text-gray-700 mb-2">Supervised By (Optional Vet Oversight)</label>
                <input type="text" name="supervised_by" id="supervised_by" value="{{ old('supervised_by') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="farm_location" class="block text-sm font-medium text-gray-700 mb-2">Farm Location / Field</label>
                <input type="text" name="farm_location" id="farm_location" value="{{ old('farm_location') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="body_condition_score" class="block text-sm font-medium text-gray-700 mb-2">Body Condition Score (1-5)</label>
                <input type="number" name="body_condition_score" id="body_condition_score" value="{{ old('body_condition_score') }}" step="0.1" min="0" max="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="current_weight" class="block text-sm font-medium text-gray-700 mb-2">Current Weight (kg)</label>
                <input type="number" name="current_weight" id="current_weight" value="{{ old('current_weight') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="previous_deworming_date" class="block text-sm font-medium text-gray-700 mb-2">Previous Deworming Date</label>
                <input type="date" name="previous_deworming_date" id="previous_deworming_date" value="{{ old('previous_deworming_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="signs_of_infection" class="block text-sm font-medium text-gray-700 mb-2">Signs of Infection</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="diarrhea" name="signs_of_infection[]" value="diarrhea">
                        <label for="diarrhea" class="ml-2 text-sm font-medium text-gray-700">Diarrhea</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="weight_loss" name="signs_of_infection[]" value="weight_loss">
                        <label for="weight_loss" class="ml-2 text-sm font-medium text-gray-700">Weight Loss</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="dull_coat" name="signs_of_infection[]" value="dull_coat">
                        <label for="dull_coat" class="ml-2 text-sm font-medium text-gray-700">Dull Coat</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="lethargy" name="signs_of_infection[]" value="lethargy">
                        <label for="lethargy" class="ml-2 text-sm font-medium text-gray-700">Lethargy</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="reduced_appetite" name="signs_of_infection[]" value="reduced_appetite">
                        <label for="reduced_appetite" class="ml-2 text-sm font-medium text-gray-700">Reduced Appetite</label>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="resistance_history" class="block text-sm font-medium text-gray-700 mb-2">Resistance History</label>
                <textarea name="resistance_history" id="resistance_history" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('resistance_history') }}</textarea>
            </div>

            <div class="mt-6">
                <label for="animal_reaction" class="block text-sm font-medium text-gray-700 mb-2">Animal Reaction</label>
                <select name="animal_reaction" id="animal_reaction" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Reaction</option>
                    <option value="normal" {{ old('animal_reaction') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="weak" {{ old('animal_reaction') == 'weak' ? 'selected' : '' }}>Weak</option>
                    <option value="vomiting" {{ old('animal_reaction') == 'vomiting' ? 'selected' : '' }}>Vomiting</option>
                    <option value="lethargic" {{ old('animal_reaction') == 'lethargic' ? 'selected' : '' }}>Lethargic</option>
                    <option value="other" {{ old('animal_reaction') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mt-6">
                <label for="effectiveness" class="block text-sm font-medium text-gray-700 mb-2">Effectiveness</label>
                <select name="effectiveness" id="effectiveness" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Select Effectiveness</option>
                    <option value="improved" {{ old('effectiveness') == 'improved' ? 'selected' : '' }}>Improved</option>
                    <option value="no_change" {{ old('effectiveness') == 'no_change' ? 'selected' : '' }}>No Change</option>
                    <option value="worse" {{ old('effectiveness') == 'worse' ? 'selected' : '' }}>Worse</option>
                </select>
            </div>

            <div class="mt-6">
                <label for="side_effects_observed" class="block text-sm font-medium text-gray-700 mb-2">Side Effects Observed</label>
                <input type="text" name="side_effects_observed" id="side_effects_observed" value="{{ old('side_effects_observed') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes') }}</textarea>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection