@extends('adminlte::page')

@section('title', 'Insurance Lead Details')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            background: #f8f9fa;
            min-height: 120px;
            padding: 10px;
        }
        .document-container {
            position: relative;
            display: inline-block;
        }
        .delete-doc {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 18px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid white;
            z-index: 10;
        }
    </style>
@stop

@section('content_header')
    <h1>Insurance Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    <div id="alert-container"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Father Name:</strong> {{ $lead->father_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    <p><strong>Email:</strong> {{ $lead->email }}</p>
                    <p><strong>Firm:</strong> {{ $lead->firm ? $lead->firm->name : '-' }}</p>
                    <p><strong>Aadhar Number:</strong> {{ $lead->aadhar_number }}</p>
                    <p><strong>PAN Number:</strong> {{ $lead->pan_card_number ?? '-' }}</p>

                    @if($lead->profile_picture_path)
                        <p><strong>Profile Picture:</strong> <br> <img src="{{ asset('storage/' . $lead->profile_picture_path) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 100px;"></p>
                    @endif

                    <h6>Address Proofs:</h6>
                    <div class="row" id="existing-docs">
                        @forelse($lead->documents->where('document_type', 'address_proof') as $doc)
                            <div class="col-md-4 mb-3 document-item" id="doc-{{ $doc->id }}">
                                <div class="document-container">
                                    <span class="delete-doc" data-id="{{ $doc->id }}" title="Delete">&times;</span>
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                        @if(Str::endsWith($doc->file_path, ['.jpg', '.jpeg', '.png']))
                                            <img src="{{ asset('storage/' . $doc->file_path) }}" class="img-thumbnail" style="height: 80px; width: 100px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center border" style="height: 80px; width: 100px;">
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><p class="text-muted small">No address proofs uploaded.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle & KYC Documents</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>RC Number:</strong> {{ $lead->rc_number ?? '-' }}</p>
                            @if($lead->rc_document_path)
                                <a href="{{ asset('storage/' . $lead->rc_document_path) }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-file-alt"></i> View RC Document
                                </a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>PAN Card:</strong> {{ $lead->pan_card_number ?? '-' }}</p>
                            @if($lead->pan_card_document_path)
                                <a href="{{ asset('storage/' . $lead->pan_card_document_path) }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-id-card"></i> View PAN Document
                                </a>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <p><strong>Loan Type:</strong> {{ $lead->loan_type }}</p>
                    @if($lead->loan_type == 'Vehicle Loan')
                        <p><strong>Vehicle Type:</strong> {{ $lead->vehicle_loan_type }}</p>
                        <p><strong>Purchase Type:</strong> {{ $lead->vehicle_purchase_type }}</p>
                        <p><strong>Mfg Name:</strong> {{ $lead->mfg_name }}</p>
                        <p><strong>Model Name:</strong> {{ $lead->model_name }}</p>
                        <p><strong>Variant:</strong> {{ $lead->variant }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Insurance & KYC Details</h3>
        </div>
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'insurance-leads.update-details', $lead->id) }}" method="POST" enctype="multipart/form-data" id="updateInsuranceForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Insurance Company</label>
                            <select name="insurance_id" class="form-control" required>
                                <option value="">Select Company</option>
                                @foreach($insurances as $insurance)
                                    <option value="{{ $insurance->id }}" {{ $lead->insurance_id == $insurance->id ? 'selected' : '' }}>{{ $insurance->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Insurance Start Date</label>
                            <input type="date" name="insurance_start_date" class="form-control" value="{{ $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Insurance End Date</label>
                            <input type="date" name="insurance_end_date" class="form-control" value="{{ $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Type Of Insurance</label>
                    @php
                        $insuranceTypes = ['Third Party Insurance', 'Commercial Vehicle Insurance', 'Comprehensive Car Insurance', 'Private Car Insurance', 'Personal Accident Cover', 'Zero Depreciation Cover', 'Roadside Assistance'];
                        $selectedTypes = $lead->insurance_type_of_insurance ? explode(', ', $lead->insurance_type_of_insurance) : [];
                    @endphp
                    <div class="row">
                        @foreach($insuranceTypes as $type)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="insurance_type_of_insurance[]" value="{{ $type }}" {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $type }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Premium Amount</label>
                            <input type="number" step="0.01" name="insurance_premium_amount" class="form-control" value="{{ $lead->insurance_premium_amount }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IDV (Insured Declared Value)</label>
                            <input type="number" step="0.01" name="insurance_idv_amount" class="form-control" value="{{ $lead->insurance_idv_amount }}" required>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>KYC & Vehicle Documents</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>PAN Card Number</label>
                            <input type="text" name="pan_card_number" class="form-control" value="{{ $lead->pan_card_number }}" placeholder="Enter PAN Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>PAN Card Document</label>
                            <input type="file" name="pan_card_document" class="form-control-file">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>RC Number</label>
                            <input type="text" name="rc_number" class="form-control" value="{{ $lead->rc_number }}" placeholder="Enter RC Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>RC Document</label>
                            <input type="file" name="rc_document" class="form-control-file">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Add More Address Proofs</h5>
                <div id="addressProofDropzone" class="dropzone mb-3">
                    <div class="dz-message">
                        <span>Drop files here or click to upload new address proofs.</span>
                    </div>
                </div>

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Update All Details</button>
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
                url: "{{ route(get_route_prefix() . 'insurance-leads.update-details', $lead->id) }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                paramName: "insurance_address_proof",
                addRemoveLinks: true,
                acceptedFiles: "image/*,application/pdf",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    var dz = this;

                    $("#submitBtn").click(function(e) {
                        e.preventDefault();
                        if ($("#updateInsuranceForm")[0].checkValidity()) {
                            if (dz.getQueuedFiles().length > 0) {
                                dz.processQueue();
                            } else {
                                // If no new files, submit form normally via AJAX
                                var formData = new FormData($("#updateInsuranceForm")[0]);
                                $.ajax({
                                    url: $("#updateInsuranceForm").attr('action'),
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        window.location.reload();
                                    },
                                    error: function(err) {
                                        alert("Error updating details.");
                                    }
                                });
                            }
                        } else {
                            $("#updateInsuranceForm")[0].reportValidity();
                        }
                    });

                    this.on("sendingmultiple", function(data, xhr, formData) {
                        var formArray = $("#updateInsuranceForm").serializeArray();
                        $.each(formArray, function(i, field) {
                            formData.append(field.name, field.value);
                        });
                        // Add files from regular inputs
                        var panFile = $('input[name="pan_card_document"]')[0].files[0];
                        if(panFile) formData.append('pan_card_document', panFile);

                        var rcFile = $('input[name="rc_document"]')[0].files[0];
                        if(rcFile) formData.append('rc_document', rcFile);
                    });

                    this.on("successmultiple", function(files, response) {
                        window.location.reload();
                    });
                }
            });

            // --- Delete Document Logic ---
            $('.delete-doc').click(function() {
                if(confirm('Are you sure you want to delete this document?')) {
                    var docId = $(this).data('id');
                    var url = "{{ route(get_route_prefix() . 'insurance-leads.delete-document', ':id') }}".replace(':id', docId);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#doc-' + docId).remove();
                            $('#alert-container').html('<div class="alert alert-success">Document deleted successfully.</div>');
                        },
                        error: function() {
                            alert('Error deleting document.');
                        }
                    });
                }
            });
        });
    </script>
@stop
