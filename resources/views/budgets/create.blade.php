@extends('layouts.MainLayout')

@section('title', 'New Budget')

@section('content')
<div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">New Budget</h1>
        <p class="mt-1 text-sm text-gray-500">Create seasonal, crop, livestock, or project budgets with category allocations, alerts, approvals, forecasts, and proof documents.</p>
    </div>
    <form method="POST" action="{{ route('budgets.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('budgets.partials.form')
        <div class="flex gap-2"><a href="{{ route('budgets.index') }}" class="rounded-md border px-4 py-2 text-sm">Cancel</a><button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Save Budget</button></div>
    </form>
</div>
@endsection
