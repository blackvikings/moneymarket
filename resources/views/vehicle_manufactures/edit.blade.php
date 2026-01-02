@extends('adminlte::page')

@section('title', 'Edit Vehicle Manufacture')

@section('content_header')
    <h1>Edit Vehicle Manufacture</h1>
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
        .select2-container--default .select2-selection--multiple.error {
            border: 1px solid red;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.update', $vehicleManufacture->id) }}" method="POST" id="editVehicleManufactureForm">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#vehicle_names').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });

            $('#editVehicleManufactureForm').validate({
                ignore: [], // To validate hidden select2 fields
                rules: {
                    name: { required: true },
                    'vehicle_names[]': { required: true }
                },
                messages: {
                    name: "Please enter the manufacture name",
                    'vehicle_names[]': "Please add at least one vehicle name"
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@stop
