@extends('adminlte::page')

@section('title', 'Edit Vehicle Manufacture')

@section('content_header')
    <h1>Edit Vehicle Manufacture</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vehicle-manufactures.update', $vehicleManufacture->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Manufacture Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $vehicleManufacture->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="vehicle_names">Vehicle Names</label>
                    <select name="vehicle_names[]" id="vehicle_names" class="form-control @error('vehicle_names') is-invalid @enderror" multiple="multiple" required>
                        @foreach (old('vehicle_names', $vehicleManufacture->vehicleNames->pluck('name')->toArray()) as $vehicleName)
                            <option value="{{ $vehicleName }}" selected>{{ $vehicleName }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_names') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Type vehicle name and press enter to add.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
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
