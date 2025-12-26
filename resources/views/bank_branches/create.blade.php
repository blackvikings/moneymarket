@extends('adminlte::page')

@section('title', 'Create Bank Branch')

@section('content_header')
    <h1>Create Bank Branch</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bank-branches.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="bank_id">Bank</label>
                    <select name="bank_id" id="bank_id" class="form-control @error('bank_id') is-invalid @enderror" required>
                        <option value="">Select a Bank</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                    @error('bank_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Branches</h4>
                <div id="branches-container">
                    @if(old('branches'))
                        @foreach(old('branches') as $branch)
                            <div class="input-group mb-2">
                                <input type="text" name="branches[]" class="form-control @error('branches.*') is-invalid @enderror" placeholder="Branch Name" value="{{ $branch }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-danger remove-branch" type="button">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2">
                            <input type="text" name="branches[]" class="form-control" placeholder="Branch Name" required>
                            <div class="input-group-append">
                                <button class="btn btn-danger remove-branch" type="button">Remove</button>
                            </div>
                        </div>
                    @endif
                    @error('branches.*') <span class="text-danger d-block">{{ $message }}</span> @enderror
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-branch">Add More</button>

                <hr>
                <button type="submit" class="btn btn-primary">Create Branches</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#add-branch').click(function() {
                let html = `
                    <div class="input-group mb-2">
                        <input type="text" name="branches[]" class="form-control" placeholder="Branch Name" required>
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-branch" type="button">Remove</button>
                        </div>
                    </div>
                `;
                $('#branches-container').append(html);
            });

            $(document).on('click', '.remove-branch', function() {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
@stop
