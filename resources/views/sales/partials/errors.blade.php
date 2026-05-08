@if ($errors->any())
    <div class="alert"><strong>Please fix:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
