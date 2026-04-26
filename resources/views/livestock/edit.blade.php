@extends('layouts.MainLayout')

@section('title', 'Edit Livestock - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Livestock</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('livestock.update', $livestock) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name (Optional)</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $livestock->name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="livestock_type_id" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="livestock_type_id" name="livestock_type_id" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ old('livestock_type_id', $livestock->livestock_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tag_number" class="form-label">Tag Number</label>
                                    <input type="text" class="form-control" id="tag_number" name="tag_number"
                                           value="{{ old('tag_number', $livestock->tag_number) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $livestock->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $livestock->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_acquired" class="form-label">Date Acquired</label>
                                    <input type="date" class="form-control" id="date_acquired" name="date_acquired"
                                           value="{{ old('date_acquired', $livestock->date_acquired?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                                           value="{{ old('birth_date', $livestock->birth_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" step="0.01" class="form-control" id="weight" name="weight"
                                           value="{{ old('weight', $livestock->weight) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="healthy" {{ old('status', $livestock->status) == 'healthy' ? 'selected' : '' }}>Healthy</option>
                                        <option value="sick" {{ old('status', $livestock->status) == 'sick' ? 'selected' : '' }}>Sick</option>
                                        <option value="sold" {{ old('status', $livestock->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="dead" {{ old('status', $livestock->status) == 'dead' ? 'selected' : '' }}>Dead</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purchase_price" class="form-label">Purchase Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price"
                                           value="{{ old('purchase_price', $livestock->purchase_price) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price"
                                           value="{{ old('sale_price', $livestock->sale_price) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $livestock->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('livestock.show', $livestock) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Livestock</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection