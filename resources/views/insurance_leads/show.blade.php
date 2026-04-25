@extends('adminlte::page')

@section('title', 'Insurance Lead Details')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
        .select2-container .select2-selection--single {
            height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-selection__rendered {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding-right: 20px !important; /* Ensure space for arrow */
        }
        .input-group {
            flex-wrap: nowrap;
        }
        .input-group .select2-container {
            width: auto !important;
            flex-grow: 1;
            /* Ensure the container itself doesn't overflow if flex-grow pushes it too far,
               though usually width: auto handles it in flex.
               We might need min-width: 0 to allow shrinking in flexbox. */
            min-width: 0;
        }
        .input-group .select2-selection--single {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
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
                    <p><strong>Lead Source:</strong> {{ $lead->lead_source ?? '-' }}
                        @if($lead->lead_source == 'Any person' && $lead->lead_source_person_name)
                            ({{ $lead->lead_source_person_name }})
                        @endif
                    </p>
                    <p><strong>Aadhar Number:</strong> {{ $lead->aadhar_number }}</p>
                    <p><strong>PAN Number:</strong> {{ $lead->pan_card_number ?? '-' }}</p>

                    @if($lead->profile_picture_path)
                        <p><strong>Profile Picture:</strong> <br> <img src="{{ asset('storage/' . $lead->profile_picture_path) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 100px;"></p>
                    @endif

                    <h6>Address Proofs:</h6>
                    <div class="row" id="existing-docs">
                        @forelse($lead->getMedia('insurance_address_proof') as $doc)
                            <div class="col-md-4 mb-3 document-item" id="doc-{{ $doc->id }}">
                                <div class="document-container">
                                    <span class="delete-doc" data-id="{{ $doc->id }}" title="Delete">&times;</span>
                                    <a href="{{ $doc->getUrl() }}" target="_blank">
                                        @if(Str::endsWith($doc->file_name, ['.jpg', '.jpeg', '.png']))
                                            <img src="{{ $doc->getUrl() }}" class="img-thumbnail" style="height: 80px; width: 100px; object-fit: cover;">
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
                            @if($lead->getFirstMediaUrl('insurance_rc'))
                                <a href="{{ $lead->getFirstMediaUrl('insurance_rc') }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-file-alt"></i> View RC Document
                                </a>
                            @elseif($lead->rc_document_path)
                                <a href="{{ asset('storage/' . $lead->rc_document_path) }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-file-alt"></i> View RC Document
                                </a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>PAN Card:</strong> {{ $lead->pan_card_number ?? '-' }}</p>
                            @if($lead->getFirstMediaUrl('insurance_pan_card'))
                                <a href="{{ $lead->getFirstMediaUrl('insurance_pan_card') }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-id-card"></i> View PAN Document
                                </a>
                            @elseif($lead->pan_card_document_path)
                                <a href="{{ asset('storage/' . $lead->pan_card_document_path) }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-id-card"></i> View PAN Document
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Aadhar Front:</strong></p>
                            @if($lead->getFirstMediaUrl('aadhar_front'))
                                <a href="{{ $lead->getFirstMediaUrl('aadhar_front') }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-id-card"></i> View Aadhar Front
                                </a>
                            @else
                                <span class="text-muted">Not Uploaded</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Aadhar Back:</strong></p>
                            @if($lead->getFirstMediaUrl('aadhar_back'))
                                <a href="{{ $lead->getFirstMediaUrl('aadhar_back') }}" target="_blank" class="btn btn-outline-info btn-sm mb-3">
                                    <i class="fas fa-id-card"></i> View Aadhar Back
                                </a>
                            @else
                                <span class="text-muted">Not Uploaded</span>
                            @endif
                        </div>
                    </div>
                    <hr>

                    @if($lead->lead_type == 'Insurance' || $lead->loan_type == 'Vehicle Loan')
                        <p><strong>Vehicle Details:</strong></p>
                        <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : ($lead->mfg_name ?? '-') }}</p>
                        <p><strong>Model:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : ($lead->model_name ?? '-') }}</p>
                        <p><strong>Variant:</strong> {{ $lead->vehicleName ? $lead->vehicleName->variant : ($lead->variant ?? '-') }}</p>
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
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id" value="{{ old('vehicle_name_id', $lead->vehicle_name_id) }}">
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lead Source</label>
                            <select name="lead_source" id="lead_source" class="form-control">
                                <option value="">Select Source</option>
                                <option value="Self" {{ old('lead_source', $lead->lead_source) == 'Self' ? 'selected' : '' }}>Self</option>
                                <option value="Social media" {{ old('lead_source', $lead->lead_source) == 'Social media' ? 'selected' : '' }}>Social media</option>
                                <option value="Any person" {{ strtolower(old('lead_source', $lead->lead_source)) == 'any person' ? 'selected' : '' }}>Any person</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Vehicle Type</label>
                            <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control">
                                <option value="">Select Vehicle Type</option>
                                <option value="Car" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Car' ? 'selected' : '' }}>Car</option>
                                <option value="Commercial Vehicle" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                <option value="Construction Equipment" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                <option value="Tractor" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                <option value="2 Wheeler" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == '2 Wheeler' ? 'selected' : '' }}>2 Wheeler</option>
                                <option value="3 Wheeler" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                                <option value="Other" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="lead_source_person_name_div" style="{{ strtolower($lead->lead_source) == 'any person' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Person Name</label>
                            <div class="input-group">
                                <select name="lead_source_person_name" id="lead_source_person_name" class="form-control select2">
                                    {{-- JS will handle pre-selection --}}
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addReferralPersonModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Insurance Start Date</label>
                            <input type="date" name="insurance_start_date" id="insurance_start_date" class="form-control" value="{{ $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Insurance End Date</label>
                            <input type="date" name="insurance_end_date" id="insurance_end_date" class="form-control" value="{{ $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Vehicle Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Manufacturer</label>
                            <select name="manufacture_id" id="manufacture_id" class="form-control manufacture-select">
                                <option value="">Select Manufacturer</option>
                                @foreach($manufactures as $mfg)
                                    <option value="{{ $mfg->id }}" {{ (string) old('manufacture_id', $lead->manufacture_id) === (string) $mfg->id ? 'selected' : '' }}>
                                        {{ $mfg->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Model Name</label>
                            <select name="model_name" id="model_name" class="form-control vehicle-name-select">
                                <option value="">{{ $lead->manufacture_id ? 'Loading...' : 'Select Manufacturer First' }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Variant</label>
                            <select name="variant" id="variant" class="form-control variant-select">
                                <option value="">{{ $lead->model_name ? 'Loading...' : 'Select Model First' }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="other_vehicle_loan_name_div" style="{{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Other' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Other Vehicle Name</label>
                            <input type="text" name="other_vehicle_loan_name" class="form-control" value="{{ old('other_vehicle_loan_name', $lead->other_vehicle_loan_name) }}">
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
                            <input type="number" step="0.01" name="insurance_premium_amount" class="form-control" value="{{ old('insurance_premium_amount', $lead->insurance_premium_amount ?? $lead->insurance_amount) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IDV (Insured Declared Value)</label>
                            <input type="number" step="0.01" name="insurance_idv_amount" class="form-control" value="{{ old('insurance_idv_amount', $lead->insurance_idv_amount) }}" required>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Premium Collection Amount Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Premium Amount Collected <span class="text-danger">*</span></label>
                            <select name="premium_amount_collected" id="premium_amount_collected" class="form-control" required>
                                <option value="No" {{ old('premium_amount_collected', $lead->premium_amount_collected) == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('premium_amount_collected', $lead->premium_amount_collected) == 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="premium_payment_details_container" style="{{ old('premium_amount_collected', $lead->premium_amount_collected) == 'Yes' ? '' : 'display: none;' }}">
                    @php
                        $oldPartAmounts = old('premium_payment_part_amount');
                        $oldPartDates = old('premium_payment_part_date');
                        $existingParts = is_array($oldPartAmounts)
                            ? collect($oldPartAmounts)->map(function ($amount, $idx) use ($oldPartDates) {
                                return ['amount' => $amount, 'date' => $oldPartDates[$idx] ?? null];
                            })->all()
                            : ($lead->premium_payment_parts ?? []);

                        if (empty($existingParts) && !empty($lead->premium_payment_amount)) {
                            $existingParts = [[
                                'amount' => $lead->premium_payment_amount,
                                'date' => optional($lead->premium_payment_date)->format('Y-m-d'),
                            ]];
                        }
                    @endphp
                    <div class="mb-3">
                        <label class="d-block">Collected Amount Entries <span class="text-danger">*</span></label>
                        <div id="premium-payment-parts">
                            @forelse($existingParts as $part)
                                <div class="row premium-part-row mb-2">
                                    <div class="col-md-5">
                                        <input type="number" step="0.01" min="0.01" name="premium_payment_part_amount[]" class="form-control premium-part-amount" value="{{ $part['amount'] ?? '' }}" placeholder="Collected Amount">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="date" name="premium_payment_part_date[]" class="form-control" value="{{ $part['date'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-block remove-premium-part"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            @empty
                                <div class="row premium-part-row mb-2">
                                    <div class="col-md-5">
                                        <input type="number" step="0.01" min="0.01" name="premium_payment_part_amount[]" class="form-control premium-part-amount" placeholder="Collected Amount">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="date" name="premium_payment_part_date[]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-block remove-premium-part"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-premium-part" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="fas fa-plus"></i> Add Part
                        </button>
                        @error('premium_payment_part_amount')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="mb-1">Total Collected</label>
                                <input type="number" step="0.01" name="premium_payment_amount" id="premium_payment_amount_total" class="form-control" value="{{ old('premium_payment_amount', $lead->premium_payment_amount) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1">Balance Amount</label>
                                <input type="number" step="0.01" id="premium_balance_amount_display" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="premium_payment_type" id="premium_payment_type" class="form-control">
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash" {{ old('premium_payment_type', $lead->premium_payment_type) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Cheque" {{ old('premium_payment_type', $lead->premium_payment_type) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Online" {{ old('premium_payment_type', $lead->premium_payment_type) == 'Online' ? 'selected' : '' }}>UPI/QR</option>
                                    <option value="Bank Transfer" {{ old('premium_payment_type', $lead->premium_payment_type) == 'Bank Transfer' ? 'selected' : '' }}>NEFT/RTGS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="premium_payment_fields"></div>
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
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Aadhar Front</label>
                            <input type="file" name="aadhar_front" class="form-control-file">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Aadhar Back</label>
                            <input type="file" name="aadhar_back" class="form-control-file">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Finalize Insurance</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Upload Final Policy Document <small class="text-muted">(PDF, JPG, PNG)</small></label>
                            <input type="file" name="final_policy_document" class="form-control-file">
                            @if($lead->getFirstMediaUrl('final_document'))
                                <div class="mt-2">
                                    <a href="{{ $lead->getFirstMediaUrl('final_document') }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> View Final Document
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="insurance_lead_status" class="form-control">
                                <option value="In Progress" {{ $lead->insurance_lead_status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Done" {{ $lead->insurance_lead_status == 'Done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Update All Details</button>
            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- Select2 for Lead Source Person ---
            var leadSourcePersonSelect = $('#lead_source_person_name');

            leadSourcePersonSelect.select2({
                placeholder: 'Search for a person...',
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: "{{ route('lead-source-persons.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            // --- Pre-select person name if it exists ---
            var initialPersonName = "{!! $lead->lead_source_person_name !!}";

            if (initialPersonName) {
                // Create a DOM Option and append it to the select box
                var newOption = new Option(initialPersonName, initialPersonName, true, true);
                leadSourcePersonSelect.append(newOption).trigger('change');
            }

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

            $("#submitBtn").click(function(e) {
                e.preventDefault();
                if ($("#updateInsuranceForm")[0].checkValidity()) {
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
                } else {
                    $("#updateInsuranceForm")[0].reportValidity();
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

            // --- Lead Source Person Name Logic ---
            $('#lead_source').change(function() {
                var val = $(this).val();
                if (val && val.toLowerCase() === 'any person') {
                    $('#lead_source_person_name_div').show();
                } else {
                    $('#lead_source_person_name_div').hide();
                }
            }).trigger('change');

            // --- Vehicle Detail Logic ---
            function toggleOtherVehicle() {
                let val = $('#vehicle_loan_type').val();
                if (val === 'Other') {
                    $('#other_vehicle_loan_name_div').show();
                } else {
                    $('#other_vehicle_loan_name_div').hide();
                    $('#other_vehicle_loan_name_div').find('input').val('');
                }
            }

            function loadModelNames(selectedModel = null) {
                let mfgId = $('#manufacture_id').val();
                let modelSelect = $('#model_name');
                modelSelect.empty().append('<option value="">Loading...</option>');

                if (!mfgId) {
                    modelSelect.empty().append('<option value="">Select Manufacturer First</option>');
                    $('#variant').empty().append('<option value="">Select Model First</option>');
                    return;
                }

                $.ajax({
                    url: '{{ url("get-vehicle-names") }}/' + mfgId,
                    type: 'GET',
                    success: function(data) {
                        modelSelect.empty().append('<option value="">Select Model</option>');
                        $.each(data, function(key, name) {
                            let selected = selectedModel && selectedModel === name ? 'selected' : '';
                            modelSelect.append('<option value="' + name + '" ' + selected + '>' + name + '</option>');
                        });
                        if (selectedModel) {
                            modelSelect.trigger('change');
                        }
                    }
                });
            }

            function loadVariants(selectedVariant = null, selectedVehicleNameId = null) {
                let modelName = $('#model_name').val();
                let mfgId = $('#manufacture_id').val();
                let variantSelect = $('#variant');
                variantSelect.empty().append('<option value="">Loading...</option>');

                if (!(mfgId && modelName)) {
                    variantSelect.empty().append('<option value="">Select Model First</option>');
                    $('#vehicle_name_id').val(selectedVehicleNameId || '');
                    return;
                }

                $.ajax({
                    url: '{{ route("get-variants") }}',
                    type: 'GET',
                    data: { manufactureId: mfgId, vehicleName: modelName },
                    success: function(data) {
                        variantSelect.empty().append('<option value="">Select Variant</option>');
                        $.each(data, function(key, item) {
                            let selected = selectedVariant && selectedVariant === item.variant ? 'selected' : '';
                            variantSelect.append('<option value="' + item.variant + '" data-id="' + item.id + '" ' + selected + '>' + item.variant + '</option>');
                        });

                        if (selectedVehicleNameId) {
                            let selectedOption = variantSelect.find('option[data-id="' + selectedVehicleNameId + '"]');
                            if (selectedOption.length) {
                                selectedOption.prop('selected', true);
                            }
                        }

                        variantSelect.trigger('change');
                    }
                });
            }

            $('#manufacture_id').change(function() {
                loadModelNames();
            });

            $('#model_name').change(function() {
                loadVariants();
            });

            $('#variant').change(function() {
                let selectedId = $(this).find(':selected').data('id') || '';
                $('#vehicle_name_id').val(selectedId);
            });

            $('#vehicle_loan_type').change(toggleOtherVehicle);
            toggleOtherVehicle();

            let initialManufactureId = $('#manufacture_id').val();
            let initialModelName = @json(old('model_name', $lead->model_name));
            let initialVariant = @json(old('variant', $lead->variant));
            let initialVehicleNameId = @json(old('vehicle_name_id', $lead->vehicle_name_id));

            if (initialManufactureId) {
                loadModelNames(initialModelName);
                if (initialModelName) {
                    loadVariants(initialVariant, initialVehicleNameId);
                }
            }

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
                    fields = '';
                } else if (type === 'Cheque') {
                    fields = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_cheque_number" class="form-control" value="{{ $lead->premium_cheque_number }}" required>
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
                                    <label>UPI / QR Ref. <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_upi_id" class="form-control" value="{{ $lead->premium_upi_id }}" required>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_holder_name" class="form-control" value="{{ $lead->premium_account_holder_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_number" class="form-control" value="{{ $lead->premium_account_number }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_bank_name" class="form-control" value="{{ $lead->premium_bank_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>IFSC Code <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_ifsc_code" class="form-control" value="{{ $lead->premium_ifsc_code }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input type="file" name="premium_bank_transfer_image" class="form-control-file">
                        </div>
                    `;
                }
                fieldsContainer.html(fields);
            }).trigger('change');

            function recalculatePremiumTotals() {
                let total = 0;
                $('.premium-part-amount').each(function() {
                    const val = parseFloat($(this).val());
                    if (!isNaN(val) && val > 0) {
                        total += val;
                    }
                });
                total = Math.round(total * 100) / 100;
                $('#premium_payment_amount_total').val(total ? total.toFixed(2) : '');

                const premium = parseFloat($('input[name="insurance_premium_amount"]').val()) || 0;
                const balance = Math.max(0, Math.round((premium - total) * 100) / 100);
                $('#premium_balance_amount_display').val(balance.toFixed(2));
            }

            $('#add-premium-part').on('click', function() {
                $('#premium-payment-parts').append(`
                    <div class="row premium-part-row mb-2">
                        <div class="col-md-5">
                            <input type="number" step="0.01" min="0.01" name="premium_payment_part_amount[]" class="form-control premium-part-amount" placeholder="Collected Amount">
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="premium_payment_part_date[]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-block remove-premium-part"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `);
            });

            $(document).on('click', '.remove-premium-part', function() {
                const rows = $('.premium-part-row').length;
                if (rows > 1) {
                    $(this).closest('.premium-part-row').remove();
                } else {
                    $(this).closest('.premium-part-row').find('input').val('');
                }
                recalculatePremiumTotals();
            });

            $(document).on('input change', '.premium-part-amount, input[name="insurance_premium_amount"]', recalculatePremiumTotals);
            recalculatePremiumTotals();
        });
    </script>
@stop
