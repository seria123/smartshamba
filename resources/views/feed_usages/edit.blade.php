@extends('layouts.MainLayout')

@section('title', 'Edit Feed Usage - SmartShamba')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Feed Usage</h1>
            <p class="text-sm text-gray-500">Update feeding details. Inventory will not be deducted twice if already deducted.</p>
        </div>
        <a href="{{ route('feed_usages.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-2"></i>Back</a>
    </div>

    <form action="{{ route('feed_usages.update', $feedUsage) }}" method="POST" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-6">
        @csrf
        @method('PUT')
        @include('feed_usages.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('feed_usages.show', $feedUsage) }}" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700"><i class="fas fa-save mr-2"></i>Update Feeding</button>
        </div>
    </form>
</div>
@endsection
