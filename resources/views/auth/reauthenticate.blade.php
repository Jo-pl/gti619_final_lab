@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reauthenticate</h2>
    <p>Please confirm your password to proceed.</p>

    <form method="POST" action="{{ route('reauthenticate') }}">
        @csrf

        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                required
                autocomplete="current-password"
            >
            @error('password')
                <span class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Confirm</button>
    </form>
</div>
@endsection
