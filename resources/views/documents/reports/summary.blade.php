@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Documents summary</h1></div></header>
    @include('documents::partials-filters')
    @include('documents::partials-nav')
    <section class="summary-grid"><article class="summary-card"><span>Total attachments</span><strong>{{ $totalAttachments }}</strong></article><article class="summary-card"><span>Total storage used</span><strong>{{ number_format($totalStorage / 1024, 1) }} KB</strong></article><article class="summary-card"><span>Uploads this month</span><strong>{{ $uploadsThisMonth }}</strong></article></section>
@endsection
