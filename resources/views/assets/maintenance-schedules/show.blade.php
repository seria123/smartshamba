@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Maintenance schedule</p><h1>{{ $schedule->schedule_number }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('assets.maintenance-schedules.edit',$schedule) }}">Edit</a><form method="POST" action="{{ route('assets.maintenance-schedules.cancel',$schedule) }}">@csrf<button class="secondary">Cancel</button></form><form method="POST" action="{{ route('assets.maintenance-schedules.complete',$schedule) }}">@csrf<button>Complete</button></form></div></header>
    <section class="detail-grid"><article><strong>Asset</strong><p>{{ $schedule->asset?->name }}</p></article><article><strong>Date</strong><p>{{ $schedule->scheduled_date?->toDateString() }}</p></article><article><strong>Priority</strong><p>{{ $schedule->priority }}</p></article><article><strong>Status</strong><p>{{ $schedule->status }}</p></article></section><p>{{ $schedule->instructions }}</p>
@endsection
