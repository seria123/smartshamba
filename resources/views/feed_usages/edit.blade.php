@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Edit Feed Usage</h4>
                </div>

                <div class="card-body">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('feed_usages.update', $feedUsage->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Feed Type --}}
                        <div class="mb-3">
                            <label class="form-label">Feed Type</label>
                            <input type="text"
                                   name="feed_type"
                                   class="form-control"
                                   value="{{ old('feed_type', $feedUsage->feed_type) }}"
                                   required>
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label">Quantity (kg)</label>
                            <input type="number"
                                   step="0.01"
                                   name="quantity"
                                   class="form-control"
                                   value="{{ old('quantity', $feedUsage->quantity) }}"
                                   required>
                        </div>

                        {{-- Animal Type --}}
                        <div class="mb-3">
                            <label class="form-label">Animal Type</label>
                            <select name="animal_type" class="form-control" required>
                                <option value="cattle" {{ $feedUsage->animal_type == 'cattle' ? 'selected' : '' }}>Cattle</option>
                                <option value="goats" {{ $feedUsage->animal_type == 'goats' ? 'selected' : '' }}>Goats</option>
                                <option value="sheep" {{ $feedUsage->animal_type == 'sheep' ? 'selected' : '' }}>Sheep</option>
                                <option value="poultry" {{ $feedUsage->animal_type == 'poultry' ? 'selected' : '' }}>Poultry</option>
                                <option value="pigs" {{ $feedUsage->animal_type == 'pigs' ? 'selected' : '' }}>Pigs</option>
                            </select>
                        </div>

                        {{-- Usage Date --}}
                        <div class="mb-3">
                            <label class="form-label">Usage Date</label>
                            <input type="date"
                                   name="usage_date"
                                   class="form-control"
                                   value="{{ old('usage_date', $feedUsage->usage_date) }}"
                                   required>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="3">{{ old('notes', $feedUsage->notes) }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('feed_usages.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-warning">
                                Update Feed Usage
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection