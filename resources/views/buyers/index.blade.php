@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Buyers</h4>
                    <a href="{{ route('buyers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Buyer
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buyers as $buyer)
                                <tr>
                                    <td>{{ $buyer->name }}</td>
                                    <td>{{ $buyer->company_name ?? '-' }}</td>
                                    <td>{{ $buyer->phone }}</td>
                                    <td>{{ ucfirst($buyer->buyer_type) }}</td>
                                    <td>{{ $buyer->rating ? $buyer->rating . '/5' : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $buyer->is_active ? 'success' : 'secondary' }}">
                                            {{ $buyer->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('buyers.show', $buyer) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No buyers added</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $buyers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection