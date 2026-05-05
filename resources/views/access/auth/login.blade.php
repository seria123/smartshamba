@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Access</p>
            <h1>Log in</h1>
        </div>
    </header>

    <form class="form-panel" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <label>
            <input name="remember" type="checkbox" value="1">
            Remember me
        </label>

        <button type="submit">Log in</button>
    </form>
@endsection
