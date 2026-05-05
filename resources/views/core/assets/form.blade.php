@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">{{ $label }}</p>
            <h1>{{ $item ? 'Edit '.$label : 'New '.$label }}</h1>
        </div>
        <a class="button secondary" href="{{ route($routeBase.'.index') }}">Back</a>
    </header>

    <form class="form-panel" method="POST" action="{{ $item ? route($routeBase.'.update', $item) : route($routeBase.'.store') }}">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="field-group">
            <label for="organization_id">Organization</label>
            <select id="organization_id" name="organization_id" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) old('organization_id', $item?->organization_id) === (string) $organization->id)>
                        {{ $organization->name }}
                    </option>
                @endforeach
            </select>
            @error('organization_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="farm_id">Farm</label>
            <select id="farm_id" name="farm_id" required>
                <option value="">Select farm</option>
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((string) old('farm_id', $item?->farm_id) === (string) $farm->id)>
                        {{ $farm->name }} - {{ $farm->organization->name }}
                    </option>
                @endforeach
            </select>
            @error('farm_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        @if ($allowsSite)
            <div class="field-group">
                <label for="site_id">Site</label>
                <select id="site_id" name="site_id">
                    <option value="">No site</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" @selected((string) old('site_id', $item?->site_id) === (string) $site->id)>
                            {{ $site->name }} - {{ $site->farm->name }}
                        </option>
                    @endforeach
                </select>
                @error('site_id') <span class="error">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="field-group">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $item?->name) }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $item?->code) }}">
            @error('code') <span class="error">{{ $message }}</span> @enderror
        </div>

        @if ($allowsArea)
            <div class="field-group">
                <label for="area">Area</label>
                <input id="area" name="area" type="number" min="0" step="0.01" value="{{ old('area', $item?->area) }}">
                @error('area') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="area_unit">Area unit</label>
                <select id="area_unit" name="area_unit" required>
                    @foreach (['acres', 'hectares', 'sqm'] as $unit)
                        <option value="{{ $unit }}" @selected(old('area_unit', $item?->area_unit ?? 'acres') === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
                @error('area_unit') <span class="error">{{ $message }}</span> @enderror
            </div>
        @endif

        @if ($allowsDescription)
            <div class="field-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $item?->description) }}</textarea>
                @error('description') <span class="error">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="field-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $item?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save {{ strtolower($label) }}</button>
    </form>
@endsection
