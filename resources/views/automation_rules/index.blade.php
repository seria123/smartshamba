@extends('layouts.MainLayout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Automation Rules</h1>
        <a href="{{ route('automation_rules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Rule
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Field</th>
                        <th>Condition</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Last Triggered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                        <tr>
                            <td>{{ $rule->name }}</td>
                            <td>{{ $rule->field->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $rule->getSensorTypeNameAttribute() }}
                                    {{ $rule->operator }}
                                    {{ $rule->threshold }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning">
                                    {{ $rule->action_icon }}
                                    {{ $rule->getActionNameAttribute() }}
                                </span>
                            </td>
                            <td>
                                @if($rule->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($rule->last_triggered)
                                    {{ $rule->last_triggered->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('automation_rules.show', $rule->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('automation_rules.edit', $rule->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('automation_rules.toggle', $rule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-secondary' : 'btn-success' }}">
                                            <i class="fas {{ $rule->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('automation_rules.destroy', $rule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No automation rules found. Create one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
