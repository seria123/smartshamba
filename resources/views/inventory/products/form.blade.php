@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Product</p><h1>{{ $product ? 'Edit product' : 'New product' }}</h1></div><a class="button secondary" href="{{ route('inventory.products.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $product ? route('inventory.products.update',$product) : route('inventory.products.store') }}">@csrf @if($product) @method('PUT') @endif
        @include('inventory::partials.organization-field', ['record'=>$product])
        <div class="field-group"><label for="category_id">Category</label><select id="category_id" name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('category_id',$product?->category_id)===(string)$category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id') <span class="error">{{ $message }}</span> @enderror</div>
        @foreach(['name'=>'Name','code'=>'Code','sku'=>'SKU','product_type'=>'Product type','unit_of_measure'=>'Unit of measure','brand'=>'Brand','manufacturer'=>'Manufacturer','active_ingredient'=>'Active ingredient'] as $field=>$label)
            <div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $product?->$field) }}" @if(in_array($field,['name','code','product_type','unit_of_measure'])) required @endif>@error($field) <span class="error">{{ $message }}</span> @enderror</div>
        @endforeach
        <div class="field-group"><label><input type="checkbox" name="tracks_batch" value="1" @checked(old('tracks_batch',$product?->tracks_batch))> Track batches</label></div>
        <div class="field-group"><label><input type="checkbox" name="tracks_expiry" value="1" @checked(old('tracks_expiry',$product?->tracks_expiry))> Track expiry</label></div>
        <div class="field-group"><label for="reorder_level">Reorder level</label><input id="reorder_level" name="reorder_level" type="number" min="0" step="0.01" value="{{ old('reorder_level',$product?->reorder_level ?? 0) }}"></div>
        <div class="field-group"><label for="default_unit_cost">Default unit cost</label><input id="default_unit_cost" name="default_unit_cost" type="number" min="0" step="0.01" value="{{ old('default_unit_cost',$product?->default_unit_cost ?? 0) }}"></div>
        <div class="field-group"><label for="status">Status</label><select id="status" name="status" required>@foreach(['active','inactive'] as $status)<option value="{{ $status }}" @selected(old('status',$product?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button type="submit">Save product</button>
    </form>
@endsection
