@extends('layouts.MainLayout')

@section('title', 'Edit Loan')

@section('content')
<div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Loan</h1>
        <p class="mt-1 text-sm text-gray-500">Update repayment progress, payment records, linked activities, documents, and loan intelligence.</p>
    </div>
    <form method="POST" action="{{ route('loans.update', $loan) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('loans.partials.form')
        <div class="flex gap-2"><a href="{{ route('loans.index') }}" class="rounded-md border px-4 py-2 text-sm">Cancel</a><button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white">Update Loan</button></div>
    </form>
</div>
@endsection
