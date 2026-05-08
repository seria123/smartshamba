@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Sales / Revenue</p><h1>Record payment</h1></div></header>
    <p>{{ $record->sale_number }} / Balance {{ $record->currency }} {{ number_format($record->balance_amount,2) }}</p>
    <form method="POST" action="{{ route('sales.payments.store',$record) }}">@csrf <div class="form-grid"><label>Date<input name="payment_date" type="date" value="{{ old('payment_date', now()->toDateString()) }}" required></label><label>Amount<input name="amount" type="number" step="0.01" min="0" max="{{ $record->balance_amount }}" value="{{ old('amount',$record->balance_amount) }}" required></label><label>Method<select name="method" required>@foreach(['cash','mpesa','bank_transfer','cheque','card','other'] as $method)<option value="{{ $method }}">{{ $method }}</option>@endforeach</select></label><label>Reference<input name="reference" value="{{ old('reference') }}"></label></div><label>Notes<textarea name="notes">{{ old('notes') }}</textarea></label>@include('sales::partials.errors')<button class="button" type="submit">Record payment</button></form>
@endsection
