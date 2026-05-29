@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Create Feed Usage</h4>
                </div>

                <div class="card-body">

                    {{-- Success/Error Messages --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('feed_usages.store') }}" method="POST">
                        @csrf

                        {{-- Feed Type --}}
                        <div class="mb-3">
                            <label class="form-label">Feed Type</label>
                            <input type="text"
                                   name="feed_type"
                                   class="form-control"
                                   value="{{ old('feed_type') }}"
                                   placeholder="e.g. Dairy Meal, Growers Mash"
                                   required>
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label">Quantity (kg)</label>
                            <input type="number"
                                   step="0.01"
                                   name="quantity"
                                   class="form-control"
                                   value="{{ old('quantity') }}"
                                   placeholder="e.g. 25"
                                   required>
                        </div>

                        {{-- Animal Type --}}
                        <div class="mb-3">
                            <label class="form-label">Animal Type</label>
                            <select name="animal_type" class="form-control" required>
                                <option value="">-- Select Animal --</option>
                                <option value="cattle">Cattle</option>
                                <option value="goats">Goats</option>
                                <option value="sheep">Sheep</option>
                                <option value="poultry">Poultry</option>
                                <option value="pigs">Pigs</option>
                            </select>
                        </div>

                        {{-- Usage Date --}}
                        <div class="mb-3">
                            <label class="form-label">Usage Date</label>
                            <input type="date"
                                   name="usage_date"
                                   class="form-control"
                                   value="{{ old('usage_date', date('Y-m-d')) }}"
                                   required>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Any additional details...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('feed_usages.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-success">
                                Save Feed Usage
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection