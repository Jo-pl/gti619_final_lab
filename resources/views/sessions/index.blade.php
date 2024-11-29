@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Active Sessions</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>IP Address</th>
                <th>Last Activity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->id }}</td>
                    <td>{{ $session->ip_address }}</td>
                    <td>{{ $session->last_activity }}</td>
                    <td>
                        <form method="POST" action="{{ route('sessions.destroy', $session->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Terminate</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
