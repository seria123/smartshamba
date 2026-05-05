@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Supplier</p><h1>{{ $supplier ? 'Edit supplier' : 'New supplier' }}</h1></div><a class="button secondary" href="{{ route('inventory.suppliers.index') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ $supplier ? route('inventory.suppliers.update',$supplier) : route('inventory.suppliers.store') }}">@csrf @if($supplier) @method('PUT') @endif
        @include('inventory::partials.organization-field', ['record'=>$supplier])
        @foreach(['name'=>'Name','code'=>'Code','contact_name'=>'Contact name','phone'=>'Phone','email'=>'Email','address'=>'Address','supplier_type'=>'Supplier type'] as $field=>$label)
            <div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" @if($field==='email') type="email" @endif value="{{ old($field,$supplier?->$field ?? ($field==='supplier_type' ? 'general' : null)) }}" @if(in_array($field,['name','supplier_type'])) required @endif>@error($field) <span class="error">{{ $message }}</span> @enderror</div>
        @endforeach
        <div class="field-group"><label for="status">Status</label><select id="status" name="status" required>@foreach(['active','inactive'] as $status)<option value="{{ $status }}" @selected(old('status',$supplier?->status ?? 'active')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="field-group"><label for="notes">Notes</label><textarea id="notes" name="notes">{{ old('notes',$supplier?->notes) }}</textarea></div>
        <button type="submit">Save supplier</button>
    </form>
@endsection
