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
                    <label for="name">Bank Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $bank->name }}" required>
                </div>
                <div class="form-group">
                    <label for="branches">Branches</label>
                    <select name="branches[]" id="branches" class="form-control" multiple="multiple" required>
                        @foreach ($bank->branches as $branch)
                            <option value="{{ $branch->name }}" selected>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Type branch name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#branches').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
    </script>
@stop
