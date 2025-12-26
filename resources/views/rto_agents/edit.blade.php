@extends('adminlte::page')

@section('title', 'Edit RTO Agent')

@section('content_header')
    <h1>Edit RTO Agent</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rto-agents.update', $rtoAgent->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Agent Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $rtoAgent->name }}" required>
                </div>
                <div class="form-group">
                    <label for="number">Agent Number</label>
                    <input type="text" name="number" id="number" class="form-control" value="{{ $rtoAgent->number }}" required>
                </div>
                <div class="form-group">
                    <label for="cities">Cities</label>
                    <select name="cities[]" id="cities" class="form-control" multiple="multiple" required>
                        @foreach ($rtoAgent->cities as $city)
                            <option value="{{ $city->city_name }}" selected>{{ $city->city_name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Type city name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
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
