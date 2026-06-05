@extends('layouts.MainLayout')

@section('title', 'Create Fumigation Schedule - SmartShamba')

@section('content')
<div class="space-y-1">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Fumigation Schedule</h1>
        <a href="{{ route('fumigation_schedules.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Schedules
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('fumigation_schedules.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-emerald-800 mb-4">Fumigation Type & Pest</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="fumigation_type" class="block text-sm font-medium text-gray-700 mb-2">Fumigation Type *</label>
                        <select name="fumigation_type" id="fumigation_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Type</option>
                            <option value="soil" {{ old('fumigation_type') == 'soil' ? 'selected' : '' }}>Soil</option>
                            <option value="storage" {{ old('fumigation_type') == 'storage' ? 'selected' : '' }}>Storage</option>
                            <option value="greenhouse" {{ old('fumigation_type') == 'greenhouse' ? 'selected' : '' }}>Greenhouse</option>
                        </select>
                    </div>
                    <div>
                        <label for="target_pest" class="block text-sm font-medium text-gray-700 mb-2">Target Pest *</label>
                        <select name="target_pest" id="target_pest" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Pest</option>
                            <option value="weevils" {{ old('target_pest') == 'weevils' ? 'selected' : '' }}>Weevils</option>
                            <option value="nematodes" {{ old('target_pest') == 'nematodes' ? 'selected' : '' }}>Nematodes</option>
                            <option value="fungi" {{ old('target_pest') == 'fungi' ? 'selected' : '' }}>Fungi</option>
                            <option value="rodents" {{ old('target_pest') == 'rodents' ? 'selected' : '' }}>Rodents</option>
                            <option value="other" {{ old('target_pest') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-blue-800 mb-4">Location & Area</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                        <select name="farm_id" id="farm_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Farm (Optional)</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field</label>
                        <select name="field_id" id="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Field (Optional)</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="crop_cycle_id" class="block text-sm font-medium text-gray-700 mb-2">Crop Cycle</label>
                        <select name="crop_cycle_id" id="crop_cycle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Crop Cycle (Optional)</option>
                            @foreach($cropCycles as $cropCycle)
                                <option value="{{ $cropCycle->id }}" {{ old('crop_cycle_id') == $cropCycle->id ? 'selected' : '' }}>{{ $cropCycle->crop->name ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                        <select name="location" id="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Location</option>
                            <option value="field" {{ old('location') == 'field' ? 'selected' : '' }}>Field</option>
                            <option value="store" {{ old('location') == 'store' ? 'selected' : '' }}>Storage Facility</option>
                            <option value="greenhouse" {{ old('location') == 'greenhouse' ? 'selected' : '' }}>Greenhouse</option>
                        </select>
                    </div>
                    <div>
                        <label for="area_covered" class="block text-sm font-medium text-gray-700 mb-2">Area Covered (hectares/acres)</label>
                        <input type="number" name="area_covered" id="area_covered" value="{{ old('area_covered') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="volume_covered" class="block text-sm font-medium text-gray-700 mb-2">Volume Covered (cubic meters, for storage)</label>
                        <input type="number" name="volume_covered" id="volume_covered" value="{{ old('volume_covered') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="enclosure_type" class="block text-sm font-medium text-gray-700 mb-2">Type of Enclosure</label>
                        <select name="enclosure_type" id="enclosure_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Enclosure Type</option>
                            <option value="tarpaulin" {{ old('enclosure_type') == 'tarpaulin' ? 'selected' : '' }}>Tarpaulin</option>
                            <option value="silo" {{ old('enclosure_type') == 'silo' ? 'selected' : '' }}>Silo</option>
                            <option value="greenhouse" {{ old('enclosure_type') == 'greenhouse' ? 'selected' : '' }}>Greenhouse</option>
                            <option value="open_field" {{ old('enclosure_type') == 'open_field' ? 'selected' : '' }}>Open Field</option>
                        </select>
                    </div>
                    <div>
                        <label for="sealing_status" class="block text-sm font-medium text-gray-700 mb-2">Sealing Status</label>
                        <select name="sealing_status" id="sealing_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Status</option>
                            <option value="sealed" {{ old('sealing_status') == 'sealed' ? 'selected' : '' }}>Sealed</option>
                            <option value="not_sealed" {{ old('sealing_status') == 'not_sealed' ? 'selected' : '' }}>Not Sealed</option>
                        </select>
                        <p class="text-xs text-yellow-600 mt-1">Without sealing, fumigation is ineffective and dangerous</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-purple-800 mb-4">Chemical & Dosage</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="chemical_id" class="block text-sm font-medium text-gray-700 mb-2">Chemical</label>
                        <select name="chemical_id" id="chemical_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Chemical (Optional)</option>
                            @foreach($chemicalTypes as $chem)
                                <option value="{{ $chem->id }}" {{ old('chemical_id') == $chem->id ? 'selected' : '' }}>{{ $chem->name }} ({{ $chem->toxicity_level ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="chemical_form" class="block text-sm font-medium text-gray-700 mb-2">Chemical Form *</label>
                        <select name="chemical_form" id="chemical_form" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Form</option>
                            <option value="Gas" {{ old('chemical_form') == 'Gas' ? 'selected' : '' }}>Gas</option>
                            <option value="Tablet" {{ old('chemical_form') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Pellet" {{ old('chemical_form') == 'Pellet' ? 'selected' : '' }}>Pellet</option>
                        </select>
                    </div>
                    <div>
                        <label for="active_ingredient" class="block text-sm font-medium text-gray-700 mb-2">Active Ingredient *</label>
                        <input type="text" name="active_ingredient" id="active_ingredient" value="{{ old('active_ingredient') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="toxicity_level" class="block text-sm font-medium text-gray-700 mb-2">Toxicity Level</label>
                        <select name="toxicity_level" id="toxicity_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select</option>
                            <option value="low" {{ old('toxicity_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('toxicity_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('toxicity_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('toxicity_level') == 'critical' ? 'selected' : '' }}>Critical ⚠️</option>
                        </select>
                    </div>
                    <div>
                        <label for="dosage" class="block text-sm font-medium text-gray-700 mb-2">Dosage (auto-calculated)</label>
                        <input type="text" name="dosage" id="dosage" value="{{ old('dosage') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="Auto-calculated if chemical selected">
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="application_method" class="block text-sm font-medium text-gray-700 mb-2">Application Method</label>
                        <input type="text" name="application_method" id="application_method" value="{{ old('application_method') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-indigo-800 mb-4">Timing & Duration</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date *</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="exposure_duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Exposure Duration (Hours) *</label>
                        <input type="number" name="exposure_duration_hours" id="exposure_duration_hours" value="{{ old('exposure_duration_hours', 24) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="frequency_days" class="block text-sm font-medium text-gray-700 mb-2">Frequency (Days)</label>
                        <input type="number" name="frequency_days" id="frequency_days" value="{{ old('frequency_days') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="rei_hours" class="block text-sm font-medium text-gray-700 mb-2">Re-entry Interval (REI, Hours)</label>
                        <input type="number" name="rei_hours" id="rei_hours" value="{{ old('rei_hours', 24) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="ventilation_method" class="block text-sm font-medium text-gray-700 mb-2">Ventilation Method</label>
                        <select name="ventilation_method" id="ventilation_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select</option>
                            <option value="natural" {{ old('ventilation_method') == 'natural' ? 'selected' : '' }}>Natural Airing</option>
                            <option value="forced" {{ old('ventilation_method') == 'forced' ? 'selected' : '' }}>Forced Ventilation</option>
                            <option value="mechanical" {{ old('ventilation_method') == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Status</option>
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="ventilating" {{ old('status') == 'ventilating' ? 'selected' : '' }}>Ventilating</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-red-800 mb-4">Safety Management</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="staff_operator_id" class="block text-sm font-medium text-gray-700 mb-2">Responsible Operator</label>
                        <select name="staff_operator_id" id="staff_operator_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Operator</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('staff_operator_id') == $s->id ? 'selected' : '' }}>{{ $s->fullName }} ({{ $s->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="emergency_contacts" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contacts</label>
                        <input type="text" name="emergency_contacts" id="emergency_contacts" value="{{ old('emergency_contacts') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" placeholder="Name - Phone, Name - Phone">
                    </div>
                    <div>
                        <label for="operator_certified" class="block text-sm font-medium text-gray-700 mb-2">Operator Certified</label>
                        <select name="operator_certified" id="operator_certified" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select</option>
                            <option value="1" {{ old('operator_certified') == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('operator_certified') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label for="certification_expiry" class="block text-sm font-medium text-gray-700 mb-2">Certification Expiry</label>
                        <input type="date" name="certification_expiry" id="certification_expiry" value="{{ old('certification_expiry') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="mt-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                    <h3 class="text-sm font-bold text-yellow-800 mb-3">PPE Checklist (Required before starting)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg cursor-pointer">
                            <input type="checkbox" name="ppe_respirator" value="1" {{ old('ppe_respirator') ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded">
                            <span class="text-sm font-medium text-gray-700">Respirator Mask</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg cursor-pointer">
                            <input type="checkbox" name="ppe_gloves" value="1" {{ old('ppe_gloves') ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded">
                            <span class="text-sm font-medium text-gray-700">Protective Gloves</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 bg-white rounded-lg cursor-pointer">
                            <input type="checkbox" name="ppe_suit" value="1" {{ old('ppe_suit') ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded">
                            <span class="text-sm font-medium text-gray-700">Protective Suit</span>
                        </label>
                    </div>
                    <p class="text-xs text-red-600 mt-2">⚠️ All three items must be checked to start fumigation</p>
                </div>
            </div>

            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-yellow-800 mb-4">Environmental Conditions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                        <input type="number" name="temperature" id="temperature" value="{{ old('temperature') }}" step="0.01" min="-50" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="humidity_level" class="block text-sm font-medium text-gray-700 mb-2">Humidity (%)</label>
                        <input type="number" name="humidity_level" id="humidity_level" value="{{ old('humidity_level') }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="wind_speed" class="block text-sm font-medium text-gray-700 mb-2">Wind Speed (m/s)</label>
                        <input type="number" name="wind_speed" id="wind_speed" value="{{ old('wind_speed') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <p class="text-xs text-yellow-600 mt-2">⚠️ Unsafe conditions (temp <10 or >40°C, humidity >90%, wind >15 m/s) will prevent starting</p>
            </div>

            <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Additional Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="performed_by" class="block text-sm font-medium text-gray-700 mb-2">Performed By</label>
                        <input type="text" name="performed_by" id="performed_by" value="{{ old('performed_by') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="pest_activity_before" class="block text-sm font-medium text-gray-700 mb-2">Pest Activity Before</label>
                        <select name="pest_activity_before" id="pest_activity_before" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="">Select Level</option>
                            <option value="low" {{ old('pest_activity_before') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('pest_activity_before') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('pest_activity_before') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost (KES)</label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('notes') }}</textarea>
                </div>
                <div class="mt-4">
                    <label for="operator_notes" class="block text-sm font-medium text-gray-700 mb-2">Operator Notes</label>
                    <textarea name="operator_notes" id="operator_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">{{ old('operator_notes') }}</textarea>
                </div>
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
