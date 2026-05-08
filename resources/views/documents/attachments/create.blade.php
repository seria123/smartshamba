@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Upload attachment</h1></div></header>
    <form method="POST" action="{{ route('documents.attachments.store') }}" enctype="multipart/form-data">@include('documents::attachments.form')</form>
@endsection
