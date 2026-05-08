@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Notifications / Alerts</p><h1>Create notification rule</h1></div></header>
    <form method="POST" action="{{ route('notifications.rules.store') }}">
        @include('notifications::rules.form')
    </form>
@endsection
