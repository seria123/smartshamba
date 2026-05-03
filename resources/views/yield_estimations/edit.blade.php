@extends('layouts.MainLayout')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Update Yield Estimation</h1>
                <p class="text-gray-600 mt-1">Update forecasts and actual results</p>
            </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-amber-500 to-amber-600 rounded-full mt-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-calculator text-amber-600"></i>
                        Yield Estimation Details
                    </h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('yield_estimations.update', $yieldEstimation) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Crop Selection -->
                            <div>
                                <label for="crop_id" class="form-label">Crop</label>
                                <select name="crop_id" id="crop_id" class="form-select-modern">
                                    <option value="{{ $yieldEstimation->crop_id }}" selected>
                                        {{ $yieldEstimation->crop?->name ?? 'Current Crop' }}
                                    </option>
                                    @foreach($crops as $crop)
                                    @if($crop->id != $yieldEstimation->crop_id)
                                    <option value="{{ $crop->id }}" 
                                            data-yield-unit="{{ $crop->yield_unit }}"
                                            data-avg-yield="{{ $crop->average_yield_per_hectare }}">
                                        {{ $crop->name }}
                                        @if($crop->average_yield_per_hectare)
                                        ({{ $crop->average_yield_per_hectare }} {{ $crop->yield_unit }}/ha avg)
                                        @endif
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    Current crop: {{ $yieldEstimation->yield_unit }}
                                </p>
                            </div>

                            <!-- Field Selection -->
                            <div>
                                <label for="field_id" class="form-label">Field (Optional)</label>
                                <select name="field_id" id="field_id" class="form-select-modern">
                                    <option value="">Select a field</option>
                                    @foreach($fields as $field)
                                    <option value="{{ $field->id }}" {{ $yieldEstimation->field_id == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }} @if($field->location)• {{ $field->location }}@endif
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    Link this estimation to a specific field
                                </p>
                            </div>

                            <!-- Hectares -->
                            <div>
                                <label for="hectares" class="form-label">Area (Hectares)</label>
                                <input type="number" name="hectares" id="hectares" step="0.01" min="0.01" 
                                       class="form-input-modern" value="{{ old('hectares', $yieldEstimation->hectares) }}" required>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-ruler-combined text-gray-400"></i>
                                    Total area under cultivation
                                </p>
                            </div>

                            <!-- Season -->
                            <div>
                                <label for="season" class="form-label">Season</label>
                                <select name="season" id="season" class="form-select-modern" required>
                                    <option value="short_rain" {{ $yieldEstimation->season == 'short_rain' ? 'selected' : '' }}>Short Rain (Vuli)</option>
                                    <option value="long_rain" {{ $yieldEstimation->season == 'long_rain' ? 'selected' : '' }}>Long Rain (Masika)</option>
                                    <option value="all_season" {{ $yieldEstimation->season == 'all_season' ? 'selected' : '' }}>All Season (Year-round)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-cloud-rain text-gray-400"></i>
                                    Seasonal timing affects yield
                                </p>
                            </div>

                            <!-- Year -->
                            <div>
                                <label for="year" class="form-label">Year</label>
                                <input type="number" name="year" id="year" class="form-input-modern" 
                                       value="{{ old('year', $yieldEstimation->year) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    Year of harvest season
                                </p>
                            </div>

                            <!-- Estimated Yield -->
                            <div>
                                <label for="estimated_yield" class="form-label">Estimated Total Yield</label>
                                <input type="number" name="estimated_yield" id="estimated_yield" 
                                       step="0.01" min="0" class="form-input-modern" 
                                       value="{{ old('estimated_yield', $yieldEstimation->estimated_yield) }}" 
                                       placeholder="Leave blank for auto-calc">
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-weight-hanging text-gray-400"></i>
                                    Current: {{ number_format($yieldEstimation->estimated_yield, 2) }} {{ $yieldEstimation->yield_unit }}
                                </p>
                            </div>

                            <!-- Actual Yield -->
                            <div>
                                <label for="actual_yield" class="form-label">Actual Yield (After Harvest)</label>
                                <input type="number" name="actual_yield" id="actual_yield" 
                                       step="0.01" min="0" class="form-input-modern" 
                                       value="{{ old('actual_yield', $yieldEstimation->actual_yield) }}" 
                                       placeholder="Enter after harvest">
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-weight-hanging text-gray-400"></i>
                                    Record actual yield after harvest
                                </p>
                            </div>
                        </div>

                        <!-- Income -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="estimated_income" class="form-label">Estimated Income (KES)</label>
                                <input type="number" name="estimated_income" id="estimated_income" 
                                       step="0.01" min="0" class="form-input-modern" 
                                       value="{{ old('estimated_income', $yieldEstimation->estimated_income) }}" 
                                       placeholder="Expected revenue">
                            </div>

                            <div>
                                <label for="actual_income" class="form-label">Actual Income (KES)</label>
                                <input type="number" name="actual_income" id="actual_income" 
                                       step="0.01" min="0" class="form-input-modern" 
                                       value="{{ old('actual_income', $yieldEstimation->actual_income) }}" 
                                       placeholder="Actual revenue after sale">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select-modern">
                                <option value="planned" {{ $yieldEstimation->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ $yieldEstimation->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="harvested" {{ $yieldEstimation->status == 'harvested' ? 'selected' : '' }}>Harvested</option>
                                <option value="failed" {{ $yieldEstimation->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-input-modern" placeholder="Additional notes...">{{ $yieldEstimation->notes }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="submit" class="btn-primary flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Update Estimation</span>
                            </button>
                            <a href="{{ route('yield_estimations.show', $yieldEstimation) }}" 
                               class="btn-secondary flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 sticky top-24">
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-teal-600"></i>
                        Current Statistics
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-sm text-gray-500 mb-1">Yield/Hectare</div>
                        <div class="text-xl font-bold text-gray-900">
                            {{ number_format($yieldEstimation->yield_per_hectare, 2) }}
                        </div>
                        <div class="text-xs text-gray-400">{{ $yieldEstimation->yield_unit }}/ha</div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-sm text-gray-500 mb-1">Est. Total Yield</div>
                        <div class="text-xl font-bold text-gray-900">
                            {{ number_format($yieldEstimation->estimated_yield, 2) }}
                        </div>
                        <div class="text-xs text-gray-400">{{ $yieldEstimation->yield_unit }}</div>
                    </div>

                    @if($yieldEstimation->actual_yield)
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <div class="text-sm text-green-600 mb-1">Actual Total Yield</div>
                        <div class="text-xl font-bold text-green-700">
                            {{ number_format($yieldEstimation->actual_yield, 2) }}
                        </div>
                        <div class="text-xs text-green-500">{{ $yieldEstimation->yield_unit }}</div>
                    </div>
                    @endif
                </div>

                <div class="p-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="#" onclick="document.getElementById('status').value='in_progress'; this.closest('form').submit();" 
                           class="w-full flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm hover:bg-blue-100 transition-colors">
                            <i class="fas fa-play"></i>
                            Mark In Progress
                        </a>
                        <a href="#" onclick="document.getElementById('status').value='harvested'; this.closest('form').submit();"
                           class="w-full flex items-center gap-2 px-3 py-2 bg-green-50 text-green-700 rounded-lg text-sm hover:bg-green-100 transition-colors">
                            <i class="fas fa-check"></i>
                            Mark Harvested
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cropSelect = document.getElementById('crop_id');
        const hectaresInput = document.getElementById('hectares');
        const estimatedYieldInput = document.getElementById('estimated_yield');

        // Auto-calculate yield based on crop average when changed
        cropSelect.addEventListener('change', function() {
            if (!estimatedYieldInput.value) {
                const selectedOption = this.options[this.selectedIndex];
                const avgYield = parseFloat(selectedOption.dataset.avgYield);
                const hectares = parseFloat(hectaresInput.value);
                if (avgYield && hectares) {
                    estimatedYieldInput.value = (avgYield * hectares).toFixed(2);
                }
            }
        });
    });
</script>
@endpush
