@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Harvest Record</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('harvests.update', $harvest) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                         <div class="row">
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="crop_id" class="form-label">Crop *</label>
                                     <select class="form-select @error('crop_id') is-invalid @enderror" id="crop_id" name="crop_id" required>
                                         <option value="">Select Crop</option>
                                         @foreach($crops as $crop)
                                         <option value="{{ $crop->id }}" {{ old('crop_id', $harvest->crop_id) == $crop->id ? 'selected' : '' }}>
                                             {{ $crop->name }}
                                         </option>
                                         @endforeach
                                     </select>
                                     @error('crop_id')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="field_id" class="form-label">Field *</label>
                                     <select class="form-select @error('field_id') is-invalid @enderror" id="field_id" name="field_id" required>
                                         <option value="">Select Field</option>
                                         @foreach($fields as $field)
                                         <option value="{{ $field->id }}" {{ old('field_id', $harvest->field_id) == $field->id ? 'selected' : '' }}>
                                             {{ $field->name }}
                                         </option>
                                         @endforeach
                                     </select>
                                     @error('field_id')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="farm_id" class="form-label">Farm *</label>
                                     <select class="form-select @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                         <option value="">Select Farm</option>
                                         @foreach(\App\Models\Farm::all() as $farm)
                                         <option value="{{ $farm->id }}" {{ old('farm_id', $harvest->farm_id) == $farm->id ? 'selected' : '' }}>
                                             {{ $farm->name }}
                                         </option>
                                         @endforeach
                                     </select>
                                     @error('farm_id')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="crop_cycle_id" class="form-label">Crop Cycle (Optional)</label>
                                     <select class="form-select @error('crop_cycle_id') is-invalid @enderror" id="crop_cycle_id" name="crop_cycle_id">
                                         <option value="">Select Crop Cycle (Optional)</option>
                                         @foreach(\App\Models\CropCycle::with(['crop', 'field'])->get() as $cycle)
                                             <option value="{{ $cycle->id }}" {{ old('crop_cycle_id', $harvest->crop_cycle_id) == $cycle->id ? 'selected' : '' }}>
                                                 {{ $cycle->crop->name }} - {{ $cycle->field->name ?? 'No Field' }} ({{ $cycle->start_date->format('M d, Y') }})
                                             </option>
                                         @endforeach
                                     </select>
                                     @error('crop_cycle_id')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="harvest_date" class="form-label">Harvest Date *</label>
                                     <input type="date" class="form-control @error('harvest_date') is-invalid @enderror" 
                                         id="harvest_date" name="harvest_date" value="{{ old('harvest_date', $harvest->harvest_date->toDateString()) }}" required>
                                     @error('harvest_date')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="mb-3">
                                     <label for="harvest_batch" class="form-label">Batch Number</label>
                                     <input type="text" class="form-control @error('harvest_batch') is-invalid @enderror" 
                                         id="harvest_batch" name="harvest_batch" value="{{ old('harvest_batch', $harvest->harvest_batch) }}">
                                     @error('harvest_batch')
                                     <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                         </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="field_id" class="form-label">Field *</label>
                                    <select class="form-select @error('field_id') is-invalid @enderror" id="field_id" name="field_id" required>
                                        <option value="">Select Field</option>
                                        @foreach($fields as $field)
                                        <option value="{{ $field->id }}" {{ old('field_id', $harvest->field_id) == $field->id ? 'selected' : '' }}>
                                            {{ $field->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('field_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="farm_id" class="form-label">Farm *</label>
                                    <select class="form-select @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                        <option value="">Select Farm</option>
                                        @foreach(\App\Models\Farm::all() as $farm)
                                        <option value="{{ $farm->id }}" {{ old('farm_id', $harvest->farm_id) == $farm->id ? 'selected' : '' }}>
                                            {{ $farm->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('farm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="harvest_date" class="form-label">Harvest Date *</label>
                                    <input type="date" class="form-control @error('harvest_date') is-invalid @enderror" 
                                        id="harvest_date" name="harvest_date" value="{{ old('harvest_date', $harvest->harvest_date->toDateString()) }}" required>
                                    @error('harvest_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="harvest_batch" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control @error('harvest_batch') is-invalid @enderror" 
                                        id="harvest_batch" name="harvest_batch" value="{{ old('harvest_batch', $harvest->harvest_batch) }}">
                                    @error('harvest_batch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                        <option value="kg" {{ old('unit', $harvest->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                        <option value="tons" {{ old('unit', $harvest->unit) == 'tons' ? 'selected' : '' }}>Tons</option>
                                        <option value="lbs" {{ old('unit', $harvest->unit) == 'lbs' ? 'selected' : '' }}>Pounds (lbs)</option>
                                        <option value="bags" {{ old('unit', $harvest->unit) == 'bags' ? 'selected' : '' }}>Bags</option>
                                    </select>
                                    @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5>Harvest Quantity & Quality</h5>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="quantity_harvested" class="form-label">Quantity Harvested *</label>
                                    <input type="number" step="0.01" class="form-control @error('quantity_harvested') is-invalid @enderror" 
                                        id="quantity_harvested" name="quantity_harvested" value="{{ old('quantity_harvested', $harvest->quantity_harvested) }}" required>
                                    @error('quantity_harvested')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="quality_grade" class="form-label">Quality Grade *</label>
                                    <select class="form-select @error('quality_grade') is-invalid @enderror" id="quality_grade" name="quality_grade" required>
                                        <option value="grade_a" {{ old('quality_grade', $harvest->quality_grade) == 'grade_a' ? 'selected' : '' }}>Grade A (Premium)</option>
                                        <option value="grade_b" {{ old('quality_grade', $harvest->quality_grade) == 'grade_b' ? 'selected' : '' }}>Grade B (Standard)</option>
                                        <option value="grade_c" {{ old('quality_grade', $harvest->quality_grade) == 'grade_c' ? 'selected' : '' }}>Grade C (Economy)</option>
                                        <option value="reject" {{ old('quality_grade', $harvest->quality_grade) == 'reject' ? 'selected' : '' }}>Reject</option>
                                    </select>
                                    @error('quality_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="quality_percentage" class="form-label">Quality %</label>
                                    <input type="number" step="0.01" class="form-control @error('quality_percentage') is-invalid @enderror" 
                                        id="quality_percentage" name="quality_percentage" value="{{ old('quality_percentage', $harvest->quality_percentage) }}">
                                    @error('quality_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="moisture_content" class="form-label">Moisture Content %</label>
                                    <input type="number" step="0.01" class="form-control @error('moisture_content') is-invalid @enderror" 
                                        id="moisture_content" name="moisture_content" value="{{ old('moisture_content', $harvest->moisture_content) }}">
                                    @error('moisture_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5>Losses (if any)</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="loss_quantity" class="form-label">Loss Quantity</label>
                                    <input type="number" step="0.01" class="form-control @error('loss_quantity') is-invalid @enderror" 
                                        id="loss_quantity" name="loss_quantity" value="{{ old('loss_quantity', $harvest->loss_quantity) }}">
                                    @error('loss_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="loss_reason" class="form-label">Loss Reason</label>
                                    <input type="text" class="form-control @error('loss_reason') is-invalid @enderror" 
                                        id="loss_reason" name="loss_reason" value="{{ old('loss_reason', $harvest->loss_reason) }}">
                                    @error('loss_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5>Storage</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="storage_location" class="form-label">Storage Location *</label>
                                    <select class="form-select @error('storage_location') is-invalid @enderror" id="storage_location" name="storage_location" required>
                                        <option value="warehouse" {{ old('storage_location', $harvest->storage_location) == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                        <option value="barn" {{ old('storage_location', $harvest->storage_location) == 'barn' ? 'selected' : '' }}>Barn</option>
                                        <option value="cold_storage" {{ old('storage_location', $harvest->storage_location) == 'cold_storage' ? 'selected' : '' }}>Cold Storage</option>
                                        <option value="field" {{ old('storage_location', $harvest->storage_location) == 'field' ? 'selected' : '' }}>In Field</option>
                                        <option value="sold_immediately" {{ old('storage_location', $harvest->storage_location) == 'sold_immediately' ? 'selected' : '' }}>Sold Immediately</option>
                                    </select>
                                    @error('storage_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="storage_details" class="form-label">Storage Details</label>
                                    <input type="text" class="form-control @error('storage_details') is-invalid @enderror" 
                                        id="storage_details" name="storage_details" value="{{ old('storage_details', $harvest->storage_details) }}">
                                    @error('storage_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes', $harvest->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('harvests.show', $harvest) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Harvest Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
