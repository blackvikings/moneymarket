@extends('adminlte::page')

@section('title', 'Add New RTO Lead')

@section('content_header')
    <h1>Add New RTO Lead</h1>
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
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix(). 'rto.store') }}" method="POST" enctype="multipart/form-data" id="createRtoLeadForm">
                @csrf
                <input type="hidden" name="lead_type" value="RTO">
                {{-- This will hold the actual ID of the selected variant --}}
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id" value="{{ old('vehicle_name_id') }}">

                <h5>Customer Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquiry Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar_number" class="form-control" placeholder="12-digit Aadhar Number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control" required>
                                <option value="Rented">Rented</option>
                                <option value="Own">Own</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address <span class="text-danger">*</span></h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="current_address_state" id="current_state" class="form-control state-select" required>
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="current_address_city" id="current_city" class="form-control city-select" required>
                                        <option value="">Select State First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Permanent Address (As per Aadhar) <span class="text-danger">*</span></h6>
                            <div class="form-group mb-0">
                                <input type="checkbox" id="same_as_current"> <label for="same_as_current" class="mb-0 ml-1">Same as Current Address</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="permanent_address_state" id="permanent_state" class="form-control state-select" required>
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="permanent_address_city" id="permanent_city" class="form-control city-select" required>
                                        <option value="">Select State First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof Type</label>
                            <select name="proof_id_type" class="form-control @error('proof_id_type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                <option value="Aadhar" {{ old('proof_id_type') == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                <option value="Voter Id" {{ old('proof_id_type') == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                <option value="Driving Licence" {{ old('proof_id_type') == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                <option value="Pancard" {{ old('proof_id_type') == 'Pancard' ? 'selected' : '' }}>Pancard</option>
                                <option value="Passport" {{ old('proof_id_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                                <option value="Electricity Bill" {{ old('proof_id_type') == 'Electricity Bill' ? 'selected' : '' }}>Electricity Bill</option>
                            </select>
                            @error('proof_id_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Upload Address Proof <span class="text-danger">*</span></label>
                            <input type="file" name="current_address_proof" class="form-control-file" required>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- RTO Details Section --}}
                <div id="rto_section">
                    <h5>RTO Details</h5>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control @error('rto_rc_number') is-invalid @enderror" value="{{ old('rto_rc_number') }}">@error('rto_rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control @error('rto_engine_number') is-invalid @enderror" value="{{ old('rto_engine_number') }}">@error('rto_engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control @error('rto_chassis_number') is-invalid @enderror" value="{{ old('rto_chassis_number') }}">@error('rto_chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="rto_mfg_year" class="form-control @error('rto_mfg_year') is-invalid @enderror" value="{{ old('rto_mfg_year') }}">@error('rto_mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mfg Name</label>
                                <select name="rto_mfg_name" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
                                    <option value="">Select Manufacturer</option>
                                    @foreach($manufactures as $mfg)
                                        <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('rto_mfg_name') == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                    @endforeach
                                </select>
                                @error('rto_mfg_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Model Name</label>
                                <select name="rto_model_name" class="form-control vehicle-name-select @error('rto_model_name') is-invalid @enderror">
                                    <option value="">Select Manufacturer First</option>
                                </select>
                                @error('rto_model_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Variant</label>
                                <select name="rto_variant" class="form-control variant-select @error('rto_variant') is-invalid @enderror @error('vehicle_name_id') is-invalid @enderror">
                                    <option value="">Select Vehicle First</option>
                                </select>
                                @error('rto_variant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                @error('vehicle_name_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="rto_owner_serial_number" class="form-control @error('rto_owner_serial_number') is-invalid @enderror" value="{{ old('rto_owner_serial_number') }}">@error('rto_owner_serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Insurance Available?</label>
                                <select name="insurance_available" id="rto_insurance_available" class="form-control @error('insurance_available') is-invalid @enderror">
                                    <option value="0" {{ old('insurance_available') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('insurance_available') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('insurance_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_insurance_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor</label>
                                        <select name="insurance_id" class="form-control @error('insurance_id') is-invalid @enderror">
                                            <option value="">Select Vendor</option>
                                            @foreach($insurances as $ins)
                                                <option value="{{ $ins->id }}" {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('insurance_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control @error('insurance_start_date') is-invalid @enderror" value="{{ old('insurance_start_date') }}">@error('insurance_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control @error('insurance_end_date') is-invalid @enderror" value="{{ old('insurance_end_date') }}">@error('insurance_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control @error('insurance_amount') is-invalid @enderror" value="{{ old('insurance_amount') }}">@error('insurance_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="rto_hypothecation_status" id="rto_hypothecation_status" class="form-control @error('rto_hypothecation_status') is-invalid @enderror">
                                    <option value="0" {{ old('rto_hypothecation_status') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('rto_hypothecation_status') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('rto_hypothecation_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_hp_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="rto_old_financer" class="form-control @error('rto_old_financer') is-invalid @enderror" value="{{ old('rto_old_financer') }}">@error('rto_old_financer') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="rto_close_amount" class="form-control @error('rto_close_amount') is-invalid @enderror" value="{{ old('rto_close_amount') }}">@error('rto_close_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="rto_emi_paid_count" class="form-control @error('rto_emi_paid_count') is-invalid @enderror" value="{{ old('rto_emi_paid_count') }}">@error('rto_emi_paid_count') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Add RTO Lead</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createRtoLeadForm').validate({
                rules: {
                    customer_name: { required: true },
                    father_name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
                    email: { required: true, email: true },
                    residence_type: { required: true },
                    current_address_line: { required: true },
                    current_address_state: { required: true },
                    current_address_city: { required: true },
                    permanent_address_line: { required: true },
                    permanent_address_state: { required: true },
                    permanent_address_city: { required: true },
                    current_address_proof: { required: true },
                    rto_rc_number: { required: true }
                },
                messages: {
                    customer_name: "Please enter the customer's name",
                    father_name: "Please enter the father's name",
                    contact_number: {
                        required: "Please enter a mobile number",
                        number: "Please enter a valid number",
                        minlength: "Mobile number must be 10 digits",
                        maxlength: "Mobile number must be 10 digits"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    residence_type: "Please select a residence type",
                    current_address_line: "Please enter the current address",
                    current_address_state: "Please select a state",
                    current_address_city: "Please select a city",
                    permanent_address_line: "Please enter the permanent address",
                    permanent_address_state: "Please select a state",
                    permanent_address_city: "Please select a city",
                    current_address_proof: "Please upload an address proof document",
                    rto_rc_number: "Please enter the RC number"
                }
            });

            // --- Location Scripts ---
            function populateStates(selector) {
                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        $(selector).each(function() {
                            let currentVal = $(this).val();
                            $(this).empty().append('<option value="">Select State</option>');
                            $.each(data, function(key, state) {
                                $(selector).append('<option value="' + state.id + '">' + state.name + '</option>');
                            });
                            $(this).val(currentVal);
                        });
                    }
                });
            }

            function populateCities(stateElement, cityElement, pincodeElement, selectedCity = null) {
                let stateId = $(stateElement).val();
                $(cityElement).empty().append('<option value="">Loading...</option>');
                if(pincodeElement) $(pincodeElement).val('');

                if (stateId) {
                    $.ajax({
                        url: '{{ route("get-cities", ["state" => ":stateId"]) }}'.replace(':stateId', stateId),
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
            populateStates('.state-select');

            // Event Handlers
            $('.state-select').change(function() {
                let citySelect = $(this).closest('.row').find('.city-select');
                let pincodeInput = $(this).closest('.row').find('.pincode-input');
                populateCities(this, citySelect, pincodeInput);
            });

            $('.city-select').change(function() {
                let pincode = $(this).find(':selected').data('pincode');
                let pincodeInput = $(this).closest('.row').find('.pincode-input');
                pincodeInput.val(pincode);
            });

            $('.pincode-input').on('input', function() {
                let pincode = $(this).val();
                if (pincode.length === 6) {
                    let stateSelect = $(this).closest('.row').find('.state-select');
                    let citySelect = $(this).closest('.row').find('.city-select');

                    $.ajax({
                        url: '{{ route("get-location-by-pincode", ["pincode" => ":pincode"]) }}'.replace(':pincode', pincode),
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

            $('#same_as_current').change(function() {
                if ($(this).is(':checked')) {
                    $('#permanent_address_line').val($('#current_address_line').val());
                    $('#permanent_pincode').val($('#current_pincode').val());
                    let currentStateVal = $('#current_state').val();
                    $('#permanent_state').val(currentStateVal);

                    // Explicitly populate cities for permanent address
                    populateCities('#permanent_state', '#permanent_city', '#permanent_pincode', $('#current_city').val());
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Vehicle Logic ---
            function populateVehicleNames(manufactureId, container, selectedVehicleName = null) {
                let vehicleSelect = container.find('.vehicle-name-select');
                vehicleSelect.empty().append('<option value="">Loading...</option>');

                if (manufactureId) {
                    $.ajax({
                        url: '{{ url("get-vehicle-names") }}/' + manufactureId,
                        type: 'GET',
                        success: function(data) {
                            vehicleSelect.empty().append('<option value="">Select Vehicle</option>');
                            $.each(data, function(key, name) {
                                let option = $('<option></option>').attr('value', name).text(name);
                                if (name == selectedVehicleName) {
                                    option.prop('selected', true);
                                }
                                vehicleSelect.append(option);
                            });
                            if(selectedVehicleName) {
                                vehicleSelect.trigger('change');
                            }
                        }
                    });
                } else {
                    vehicleSelect.empty().append('<option value="">Select Manufacturer First</option>');
                }
            }

            function populateVariants(manufactureId, vehicleName, container, selectedVariant = null) {
                let variantSelect = container.find('.variant-select');
                variantSelect.empty().append('<option value="">Loading...</option>');

                if (manufactureId && vehicleName) {
                    $.ajax({
                        url: '{{ route("get-variants") }}',
                        type: 'GET',
                        data: {
                            manufactureId: manufactureId,
                            vehicleName: vehicleName
                        },
                        success: function(data) {
                            variantSelect.empty().append('<option value="">Select Variant</option>');
                            $.each(data, function(key, item) {
                                let option = $('<option></option>').attr('value', item.variant).text(item.variant).data('id', item.id);
                                if (item.variant == selectedVariant) {
                                    option.prop('selected', true);
                                    $('#vehicle_name_id').val(item.id); // Pre-select the ID
                                }
                                variantSelect.append(option);
                            });
                        }
                    });
                } else {
                    variantSelect.empty().append('<option value="">Select Vehicle First</option>');
                }
            }

            $(document).on('change', '.manufacture-select', function() {
                let selectedOption = $(this).find('option:selected');
                let mfgId = selectedOption.attr('data-id'); // Try getting data-id attribute

                if (!mfgId) {
                    mfgId = $(this).val(); // Fallback to value (for New Purchase)
                }

                let container = $(this).closest('.row');
                populateVehicleNames(mfgId, container);
            });

            $(document).on('change', '.vehicle-name-select', function() {
                let vehicleName = $(this).val();
                let container = $(this).closest('.row');

                let mfgSelect = container.find('.manufacture-select');
                let selectedOption = mfgSelect.find('option:selected');
                let mfgId = selectedOption.attr('data-id');
                if (!mfgId) {
                    mfgId = mfgSelect.val();
                }

                let variantToSelect = container.find('.variant-select').data('selected');
                populateVariants(mfgId, vehicleName, container, variantToSelect);
            });

            $(document).on('change', '.variant-select', function() {
                let selectedId = $(this).find(':selected').data('id');
                $('#vehicle_name_id').val(selectedId);
            });

            $('#rto_insurance_available').change(function() {
                $('#rto_insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#rto_hypothecation_status').change(function() {
                $('#rto_hp_details_div').toggle($(this).val() == '1');
            }).trigger('change');
        });
    </script>
@stop
