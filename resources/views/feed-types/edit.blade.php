@extends('layouts.MainLayout')

@section('title', 'Edit Food Type - SmartShamba')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit {{ $feedType->name }}</h1>
            <p class="text-sm text-gray-500">Update food/feed pricing, source, production, storage, quality, and notes.</p>
        </div>
        <a href="{{ route('feed-types.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-2"></i>Back</a>
    </div>

    <form action="{{ route('feed-types.update', $feedType) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')
        @include('feed-types.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('feed-types.show', $feedType) }}" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50">Cancel</a>
            <button class="rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700"><i class="fas fa-save mr-2"></i>Update Food Type</button>
        </div>
    </form>
</div>
@endsection
