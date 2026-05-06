@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Water reading</p><h1>New reading</h1></div><a class="button secondary" href="{{ route('irrigation.readings.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ route('irrigation.readings.store') }}">@csrf
        @include('irrigation::partials.scope-fields', ['record' => null])
        <div class="field-group"><label for="water_source_id">Water source</label><select id="water_source_id" name="water_source_id"><option value="">None</option>@foreach($sources as $source)<option value="{{ $source->id }}" @selected((string)old('water_source_id')===(string)$source->id)>{{ $source->name }}</option>@endforeach</select>@error('water_source_id')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field-group"><label for="irrigation_zone_id">Zone</label><select id="irrigation_zone_id" name="irrigation_zone_id"><option value="">None</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected((string)old('irrigation_zone_id')===(string)$zone->id)>{{ $zone->name }}</option>@endforeach</select>@error('irrigation_zone_id')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field-group"><label for="reading_date">Date</label><input id="reading_date" name="reading_date" type="date" value="{{ old('reading_date') }}" required></div>
        <div class="field-group"><label for="reading_type">Type</label><select id="reading_type" name="reading_type">@foreach(['meter_reading','tank_level','flow_rate','pressure','soil_moisture','rainfall','manual_observation','other'] as $value)<option value="{{ $value }}" @selected(old('reading_type','meter_reading')===$value)>{{ ucfirst(str_replace('_',' ',$value)) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="value">Value</label><input id="value" name="value" type="number" step="0.01" required>@error('value')<span class="error">{{ $message }}</span>@enderror</div>
        <div class="field-group"><label for="unit_of_measure">Unit</label><input id="unit_of_measure" name="unit_of_measure" required></div>
        <div class="field-group"><label for="recorded_by_worker_id">Recorded by worker</label><select id="recorded_by_worker_id" name="recorded_by_worker_id"><option value="">None</option>@foreach($workers as $worker)<option value="{{ $worker->id }}">{{ $worker->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes') }}</textarea></div><button>Save reading</button>
    </form>
@endsection
