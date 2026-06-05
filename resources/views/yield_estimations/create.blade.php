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
                <h1 class="text-3xl font-bold text-gray-900">Create Yield Estimation</h1>
                <p class="text-gray-600 mt-1">Forecast your crop production and expected yields</p>
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
                    <form action="{{ route('yield_estimations.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Crop Selection -->
                            <div>
                                <label for="crop_id" class="form-label">Crop <span class="text-red-500">*</span></label>
                                <select name="crop_id" id="crop_id" class="form-select-modern" required>
                                    <option value="">Select a crop</option>
                                    @foreach($crops as $crop)
                                    <option value="{{ $crop->id }}" 
                                            data-yield-unit="{{ $crop->yield_unit }}"
                                            data-avg-yield="{{ $crop->average_yield_per_hectare }}">
                                        {{ $crop->name }}
                                        @if($crop->average_yield_per_hectare)
                                        ({{ $crop->average_yield_per_hectare }} {{ $crop->yield_unit }}/ha avg)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    Select the crop for yield estimation
                                </p>
                            </div>

                            <!-- Field Selection -->
                            <div>
                                <label for="field_id" class="form-label">Field (Optional)</label>
                                <select name="field_id" id="field_id" class="form-select-modern">
                                    <option value="">Select a field</option>
                                    @foreach($fields as $field)
                                    <option value="{{ $field->id }}">{{ $field->name }} @if($field->location)• {{ $field->location }}@endif</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    Link this estimation to a specific field
                                </p>
                            </div>

                            <!-- Hectares -->
                            <div>
                                <label for="hectares" class="form-label">Area (Hectares) <span class="text-red-500">*</span></label>
                                <input type="number" name="hectares" id="hectares" step="0.01" min="0.01" 
                                       class="form-input-modern" placeholder="e.g. 5.5" required>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-ruler-combined text-gray-400"></i>
                                    Total area under cultivation
                                </p>
                            </div>

                            <!-- Season -->
                            <div>
                                <label for="season" class="form-label">Season <span class="text-red-500">*</span></label>
                                <select name="season" id="season" class="form-select-modern" required>
                                    <option value="">Select season</option>
                                    <option value="short_rain">Short Rain (Vuli)</option>
                                    <option value="long_rain">Long Rain (Masika)</option>
                                    <option value="all_season">All Season (Year-round)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-cloud-rain text-gray-400"></i>
                                    Seasonal timing affects yield
                                </p>
                            </div>

                            <!-- Year -->
                            <div>
                                <label for="year" class="form-label">Year <span class="text-red-500">*</span></label>
                                <input type="number" name="year" id="year" class="form-input-modern" 
                                       value="{{ date('Y') }}" min="2000" max="{{ date('Y') + 1 }}" required>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    Year of harvest season
                                </p>
                            </div>

                            <!-- Estimated Yield -->
                            <div>
                                <label for="estimated_yield" class="form-label">Estimated Total Yield</label>
                                <input type="number" name="estimated_yield" id="estimated_yield" 
                                       step="0.01" min="0" class="form-input-modern" placeholder="Auto-calculated">
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <i class="fas fa-weight-hanging text-gray-400"></i>
                                    Leave blank to calculate based on average yield
                                </p>
                                <div id="autoYieldHint" class="text-xs text-emerald-600 mt-1 hidden">
                                    <i class="fas fa-calculator"></i>
                                    Auto-calculated: <span id="calcYield"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Estimated Income -->
                        <div class="mt-4">
                            <label for="estimated_income" class="form-label">Estimated Income (KES)</label>
                            <input type="number" name="estimated_income" id="estimated_income" 
                                   step="0.01" min="0" class="form-input-modern" placeholder="Expected revenue">
                            <p class="text-xs text-gray-500 mt-1.5">
                                <i class="fas fa-money-bill-wave text-gray-400"></i>
                                Projected income at harvest (optional)
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="form-select-modern" required>
                                <option value="planned" selected>Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="harvested">Harvested</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-input-modern" placeholder="Additional notes about this estimation..."></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="submit" class="btn-primary flex items-center justify-center gap-2">
                                <i class="fas fa-calculator"></i>
                                <span>Create Estimation</span>
                            </button>
                            <a href="{{ route('yield-estimations.index') }}" 
                               class="btn-secondary flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tips Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 sticky top-24">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-600"></i>
                        Estimation Tips
                    </h3>
                </div>
                
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-seedling text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Base estimates on historical crop performance</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-water text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Consider seasonal rainfall patterns</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-tint-slash text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Factor in potential pest/disease losses (10-20%)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-chart-bar text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Update actual yield after harvest</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-history text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Compare with previous seasons for trends</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-leaf text-emerald-700 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Use field-specific data when available</span>
                        </li>
                    </ul>

                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Calculation Method</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600">
                                <span class="font-medium">Yield/Hectare</span> = Total Yield ÷ Area<br>
                                <span class="font-medium">Expected Income</span> = Total Yield × Market Rate
                            </p>
                        </div>
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
        const autoYieldHint = document.getElementById('autoYieldHint');
        const calcYield = document.getElementById('calcYield');
        const yearInput = document.getElementById('year');

        // Current year max
        const currentYear = new Date().getFullYear();
        yearInput.max = currentYear + 1;

        // Auto-calculate yield based on crop average
        function updateAutoYield() {
            const selectedOption = cropSelect.options[cropSelect.selectedIndex];
            const avgYield = parseFloat(selectedOption.dataset.avgYield);
            const hectares = parseFloat(hectaresInput.value);

            if (avgYield && hectares && !estimatedYieldInput.value) {
                const calculated = avgYield * hectares;
                calcYield.textContent = calculated.toFixed(2) + ' ' + selectedOption.dataset.yieldUnit;
                autoYieldHint.classList.remove('hidden');
            } else {
                autoYieldHint.classList.add('hidden');
            }
        }

        cropSelect.addEventListener('change', updateAutoYield);
        hectaresInput.addEventListener('input', updateAutoYield);
    });
</script>
@endpush
