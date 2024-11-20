@extends('master')

@section('content')
    <div class="container">
        <h1>Business Clients</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $client->first_name }}</td>
                        <td>{{ $client->last_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
