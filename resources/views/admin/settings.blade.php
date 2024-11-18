@extends('master')

@section('content')
    <div class="container">
        <h1>Admin Security Settings</h1>
        <form action="{{ route('admin.saveSettings') }}" method="POST">
            @csrf
            <label for="securityParam">Configure Security Parameter:</label>
            <input type="text" id="securityParam" name="securityParam" required>
            <button type="submit">Save</button>
        </form>
    </div>
@endsection
