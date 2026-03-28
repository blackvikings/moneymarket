@extends('adminlte::page')

@section('title', 'Edit Insurance')

@section('content_header')
    <h1>Edit Insurance</h1>
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
        .invalid-feedback {
            display: block;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix(). 'insurances.update', $insurance->id) }}" method="POST" id="editInsuranceForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $insurance->company_name) }}" required>
                            @error('company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="agent_name">Agent Name <span class="text-danger">*</span></label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name', $insurance->agent_name) }}" required>
                            @error('agent_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vehicle_type">Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror" required>
                                <option value="2 Wheeler" {{ (old('vehicle_type', $insurance->vehicle_type) == '2 Wheeler') ? 'selected' : '' }}>2 Wheeler</option>
                                <option value="3 Wheeler" {{ (old('vehicle_type', $insurance->vehicle_type) == '3 Wheeler') ? 'selected' : '' }}>3 Wheeler</option>
                                <option value="4 Wheeler" {{ (old('vehicle_type', $insurance->vehicle_type) == '4 Wheeler') ? 'selected' : '' }}>4 Wheeler</option>
                                <option value="Commercial" {{ (old('vehicle_type', $insurance->vehicle_type) == 'Commercial') ? 'selected' : '' }}>Commercial</option>
                            </select>
                            @error('vehicle_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Insurance Types</h4>
                <div id="types-container">
                    @php
                        $types = old('types', $insurance->types->map(function($type) {
                            return [
                                'name' => $type->name
                            ];
                        })->toArray());
                        if (empty($types)) {
                            $types = [['name' => '']];
                        }
                    @endphp
                    @foreach ($types as $index => $type)
                        <div class="card type-card mt-3" data-index="{{ $index }}">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Insurance Type</label>
                                    <input type="text" name="types[{{ $index }}][name]" class="form-control @error('types.'.$index.'.name') is-invalid @enderror" value="{{ $type['name'] }}" required>
                                    @error('types.'.$index.'.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-type">Remove Type</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-type">Add Insurance Type</button>

                <hr>
                <button type="submit" class="btn btn-primary">Save Insurance</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#editInsuranceForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                rules: {
                    company_name: { required: true },
                    agent_name: { required: true },
                    vehicle_type: { required: true }
                },
                messages: {
                    company_name: "Please enter the company name",
                    agent_name: "Please enter the agent name",
                    vehicle_type: "Please select a vehicle type"
                }
            });

            let typeIndex = {{ count(old('types', $insurance->types->toArray())) -1 }};
            if (typeIndex < 0) typeIndex = 0;

            // Add validation rules for existing rows
            $('.type-card').each(function() {
                let index = $(this).data('index');
                $(`[name="types[${index}][name]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: "Insurance type name is required"
                    }
                });
            });

            $('#add-type').click(function() {
                typeIndex++;
                let html = `
                    <div class="card type-card mt-3" data-index="${typeIndex}">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Insurance Type</label>
                                <input type="text" name="types[${typeIndex}][name]" class="form-control" required>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-type">Remove Type</button>
                        </div>
                    </div>
                `;
                $('#types-container').append(html);

                // Add validation for the new row
                $(`[name="types[${typeIndex}][name]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: "Insurance type name is required"
                    }
                });
            });

            $(document).on('click', '.remove-type', function() {
                $(this).closest('.type-card').remove();
            });
        });
    </script>
@stop
