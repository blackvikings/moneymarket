@extends('adminlte::page')

@section('title', 'Create Vehicle Manufacture')

@section('content_header')
    <h1>Create Vehicle Manufacture</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vehicle-manufactures.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Manufacture Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="vehicle_names">Vehicle Names</label>
                    <select name="vehicle_names[]" id="vehicle_names" class="form-control @error('vehicle_names') is-invalid @enderror" multiple="multiple" required>
                        @if(old('vehicle_names'))
                            @foreach(old('vehicle_names') as $vehicleName)
                                <option value="{{ $vehicleName }}" selected>{{ $vehicleName }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('vehicle_names') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Type vehicle name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#vehicle_names').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
    </script>
@stop
