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
        .select2-container--default .select2-selection--multiple.error {
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $insurance->company_name) }}" required>
                            @error('company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_name">Agent Name <span class="text-danger">*</span></label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name', $insurance->agent_name) }}" required>
                            @error('agent_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="form-group">
                    <label>Address Line <span class="text-danger">*</span></label>
                    <input type="text" name="address_line" id="address_line" class="form-control @error('address_line') is-invalid @enderror" value="{{ old('address_line', $insurance->address_line) }}" required>
                    @error('address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pin Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" id="pincode" class="form-control pincode-input @error('pincode') is-invalid @enderror" value="{{ old('pincode', $insurance->pincode) }}" required>
                            @error('pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>State <span class="text-danger">*</span></label>
                            <select name="state_id" id="state_id" class="form-control state-select @error('state_id') is-invalid @enderror" required>
                                <option value="">Select State</option>
                            </select>
                            @error('state_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>City <span class="text-danger">*</span></label>
                            <select name="city_id" id="city_id" class="form-control city-select @error('city_id') is-invalid @enderror" required>
                                <option value="">Select State First</option>
                            </select>
                            @error('city_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Insurance Types & Sub Types</h4>
                <div id="types-container">
                    @php
                        $types = old('types', $insurance->types->map(function($type) {
                            return [
                                'name' => $type->name,
                                'sub_types' => $type->subTypes->pluck('name')->toArray()
                            ];
                        })->toArray());
                        if (empty($types)) {
                            $types = [['name' => '', 'sub_types' => []]];
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
                                <div class="form-group">
                                    <label>Sub Types</label>
                                    <select name="types[{{ $index }}][sub_types][]" class="form-control sub-types-select" multiple="multiple">
                                        @if(isset($type['sub_types']))
                                            @foreach($type['sub_types'] as $subType)
                                                <option value="{{ $subType }}" selected>{{ $subType }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">Type sub-type name and press enter to add.</small>
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
                }
            });

            let typeIndex = {{ count(old('types', $insurance->types->toArray())) -1 }};
            if (typeIndex < 0) typeIndex = 0;

            function initSelect2(element) {
                element.select2({
                    tags: true,
                    tokenSeparators: [','] // Removed space to allow spaces in strings
                });
            }

            initSelect2($('.sub-types-select'));

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
                            <div class="form-group">
                                <label>Sub Types</label>
                                <select name="types[${typeIndex}][sub_types][]" class="form-control sub-types-select" multiple="multiple">
                                </select>
                                <small class="form-text text-muted">Type sub-type name and press enter to add. Spaces are allowed.</small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-type">Remove Type</button>
                        </div>
                    </div>
                `;
                $('#types-container').append(html);
                initSelect2($('#types-container .type-card:last .sub-types-select'));

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

            // --- Location Scripts ---
            function populateStates(selector, selectedStateId = null) {
                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        let stateSelects = $(selector);
                        stateSelects.empty().append('<option value="">Select State</option>');
                        $.each(data, function(key, state) {
                            let selected = (selectedStateId == state.id) ? 'selected' : '';
                            stateSelects.append('<option value="' + state.id + '" ' + selected + '>' + state.name + '</option>');
                        });
                        if (selectedStateId) {
                            populateCities('#state_id', '#city_id', null, {{ $insurance->city_id ?? 'null' }});
                        }
                    }
                });
            }

            function populateCities(stateElement, cityElement, pincodeElement, selectedCity = null) {
                let stateId = $(stateElement).val();
                $(cityElement).empty().append('<option value="">Loading...</option>');
                if(pincodeElement) $(pincodeElement).val('');

                if (stateId) {
                    $.ajax({
                        url: '{{ url("get-cities") }}/' + stateId,
                        type: 'GET',
                        success: function(data) {
                            $(cityElement).empty().append('<option value="">Select City</option>');
                            $.each(data, function(key, city) {
                                let option = $('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode);
                                if (selectedCity && city.id == selectedCity) {
                                    option.prop('selected', true);
                                    if(pincodeElement) $(pincodeElement).val(city.pincode);
                                }
                                $(cityElement).append(option);
                            });
                        }
                    });
                } else {
                    $(cityElement).empty().append('<option value="">Select State First</option>');
                }
            }

            // Initial population
            populateStates('.state-select', {{ $insurance->state_id ?? 'null' }});

            // Event Handlers
            $('.state-select').change(function() {
                let citySelect = $(this).closest('.card-body').find('.city-select');
                let pincodeInput = $(this).closest('.card-body').find('.pincode-input');
                populateCities(this, citySelect, pincodeInput);
            });

            $('.city-select').change(function() {
                let pincode = $(this).find(':selected').data('pincode');
                let pincodeInput = $(this).closest('.card-body').find('.pincode-input');
                pincodeInput.val(pincode);
            });

            $('.pincode-input').on('input', function() {
                let pincode = $(this).val();
                if (pincode.length === 6) {
                    let stateSelect = $(this).closest('.card-body').find('.state-select');
                    let citySelect = $(this).closest('.card-body').find('.city-select');

                    $.ajax({
                        url: '{{ url("get-location-by-pincode") }}/' + pincode,
                        type: 'GET',
                        success: function(data) {
                            if (data) {
                                stateSelect.val(data.state.id);
                                populateCities(stateSelect, citySelect, null, data.city.id);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            if(jqXHR.status == 404) {
                                alert('Invalid Pincode');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
