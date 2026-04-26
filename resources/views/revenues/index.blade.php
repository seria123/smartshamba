@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Revenue / Sales</h4>
                    <a href="{{ route('revenues.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Record Sale
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }}</h3>
                                    <small>Total Revenue</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['paid'], 2) }}</h3>
                                    <small>Paid</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['pending'], 2) }}</h3>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Farm</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenues as $revenue)
                                <tr>
                                    <td>{{ $revenue->sale_date->format('M d, Y') }}</td>
                                    <td>{{ $revenue->buyer?->name ?? 'Direct Sale' }}</td>
                                    <td>{{ $revenue->farm?->name }}</td>
                                    <td class="text-success fw-bold">{{ number_format($revenue->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $revenue->status_color }}">{{ ucfirst($revenue->payment_status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('revenues.show', $revenue) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No revenue recorded</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $revenues->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection