@extends('adminlte::page')

@section('title', 'Edit Bank')

@section('content_header')
    <h1>Edit Bank</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('banks.update', $bank->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bank->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Branches</h5>
                <div id="branches-container">
                    @forelse($bank->branches as $branch)
                        <div class="row branch-row mb-2">
                            <div class="col-md-10">
                                <input type="text" name="branches[]" class="form-control" placeholder="Branch Name" value="{{ $branch->name }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-branch">X</button>
                            </div>
                        </div>
                    @empty
                        <div class="row branch-row mb-2">
                            <div class="col-md-10">
                                <input type="text" name="branches[]" class="form-control" placeholder="Branch Name">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-branch">X</button>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-branch">Add Branch</button>

                <br><br>
                <button type="submit" class="btn btn-primary">Update Bank</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
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
