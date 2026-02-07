@extends('adminlte::page')

@section('title', 'Edit Firm')

@section('content_header')
    <h1>Edit Firm</h1>
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
            <form action="{{ route('admin.firms.update', $firm->id) }}" method="POST" id="editFirmForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Firm Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $firm->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="form-group">
                    <label>Address Line <span class="text-danger">*</span></label>
                    <input type="text" name="address_line" id="address_line" class="form-control @error('address_line') is-invalid @enderror" value="{{ old('address_line', $firm->address_line) }}" required>
                    @error('address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pin Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" id="pincode" class="form-control pincode-input @error('pincode') is-invalid @enderror" value="{{ old('pincode', $firm->pincode) }}" required>
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

                <button type="submit" class="btn btn-primary">Save Firm</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#editFirmForm').validate({
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
                            populateCities('#state_id', '#city_id', null, {{ $firm->city_id ?? 'null' }});
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
            populateStates('.state-select', {{ $firm->state_id ?? 'null' }});

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
