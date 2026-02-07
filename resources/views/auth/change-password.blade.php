@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', 'Change Password')

@section('auth_body')
    <p class="login-box-msg">You must change your password on first login.</p>

    <form action="{{ route('employee.update-password') }}" method="post">
        @csrf

        {{-- Current Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                   placeholder="Current Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('current_password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- New Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror"
                   placeholder="New Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('new_password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="new_password_confirmation" class="form-control"
                   placeholder="Retype New Password" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Change Password</button>
            </div>
        </div>
    </form>
@stop
