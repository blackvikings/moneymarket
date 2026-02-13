@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content_header')
    <h1>Create Permission</h1>
@stop

@section('content')
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST" id="createPermissionForm">
                @csrf
                <div class="form-group">
                    <label for="module">Module <span class="text-danger">*</span></label>
                    <select name="module" id="module" class="form-control @error('module') is-invalid @enderror" required>
                        <option value="">Select Module</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ old('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                        <option value="Other">Other</option>
                    </select>
                    @error('module') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="name">Permission Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save Permission</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createPermissionForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                rules: {
                    module: { required: true },
                    name: { required: true }
                },
                messages: {
                    module: "Please select a module",
                    name: "Please enter a permission name"
                }
            });
        });
    </script>
@stop
