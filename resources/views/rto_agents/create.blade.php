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
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="number">Agent Number</label>
                    <input type="text" name="number" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}" required>
                    @error('number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="cities">Cities</label>
                    <select name="cities[]" id="cities" class="form-control @error('cities') is-invalid @enderror" multiple="multiple" required>
                        @foreach ($cities as $city)
                            <option value="{{ $city->name }}" {{ in_array($city->name, old('cities', [])) ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('cities') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Select cities from the list.</small>
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
                tags: false,
                tokenSeparators: [',', ' ']
            });
        });
    </script>
@stop
