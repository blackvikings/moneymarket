@extends('adminlte::page')

@section('title', 'Add New RTO Lead')

@section('css')
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
    </style>
@stop

@section('content_header')
    <h1>Add New RTO Lead</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'rto.store') }}" method="POST" enctype="multipart/form-data" id="rtoLeadForm">
                @csrf
                {{-- Hidden Lead Type --}}
                <input type="hidden" name="lead_type" value="RTO">
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id">

                <h5>Customer Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                            @error('customer_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name') }}" required>
                            @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror" value="{{ old('aadhar_number') }}" placeholder="12-digit Aadhar Number">
                            @error('aadhar_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquire At (Firm)</label>
                            <select name="firm_id" class="form-control @error('firm_id') is-invalid @enderror">
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                            @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control @error('current_address_line') is-invalid @enderror" value="{{ old('current_address_line') }}">
                            @error('current_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input @error('current_address_pincode') is-invalid @enderror" value="{{ old('current_address_pincode') }}">
                                    @error('current_address_pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="current_address_state" id="current_state" class="form-control state-select @error('current_address_state') is-invalid @enderror">
                                        <option value="">Select State</option>
                                    </select>
                                    @error('current_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="current_address_city" id="current_city" class="form-control city-select @error('current_address_city') is-invalid @enderror">
                                        <option value="">Select State First</option>
                                    </select>
                                    @error('current_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Permanent Address</h6>
                            <div class="form-group mb-0">
                                <input type="checkbox" id="same_as_current"> <label for="same_as_current" class="mb-0 ml-1">Same as Current Address</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control @error('permanent_address_line') is-invalid @enderror" value="{{ old('permanent_address_line') }}">
                            @error('permanent_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input @error('permanent_address_pincode') is-invalid @enderror" value="{{ old('permanent_address_pincode') }}">
                                    @error('permanent_address_pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="permanent_address_state" id="permanent_state" class="form-control state-select @error('permanent_address_state') is-invalid @enderror">
                                        <option value="">Select State</option>
                                    </select>
                                    @error('permanent_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="permanent_address_city" id="permanent_city" class="form-control city-select @error('permanent_address_city') is-invalid @enderror">
                                        <option value="">Select State First</option>
                                    </select>
                                    @error('permanent_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h5>RTO Details</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control @error('rto_rc_number') is-invalid @enderror" value="{{ old('rto_rc_number') }}" required>@error('rto_rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control @error('rto_engine_number') is-invalid @enderror" value="{{ old('rto_engine_number') }}">@error('rto_engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control @error('rto_chassis_number') is-invalid @enderror" value="{{ old('rto_chassis_number') }}">@error('rto_chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="rto_mfg_year" class="form-control @error('rto_mfg_year') is-invalid @enderror" value="{{ old('rto_mfg_year') }}">@error('rto_mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mfg Name</label>
                            <select name="rto_mfg_name" id="manufacture_id" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
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
                            <select name="rto_model_name" id="model_name" class="form-control vehicle-name-select @error('rto_model_name') is-invalid @enderror">
                                <option value="">Select Manufacturer First</option>
                            </select>
                            @error('rto_model_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Variant</label>
                            <select name="rto_variant" id="variant" class="form-control variant-select @error('rto_variant') is-invalid @enderror">
                                <option value="">Select Model First</option>
                            </select>
                            @error('rto_variant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="rto_owner_serial_number" class="form-control @error('rto_owner_serial_number') is-invalid @enderror" value="{{ old('rto_owner_serial_number') }}">@error('rto_owner_serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Insurance Available?</label>
                            <select name="insurance_available" id="insurance_available" class="form-control @error('insurance_available') is-invalid @enderror">
                                <option value="0" {{ old('insurance_available') == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('insurance_available') == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('insurance_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-9" id="insurance_details_div" style="display:none;">
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
                            <select name="rto_hypothecation_status" id="hypothecation_status" class="form-control @error('rto_hypothecation_status') is-invalid @enderror">
                                <option value="0" {{ old('rto_hypothecation_status') == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('rto_hypothecation_status') == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('rto_hypothecation_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-9" id="hp_details_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4"><div class="form-group"><label>Financer Name</label><input type="text" name="rto_old_financer" class="form-control @error('rto_old_financer') is-invalid @enderror" value="{{ old('rto_old_financer') }}">@error('rto_old_financer') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            {{-- <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="rto_close_amount" class="form-control @error('rto_close_amount') is-invalid @enderror" value="{{ old('rto_close_amount') }}">@error('rto_close_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div> --}}
                        </div>
                    </div>
                </div>

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Add RTO Lead</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize validation
            $('#rtoLeadForm').validate({
                rules: {
                    customer_name: { required: true },
                    father_name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
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
                    rto_rc_number: "Please enter the RC number"
                }
            });

            $("#submitBtn").click(function(e) {
                e.preventDefault();
                if (!$("#rtoLeadForm").valid()) {
                    return;
                }

                var formData = new FormData($("#rtoLeadForm")[0]);
                $.ajax({
                    url: $("#rtoLeadForm").attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        window.location.href = "{{ route(get_route_prefix() . 'rto.index') }}";
                    },
                    error: function() {
                        alert("Error creating lead.");
                    }
                });
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

            populateStates('.state-select');

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
                    populateCities('#permanent_state', '#permanent_city', '#permanent_pincode', $('#current_city').val());
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Vehicle Logic ---
            $('#manufacture_id').change(function() {
                let mfgId = $(this).find(':selected').data('id');
                let modelSelect = modelSelect = $('#model_name');
                modelSelect.empty().append('<option value="">Loading...</option>');

                if (mfgId) {
                    $.ajax({
                        url: '{{ url("get-vehicle-names") }}/' + mfgId,
                        type: 'GET',
                        success: function(data) {
                            modelSelect.empty().append('<option value="">Select Model</option>');
                            $.each(data, function(key, name) {
                                modelSelect.append('<option value="' + name + '">' + name + '</option>');
                            });
                        }
                    });
                } else {
                    modelSelect.empty().append('<option value="">Select Manufacturer First</option>');
                }
            });

            $('#model_name').change(function() {
                let modelName = $(this).val();
                let mfgId = $('#manufacture_id').find(':selected').data('id');
                let variantSelect = $('#variant');
                variantSelect.empty().append('<option value="">Loading...</option>');

                if (mfgId && modelName) {
                    $.ajax({
                        url: '{{ route("get-variants") }}',
                        type: 'GET',
                        data: { manufactureId: mfgId, vehicleName: modelName },
                        success: function(data) {
                            variantSelect.empty().append('<option value="">Select Variant</option>');
                            $.each(data, function(key, item) {
                                variantSelect.append('<option value="' + item.variant + '" data-id="' + item.id + '">' + item.variant + '</option>');
                            });
                        }
                    });
                } else {
                    variantSelect.empty().append('<option value="">Select Model First</option>');
                }
            });

            $('#variant').change(function() {
                let selectedId = $(this).find(':selected').data('id');
                $('#vehicle_name_id').val(selectedId);
            });

            $('#insurance_available').change(function() {
                $('#insurance_details_div').toggle($(this).val() == '1');
            });

            $('#hypothecation_status').change(function() {
                $('#hp_details_div').toggle($(this).val() == '1');
            });

            $('input[name="insurance_start_date"]').change(function() {
                var startDate = $(this).val();
                if (startDate) {
                    var start = new Date(startDate);
                    start.setFullYear(start.getFullYear() + 1);
                    start.setDate(start.getDate() - 1);
                    var endDate = start.toISOString().split('T')[0];
                    $('input[name="insurance_end_date"]').val(endDate);
                }
            });
        });
    </script>
@stop
