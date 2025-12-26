@extends('adminlte::page')

@section('title', 'Create RTO Agent')

@section('content_header')
    <h1>Create RTO Agent</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rto-agents.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Agent Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="number">Agent Number</label>
                    <input type="text" name="number" id="number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cities">Cities</label>
                    <select name="cities[]" id="cities" class="form-control" multiple="multiple" required>
                    </select>
                    <small class="form-text text-muted">Type city name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#cities').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
    </script>
@stop
