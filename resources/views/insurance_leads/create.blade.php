@extends('adminlte::page')

@section('title', 'Add New Insurance Lead')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            background: #f8f9fa;
            min-height: 150px;
            padding: 20px;
        }
        .dropzone .dz-message {
            font-weight: 400;
        }
        .dropzone .dz-message .note {
            font-size: 0.8em;
            font-weight: 200;
            display: block;
            margin-top: 1.4rem;
        }
        .dropzone .dz-preview .dz-remove {
            color: #fff;
            background: #dc3545;
            padding: 2px 5px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        .dropzone .dz-preview .dz-remove:hover {
            background: #a71d2a;
            color: #fff;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@stop

@section('content_header')
    <h1>Add New Insurance Lead</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'insurance-leads.store') }}" method="POST" enctype="multipart/form-data" id="insuranceLeadForm">
                @csrf
                {{-- Hidden Fields --}}
                <input type="hidden" name="lead_type" value="Insurance">
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id">

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
                            <label>Father Name</label>
                            <input type="text" name="father_name" class="form-control">
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
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control">
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
                            <label>Enquire At (Firm)</label>
                            <select name="firm_id" class="form-control">
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}">{{ $firm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lead Source</label>
                            <select name="lead_source" id="lead_source" class="form-control">
                                <option value="">Select Source</option>
                                <option value="Self">Self</option>
                                <option value="Social media">Social media</option>
                                <option value="Any person">Any person</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="lead_source_person_name_div" style="display:none;">
                        <div class="form-group">
                            <label>Person Name</label>
                            <div class="input-group">
                                <select name="lead_source_person_name" id="lead_source_person_name" class="form-control select2 @error('lead_source_person_name') is-invalid @enderror">
                                    <option value="">Search for a person...</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addReferralPersonModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Vehicle Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Manufacturer</label>
                            <div class="input-group">
                                <select name="manufacture_id" id="manufacture_id" class="form-control manufacture-select">
                                    <option value="">Select Manufacturer</option>
                                    @foreach($manufactures as $mfg)
                                        <option value="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Model Name</label>
                            <select name="model_name" id="model_name" class="form-control vehicle-name-select">
                                <option value="">Select Manufacturer First</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Variant</label>
                            <select name="variant" id="variant" class="form-control variant-select">
                                <option value="">Select Model First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Vehicle Type</label>
                            <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control">
                                <option value="">Select Vehicle Type</option>
                                <option value="Car" {{ old('vehicle_loan_type') == 'Car' ? 'selected' : '' }}>Car</option>
                                <option value="Commercial Vehicle" {{ old('vehicle_loan_type') == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                <option value="Construction Equipment" {{ old('vehicle_loan_type') == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                <option value="Tractor" {{ old('vehicle_loan_type') == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                <option value="2 Wheeler" {{ old('vehicle_loan_type') == '2 Wheeler' ? 'selected' : '' }}>2 Wheeler</option>
                                <option value="3 Wheeler" {{ old('vehicle_loan_type') == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                                <option value="Other" {{ old('vehicle_loan_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="other_vehicle_loan_name_div" style="display:none;">
                        <div class="form-group">
                            <label>Other Vehicle Name</label>
                            <input type="text" name="other_vehicle_loan_name" class="form-control" value="{{ old('other_vehicle_loan_name') }}">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address </h6>
                        <div class="form-group">
                            <label>Address Line </label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control" >
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code </label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State </label>
                                    <select name="current_address_state" id="current_state" class="form-control state-select" >
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="current_address_city" id="current_city" class="form-control city-select" >
                                        <option value="">Select State First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Permanent Address (As per Aadhar)</h6>
                            <div class="form-group mb-0">
                                <input type="checkbox" id="same_as_current"> <label for="same_as_current" class="mb-0 ml-1">Same as Current Address</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line </label>
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control" >
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code </label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State </label>
                                    <select name="permanent_address_state" id="permanent_state" class="form-control state-select" >
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City </label>
                                    <select name="permanent_address_city" id="permanent_city" class="form-control city-select" >
                                        <option value="">Select State First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address Proof Type <span class="text-danger">*</span></label>
                            <select name="insurance_address_proof_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Aadhar">Aadhar</option>
                                <option value="Voter Id">Voter Id</option>
                                <option value="Driving Licence">Driving Licence</option>
                                <option value="Pancard">Pancard</option>
                                <option value="Passport">Passport</option>
                                <option value="Electricity Bill">Electricity Bill</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Address Proof Upload <small class="text-muted">(Max 5MB per file, JPG, PNG, PDF)</small> <span class="text-danger">*</span></label>
                            <div id="addressProofDropzone" class="dropzone">
                                <div class="dz-message">
                                    <h3>Drop files here or click to upload.</h3>
                                    <span class="note">Multiple files allowed. Previews will be shown below.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Premium Payment Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Premium Amount Collected <span class="text-danger">*</span></label>
                            <select name="premium_amount_collected" id="premium_amount_collected" class="form-control" required>
                                <option value="No" selected>No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="premium_payment_details_container" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="premium_payment_type" id="premium_payment_type" class="form-control">
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Online">Online</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="premium_payment_fields"></div>
                </div>

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Add Insurance Lead</button>
            </form>
        </div>
    </div>

    <!-- Add Manufacture Modal -->
    <div class="modal fade" id="addManufactureModal" tabindex="-1" role="dialog" aria-labelledby="addManufactureModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addManufactureModalLabel">Add New Vehicle Manufacture</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addManufactureForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Manufacturer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Vehicle Model Name <span class="text-danger">*</span></label>
                            <input type="text" name="vehicle_name" class="form-control" required>
                        </div>
                        <div id="variant-container">
                            <div class="form-group">
                                <label>Variant(s)</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="variants[]" class="form-control" placeholder="Enter variant">
                                    <div class="input-group-append">
                                        <button class="btn btn-success add-variant" type="button"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Referral Person Modal -->
    <div class="modal fade" id="addReferralPersonModal" tabindex="-1" role="dialog" aria-labelledby="addReferralPersonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReferralPersonModalLabel">Add New Referral Person</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addReferralPersonForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Person</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            // --- Select2 for Lead Source Person ---
            $('#lead_source_person_name').select2({
                placeholder: 'Search for a person...',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('lead-source-persons.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // --- Add Referral Person AJAX ---
            $('#addReferralPersonForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('lead-source-persons.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            let newOption = new Option(response.person.name, response.person.name, true, true);
                            $('#lead_source_person_name').append(newOption).trigger('change');
                            $('#addReferralPersonModal').modal('hide');
                            $('#addReferralPersonForm')[0].reset();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding person. Please check inputs.');
                    }
                });
            });

            // Toggle "Other Vehicle Name" for Vehicle Type.
            function toggleOtherVehicle() {
                let val = $('#vehicle_loan_type').val();
                if (val === 'Other') {
                    $('#other_vehicle_loan_name_div').show();
                } else {
                    $('#other_vehicle_loan_name_div').hide();
                    $('#other_vehicle_loan_name_div').find('input').val('');
                }
            }
            $('#vehicle_loan_type').change(toggleOtherVehicle);
            toggleOtherVehicle();

            // --- Dropzone Initialization ---
            var myDropzone = new Dropzone("#addressProofDropzone", {
                url: "{{ route(get_route_prefix() . 'insurance-leads.store') }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                paramName: "insurance_address_proof",
                addRemoveLinks: true,
                dictRemoveFile: "Remove File",
                acceptedFiles: "image/*,application/pdf",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    var dz = this;

                    $("#submitBtn").click(function(e) {
                        e.preventDefault();
                        if ($("#insuranceLeadForm")[0].checkValidity()) {
                            if (dz.getQueuedFiles().length > 0) {
                                dz.processQueue();
                            } else {
                                alert("Please upload at least one address proof.");
                            }
                        } else {
                            $("#insuranceLeadForm")[0].reportValidity();
                        }
                    });

                    this.on("sendingmultiple", function(data, xhr, formData) {
                        var formArray = $("#insuranceLeadForm").serializeArray();
                        $.each(formArray, function(i, field) {
                            formData.append(field.name, field.value);
                        });
                    });

                    this.on("successmultiple", function(files, response) {
                        window.location.href = "{{ route(get_route_prefix() . 'insurance-leads.index') }}";
                    });
                }
            });

            // --- Vehicle Logic ---
            $('#manufacture_id').change(function() {
                let mfgId = $(this).val();
                let modelSelect = $('#model_name');
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
                let mfgId = $('#manufacture_id').val();
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

            // --- Lead Source Person Name Logic ---
            $('#lead_source').change(function() {
                if ($(this).val() === 'Any person') {
                    $('#lead_source_person_name_div').show();
                } else {
                    $('#lead_source_person_name_div').hide();
                }
            }).trigger('change');

            // --- Auto-calculate Insurance End Date ---
            $('#insurance_start_date').change(function() {
                var startDate = $(this).val();
                if (startDate) {
                    var start = new Date(startDate);
                    start.setFullYear(start.getFullYear() + 1);
                    // Subtract 1 day so it is exactly a 1-year period
                    start.setDate(start.getDate() - 1);
                    var endDate = start.toISOString().split('T')[0];
                    $('#insurance_end_date').val(endDate);
                }
            });

            // --- Add Manufacture AJAX ---
            $(document).on('click', '.add-variant', function() {
                let html = `
                    <div class="input-group mb-2">
                        <input type="text" name="variants[]" class="form-control" placeholder="Enter variant">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-variant" type="button"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>`;
                $('#variant-container').append(html);
            });

            $(document).on('click', '.remove-variant', function() {
                $(this).closest('.input-group').remove();
            });

            $('#addManufactureForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('vehicle-manufactures.store-ajax') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Add to all manufacture selects on the page
                            let newOption = `<option value="${response.manufacture.id}" data-id="${response.manufacture.id}">${response.manufacture.name}</option>`;
                            $('.manufacture-select').append(newOption);

                            $('#addManufactureModal').modal('hide');
                            $('#addManufactureForm')[0].reset();
                            $('#variant-container').find('.input-group:not(:first)').remove();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding manufacture. Please check inputs.');
                    }
                });
            });

            // --- Premium Payment Logic ---
            $('#premium_amount_collected').change(function() {
                const isYes = $(this).val() === 'Yes';
                $('#premium_payment_details_container').toggle(isYes);
                $('#premium_payment_type').prop('required', isYes).prop('disabled', !isYes);

                if (!isYes) {
                    $('#premium_payment_type').val('');
                    $('#premium_payment_fields').html('');
                } else if (!$('#premium_payment_type').val()) {
                    $('#premium_payment_type').val('Cash').trigger('change');
                }
            }).trigger('change');

            $('#premium_payment_type').change(function() {
                let type = $(this).val();
                let fieldsContainer = $('#premium_payment_fields');
                fieldsContainer.html(''); // Clear previous fields

                let fields = '';
                if (type === 'Cash') {
                    fields = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="premium_payment_amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (type === 'Cheque') {
                    fields = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_cheque_number" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="premium_payment_amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input type="file" name="premium_cheque_image" class="form-control-file">
                        </div>
                    `;
                } else if (type === 'Online') {
                    fields = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>UPI ID <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_upi_id" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="premium_payment_amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input type="file" name="premium_online_payment_image" class="form-control-file">
                        </div>
                    `;
                } else if (type === 'Bank Transfer') {
                    fields = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_holder_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_number" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_bank_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>IFSC Code <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_ifsc_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <input type="file" name="premium_bank_transfer_image" class="form-control-file">
                                </div>
                            </div>
                        </div>
                    `;
                }
                fieldsContainer.html(fields);
            });
        });
    </script>
@stop
