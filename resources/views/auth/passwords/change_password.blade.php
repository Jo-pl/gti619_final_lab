@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Change Password') }}</div>

                <div class="card-body">
                    {{-- Display validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Display session error message --}}
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif

                    {{-- Display success message --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            <strong>{{ session('success') }}</strong>
                        </div>
                    @endif

                    {{-- Admin-specific form for changing any user's password --}}
                    @if (Auth::user()->role === 'admin')
                        <h5>{{ __('Admin: Change User Password') }}</h5>
                        <form method="POST" action="{{ route('admin.change.user.password') }}" class="mb-4">
                            @csrf

                            <div class="mb-3">
                                <label for="user_id" class="form-label">{{ __('Select User') }}</label>
                                <select id="user_id" name="user_id" class="form-control" required>
                                    @foreach (\App\Models\User::where('id', '!=', Auth::id())->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">{{ __('New Password') }}</label>
                                <input id="new_password" type="password" class="form-control" name="new_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                                <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger">
                                    {{ __('Change User Password') }}
                                </button>
                            </div>
                        </form>
                    @endif


                    {{-- Standard password change form for current user --}}
                    <h5>{{ __('Change Your Password') }}</h5>
                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input id="current_password" type="password" class="form-control" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">{{ __('New Password') }}</label>
                            <input id="new_password" type="password" class="form-control" name="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Change Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
