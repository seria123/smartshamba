@extends('layouts.MainLayout')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>{{ $automationRule->name }}</h3>
            <div>
                @if($automationRule->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Rule Details</h5>
                    <table class="table">
                        <tr>
                            <th>Field:</th>
                            <td>{{ $automationRule->field->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sensor Type:</th>
                            <td>{{ $automationRule->getSensorTypeNameAttribute() }}</td>
                        </tr>
                        <tr>
                            <th>Condition:</th>
                            <td>
                                <span class="badge bg-info">
                                    {{ $automationRule->sensor_type }}
                                    {{ $automationRule->operator }}
                                    {{ $automationRule->threshold }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Action:</th>
                            <td>
                                <span class="badge bg-warning">
                                    {{ $automationRule->action_icon }}
                                    {{ $automationRule->getActionNameAttribute() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Cooldown:</th>
                            <td>{{ $automationRule->cooldown_minutes }} minutes</td>
                        </tr>
                        <tr>
                            <th>Last Triggered:</th>
                            <td>
                                @if($automationRule->last_triggered)
                                    {{ $automationRule->last_triggered->format('Y-m-d H:i:s') }}
                                    ({{ $automationRule->last_triggered->diffForHumans() }})
                                @else
                                    Never
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Quick Test</h5>
                    <p>Test this rule with a specific value:</p>
                    <form action="{{ route('automation_rules.trigger', $automationRule->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="number" step="0.01" name="test_value" class="form-control" placeholder="Enter test value" required>
                            <button type="submit" class="btn btn-primary">Test</button>
                        </div>
                    </form>

                    <h5>Actions</h5>
                    <div class="btn-group-vertical w-100">
                        <a href="{{ route('automation_rules.edit', $automationRule->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Rule
                        </a>
                        <form action="{{ route('automation_rules.toggle', $automationRule->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $automationRule->is_active ? 'btn-secondary' : 'btn-success' }} w-100">
                                <i class="fas {{ $automationRule->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                {{ $automationRule->is_active ? 'Disable Rule' : 'Enable Rule' }}
                            </button>
                        </form>
                        <form action="{{ route('automation_rules.destroy', $automationRule->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete Rule
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('automation_rules.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Rules
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
