@extends('adminlte::page')

@section('title', 'Create Bank')

@section('content_header')
    <h1>Create Bank</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('banks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Bank Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="branches">Branches</label>
                    <select name="branches[]" id="branches" class="form-control" multiple="multiple" required>
                    </select>
                    <small class="form-text text-muted">Type branch name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
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
