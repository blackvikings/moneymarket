@extends('adminlte::auth.login')

@section('auth_header', 'Employee Login')

@section('auth_body')
    <form action="{{ route('employee.login') }}" method="post">
        @csrf

        {{-- Employee ID field --}}
        <div class="input-group mb-3">
            <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                   value="{{ old('employee_id') }}" placeholder="Employee ID" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-id-card {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('employee_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" id="employee-password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary border-left-0" id="toggle-employee-password"
                        aria-label="Show password" aria-pressed="false">
                    <span class="fas fa-eye"></span>
                </button>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember', true) ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>

    </form>
@stop

@section('js')
    @parent
    <script>
        (function () {
            const passwordInput = document.getElementById('employee-password');
            const toggleButton = document.getElementById('toggle-employee-password');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(isPassword));
                toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                toggleButton.querySelector('span').className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        })();
    </script>
@stop
