@extends('layouts.MainLayout')

@section('title', 'New Loan')

@section('content')
<div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">New Loan</h1>
        <p class="mt-1 text-sm text-gray-500">Create a loan profile with terms, repayment schedule, activity links, risk notes, documents, and ROI tracking.</p>
    </div>
    <form method="POST" action="{{ route('loans.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('loans.partials.form')
        <div class="flex gap-2"><a href="{{ route('loans.index') }}" class="rounded-md border px-4 py-2 text-sm">Cancel</a><button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Save Loan</button></div>
    </form>
</div>
@endsection
