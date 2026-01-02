@extends('adminlte::page')

@section('title', 'Create Bank')

@section('content_header')
    <h1>Create Bank</h1>
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
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.banks.store') }}" method="POST" id="createBankForm">
                @csrf
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Branches</h5>
                <div id="branches-container">
                    <div class="row branch-row mb-2">
                        <div class="col-md-10">
                            <input type="text" name="branches[]" class="form-control" placeholder="Branch Name">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-branch">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-branch">Add Branch</button>

                <br><br>
                <button type="submit" class="btn btn-primary">Create Bank</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createBankForm').validate();

            $('#add-branch').click(function() {
                let html = `
                    <div class="row branch-row mb-2">
                        <div class="col-md-10">
                            <input type="text" name="branches[]" class="form-control" placeholder="Branch Name">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-branch">X</button>
                        </div>
                    </div>
                `;
                $('#branches-container').append(html);
            });

            $(document).on('click', '.remove-branch', function() {
                $(this).closest('.branch-row').remove();
            });
        });
    </script>
@stop
