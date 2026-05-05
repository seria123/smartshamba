@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $cycle->cycle_number }}</p><h1>{{ $cycle->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('crops.cycles.index') }}">Back</a><a class="button" href="{{ route('crops.cycles.edit',$cycle) }}">Edit</a><form method="POST" action="{{ route('crops.cycles.close',$cycle) }}">@csrf<button>Close</button></form><form method="POST" action="{{ route('crops.cycles.cancel',$cycle) }}">@csrf<button>Cancel</button></form></div></header>
    <dl class="detail-list"><div><dt>Crop</dt><dd>{{ $cycle->crop->name }} {{ $cycle->variety?->name }}</dd></div><div><dt>Location</dt><dd>{{ $cycle->farm->name }} / {{ $cycle->field->name }}</dd></div><div><dt>Season</dt><dd>{{ $cycle->season?->name ?? 'None' }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($cycle->status) }}</dd></div><div><dt>Area</dt><dd>{{ $cycle->area_planted }} {{ $cycle->area_unit }}</dd></div></dl>

    <h2>Activity timeline</h2>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Task</th><th>Notes</th></tr></thead><tbody>@forelse($cycle->activities as $activity)<tr><td>{{ $activity->activity_date?->format('Y-m-d') }}</td><td>{{ str_replace('_',' ',$activity->activity_type) }}</td><td>{{ $activity->task?->title ?? 'None' }}</td><td>{{ $activity->notes }}</td></tr>@empty<tr><td colspan="4">No activities recorded.</td></tr>@endforelse</tbody></table></div>
    <form class="form-panel" method="POST" action="{{ route('crops.cycles.activities.store',$cycle) }}">@csrf
        <h2>Record activity</h2>
        <div class="field-group"><label for="activity_type">Activity type</label><select id="activity_type" name="activity_type" required>@foreach(['land_preparation','planting','transplanting','fertilizer_application','spray_application','scouting','weeding','pruning','irrigation_note','harvest','loss','general_observation','other'] as $type)<option value="{{ $type }}">{{ str_replace('_',' ',$type) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="activity_date">Date</label><input id="activity_date" name="activity_date" type="date" value="{{ now()->toDateString() }}" required></div>
        <div class="field-group"><label for="task_id">Related task</label><select id="task_id" name="task_id"><option value="">No task</option>@foreach($tasks as $task)<option value="{{ $task->id }}">{{ $task->task_number }} {{ $task->title }}</option>@endforeach</select></div>
        <div class="field-group"><label for="worker_id">Worker</label><select id="worker_id" name="worker_id"><option value="">No worker</option>@foreach($workers as $worker)<option value="{{ $worker->id }}">{{ $worker->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="team_id">Team</label><select id="team_id" name="team_id"><option value="">No team</option>@foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="seed_quantity">Seed quantity</label><input id="seed_quantity" name="seed_quantity" type="number" min="0" step="0.01"></div>
        <div class="field-group"><label for="seed_unit">Seed unit</label><input id="seed_unit" name="seed_unit"></div>
        <div class="field-group"><label for="notes">Notes</label><textarea id="notes" name="notes"></textarea></div>
        <button type="submit">Record activity</button>
    </form>

    <h2>Scouting</h2>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Severity</th><th>Issue</th></tr></thead><tbody>@forelse($cycle->scoutingObservations as $record)<tr><td>{{ $record->observation_date?->format('Y-m-d') }}</td><td>{{ $record->observation_type }}</td><td>{{ $record->severity }}</td><td>{{ $record->pest_or_disease }}</td></tr>@empty<tr><td colspan="4">No scouting records.</td></tr>@endforelse</tbody></table></div>
    <form class="form-panel" method="POST" action="{{ route('crops.cycles.scouting.store',$cycle) }}">@csrf
        <h2>Record scouting</h2>
        <div class="field-group"><label for="observation_date">Date</label><input id="observation_date" name="observation_date" type="date" value="{{ now()->toDateString() }}" required></div>
        <div class="field-group"><label for="observation_type">Type</label><input id="observation_type" name="observation_type" required></div>
        <div class="field-group"><label for="severity">Severity</label><input id="severity" name="severity"></div>
        <div class="field-group"><label for="pest_or_disease">Pest or disease</label><input id="pest_or_disease" name="pest_or_disease"></div>
        <div class="field-group"><label for="recommendation">Recommendation</label><textarea id="recommendation" name="recommendation"></textarea></div>
        <button type="submit">Record scouting</button>
    </form>

    <h2>Treatments</h2>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Product</th><th>Quantity</th></tr></thead><tbody>@forelse($cycle->treatments as $record)<tr><td>{{ $record->application_date?->format('Y-m-d') }}</td><td>{{ $record->application_type }}</td><td>{{ $record->product_name_snapshot ?? $record->product?->name }}</td><td>{{ $record->quantity_used }} {{ $record->quantity_unit }}</td></tr>@empty<tr><td colspan="4">No treatment records.</td></tr>@endforelse</tbody></table></div>
    <form class="form-panel" method="POST" action="{{ route('crops.cycles.treatments.store',$cycle) }}">@csrf
        <h2>Record treatment</h2>
        <div class="field-group"><label for="application_type">Type</label><input id="application_type" name="application_type" required></div>
        <div class="field-group"><label for="application_date">Date</label><input id="application_date" name="application_date" type="date" value="{{ now()->toDateString() }}" required></div>
        <div class="field-group"><label for="product_id">Product</label><select id="product_id" name="product_id"><option value="">No product</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="quantity_used">Quantity used</label><input id="quantity_used" name="quantity_used" type="number" min="0" step="0.01"></div>
        <div class="field-group"><label for="quantity_unit">Unit</label><input id="quantity_unit" name="quantity_unit"></div>
        <button type="submit">Record treatment</button>
    </form>

    <h2>Harvests and losses</h2>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Harvest</th><th>Losses</th></tr></thead><tbody><tr><td>Totals</td><td>{{ $cycle->harvests->sum('quantity') }}</td><td>{{ $cycle->losses->count() }} records</td></tr></tbody></table></div>
    <form class="form-panel" method="POST" action="{{ route('crops.cycles.harvests.store',$cycle) }}">@csrf
        <h2>Record harvest</h2>
        <div class="field-group"><label for="harvest_date">Date</label><input id="harvest_date" name="harvest_date" type="date" value="{{ now()->toDateString() }}" required></div>
        <div class="field-group"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="0.01" step="0.01" required></div>
        <div class="field-group"><label for="unit">Unit</label><input id="unit" name="unit" required></div>
        <div class="field-group"><label for="destination">Destination</label><input id="destination" name="destination"></div>
        <button type="submit">Record harvest</button>
    </form>
    <form class="form-panel" method="POST" action="{{ route('crops.cycles.losses.store',$cycle) }}">@csrf
        <h2>Record loss</h2>
        <div class="field-group"><label for="loss_date">Date</label><input id="loss_date" name="loss_date" type="date" value="{{ now()->toDateString() }}" required></div>
        <div class="field-group"><label for="loss_type">Loss type</label><input id="loss_type" name="loss_type" required></div>
        <div class="field-group"><label for="estimated_quantity">Estimated quantity</label><input id="estimated_quantity" name="estimated_quantity" type="number" min="0" step="0.01"></div>
        <div class="field-group"><label for="cause">Cause</label><input id="cause" name="cause"></div>
        <button type="submit">Record loss</button>
    </form>
@endsection
