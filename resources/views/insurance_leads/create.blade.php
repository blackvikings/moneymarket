@extends('adminlte::page')

@section('title', 'Add New Insurance Lead')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
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

                <hr>
                <h5>Vehicle Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Manufacturer</label>
                            <select name="manufacture_id" id="manufacture_id" class="form-control manufacture-select">
                                <option value="">Select Manufacturer</option>
                                @foreach($manufactures as $mfg)
                                    <option value="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                @endforeach
                            </select>
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

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Add Insurance Lead</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
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
        });
    </script>
@stop
