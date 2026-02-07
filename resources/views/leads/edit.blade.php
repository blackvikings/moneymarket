@extends('adminlte::page')

@section('title', 'Edit Lead')

@section('content_header')
    <h1>Edit Lead</h1>
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
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route(get_route_prefix() . 'leads.update', $lead->id) }}" method="POST" enctype="multipart/form-data" id="editLeadForm">
                @csrf
                @method('PUT')

                {{-- Hidden fields for "Qualify on Save" flow --}}
                <input type="hidden" name="qualify_on_save" value="{{ old('bank_id') ? '1' : '0' }}">
                @if(old('bank_id'))
                    <input type="hidden" name="bank_id" value="{{ old('bank_id') }}">
                    <input type="hidden" name="bank_branch_id" value="{{ old('bank_branch_id') }}">
                @endif

                {{-- Basic Details --}}
                <h5>Basic Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $lead->customer_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $lead->father_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquire At (Firm) <span class="text-danger">*</span></label>
                            <select name="firm_id" class="form-control" required>
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $lead->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $lead->contact_number) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar_number" class="form-control" value="{{ old('aadhar_number', $lead->aadhar_number) }}" placeholder="12-digit Aadhar Number">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Rented" {{ old('residence_type', $lead->residence_type) == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Own" {{ old('residence_type', $lead->residence_type) == 'Own' ? 'selected' : '' }}>Own</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lead Type <span class="text-danger">*</span></label>
                            <select name="lead_type" id="lead_type" class="form-control" required>
                                <option value="">Select Lead Type</option>
                                <option value="Loan" {{ old('lead_type', $lead->lead_type) == 'Loan' ? 'selected' : '' }}>Loan</option>
                                <option value="RTO" {{ old('lead_type', $lead->lead_type) == 'RTO' ? 'selected' : '' }}>RTO</option>
                                <option value="Insurance" {{ old('lead_type', $lead->lead_type) == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Picture <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <input type="file" name="profile_picture" class="form-control-file">
                            @if($lead->profile_picture_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $lead->profile_picture_path) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Address Details --}}
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address <span class="text-danger">*</span></h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control" value="{{ old('current_address_line', $lead->current_address_line) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input" value="{{ old('current_address_pincode', $lead->current_address_pincode) }}">
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
                        <div class="form-group">
                            <label>Address Proof <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <input type="file" name="current_address_proof" class="form-control-file">
                            @if($lead->current_address_proof_path)
                                <small><a href="{{ asset('storage/' . $lead->current_address_proof_path) }}" target="_blank">View Current Proof</a></small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Permanent Address <span class="text-danger">*</span></h6>
                            <div class="form-group mb-0">
                                <input type="checkbox" id="same_as_current"> <label for="same_as_current" class="mb-0 ml-1">Same as Current Address</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control" value="{{ old('permanent_address_line', $lead->permanent_address_line) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input" value="{{ old('permanent_address_pincode', $lead->permanent_address_pincode) }}">
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
                        <div class="form-group">
                            <label>Address Proof <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <input type="file" name="permanent_address_proof" class="form-control-file">
                            @if($lead->permanent_address_proof_path)
                                <small><a href="{{ asset('storage/' . $lead->permanent_address_proof_path) }}" target="_blank">View Permanent Proof</a></small>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Job Profile --}}
                <div id="job_profile_section">
                    <h5>Job Profile <span class="text-danger">*</span></h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Profile Type</label>
                                <select name="job_profile_type" id="job_profile_type" class="form-control" required>
                                    <option value="">Select Profile</option>
                                    <option value="Self Employed" {{ old('job_profile_type', $lead->job_profile_type) == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                    <option value="Farmer" {{ old('job_profile_type', $lead->job_profile_type) == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                                    <option value="Salaried" {{ old('job_profile_type', $lead->job_profile_type) == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="other_company_name" class="form-control" value="{{ old('other_company_name', $lead->other_company_name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation', $lead->designation) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="nature_of_work_div" style="display:none;">
                            <div class="form-group">
                                <label>Nature of Work</label>
                                <input type="text" name="nature_of_work" class="form-control" value="{{ old('nature_of_work', $lead->nature_of_work) }}">
                            </div>
                        </div>
                        <div class="col-md-4" id="salaried_type_div" style="display:none;">
                            <div class="form-group">
                                <label>Salaried Type</label>
                                <select name="salaried_type" id="salaried_type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="Government" {{ old('salaried_type', $lead->salaried_type) == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Corporate" {{ old('salaried_type', $lead->salaried_type) == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Other" {{ old('salaried_type', $lead->salaried_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Income Proof Available?</label>
                                <select name="income_proof_available" id="income_proof_available" class="form-control">
                                    <option value="0" {{ old('income_proof_available', $lead->income_proof_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('income_proof_available', $lead->income_proof_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div id="itr_details_div" style="display:none;">
                                <div class="form-group">
                                    <label>Last ITR Amount</label>
                                    <input type="number" step="0.01" name="itr_amount" class="form-control" value="{{ old('itr_amount', $lead->itr_amount) }}">
                                </div>
                                <div class="form-group">
                                    <label>ITR Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="itr_document" class="form-control-file">
                                    @if($lead->itr_document_path) <small><a href="{{ asset('storage/' . $lead->itr_document_path) }}" target="_blank">View Current</a></small> @endif
                                </div>
                            </div>
                            <div id="approx_income_div">
                                <div class="form-group">
                                    <label>Approx. Income</label>
                                    <input type="number" step="0.01" name="approx_income" class="form-control" value="{{ old('approx_income', $lead->approx_income) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="business_proof_main_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Proof Available?</label>
                                <select name="business_proof_available" id="business_proof_available" class="form-control">
                                    <option value="0" {{ old('business_proof_available', $lead->business_proof_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('business_proof_available', $lead->business_proof_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div id="business_proof_doc_div" style="display:none;">
                                <div class="form-group">
                                    <label>Business Proof Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="business_proof_document" class="form-control-file">
                                    @if($lead->business_proof_document_path) <small><a href="{{ asset('storage/' . $lead->business_proof_document_path) }}" target="_blank">View Current</a></small> @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Loan Details Section --}}
                <div id="loan_section" style="display:none;">
                    <h5>Loan Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Loan Type Required <span class="text-danger">*</span></label>
                                <select name="loan_type" id="loan_type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="Vehicle Loan" {{ old('loan_type', $lead->loan_type) == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                                    <option value="Housing Loan" {{ old('loan_type', $lead->loan_type) == 'Housing Loan' ? 'selected' : '' }}>Housing Loan</option>
                                    <option value="LAP" {{ old('loan_type', $lead->loan_type) == 'LAP' ? 'selected' : '' }}>Loan Against Property (LAP)</option>
                                    <option value="Business Loan" {{ old('loan_type', $lead->loan_type) == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                                    <option value="Personal Loan" {{ old('loan_type', $lead->loan_type) == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                                    <option value="Other" {{ old('loan_type', $lead->loan_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" id="other_loan_name_div" style="display:none;">
                            <div class="form-group">
                                <label>Other Loan Name</label>
                                <input type="text" name="other_loan_name" class="form-control" value="{{ old('other_loan_name', $lead->other_loan_name) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Loan Specifics --}}
                    <div id="vehicle_loan_details_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control">
                                        <option value="">Select Vehicle Type</option>
                                        <option value="Car" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Car' ? 'selected' : '' }}>Car</option>
                                        <option value="Commercial Vehicle" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                        <option value="Construction Equipment" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                        <option value="Tractor" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                        <option value="Other" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="other_vehicle_loan_name_div" style="display:none;">
                                <div class="form-group">
                                    <label>Other Vehicle Name</label>
                                    <input type="text" name="other_vehicle_loan_name" class="form-control" value="{{ old('other_vehicle_loan_name', $lead->other_vehicle_loan_name) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control">
                                        <option value="">Select Purchase Type</option>
                                        <option value="New Purchase" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'New Purchase' ? 'selected' : '' }}>New Purchase</option>
                                        <option value="Repurchase" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Repurchase' ? 'selected' : '' }}>Repurchase</option>
                                        <option value="Refinance" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Refinance' ? 'selected' : '' }}>Refinance</option>
                                        <option value="Balance Transfer" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- New Purchase --}}
                        <div id="new_purchase_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Manufacturer <span class="text-danger">*</span></label>
                                        <select name="manufacture_id" class="form-control manufacture-select">
                                            <option value="">Select Manufacturer</option>
                                            @foreach($manufactures as $mfg)
                                                <option value="{{ $mfg->id }}" {{ old('manufacture_id', $lead->manufacture_id) == $mfg->id ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Vehicle Name <span class="text-danger">*</span></label>
                                        <select name="vehicle_name_id" class="form-control vehicle-name-select">
                                            <option value="">Select Manufacturer First</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Variant</label>
                                        <select name="vehicle_variant" class="form-control variant-select">
                                            <option value="">Select Vehicle First</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Showroom Price</label>
                                        <input type="number" step="0.01" name="showroom_price" class="form-control" value="{{ old('showroom_price', $lead->showroom_price) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>On Road Price</label>
                                        <input type="number" step="0.01" name="on_road_price" class="form-control" value="{{ old('on_road_price', $lead->on_road_price) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Repurchase/Refinance/BT --}}
                        <div id="repurchase_div" style="display:none;">
                            <h6>RC Details</h6>
                            <div class="row">
                                <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rc_number" class="form-control" value="{{ old('rc_number', $lead->rc_number) }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control" value="{{ old('engine_number', $lead->engine_number) }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control" value="{{ old('chassis_number', $lead->chassis_number) }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" class="form-control" value="{{ old('mfg_year', $lead->mfg_year) }}"></div></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mfg Name</label>
                                        <select name="rto_mfg_name" class="form-control manufacture-select">
                                            <option value="">Select Manufacturer</option>
                                            @foreach($manufactures as $mfg)
                                                <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('rto_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Model Name</label>
                                        <select name="rto_model_name" class="form-control vehicle-name-select">
                                            <option value="">Select Manufacturer First</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Variant</label>
                                        <select name="variant" class="form-control variant-select">
                                            <option value="">Select Vehicle First</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control" value="{{ old('owner_serial_number', $lead->owner_serial_number) }}"></div></div>
                            </div>

                            <h6>Insurance</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Insurance Available?</label>
                                        <select name="insurance_available" id="insurance_available" class="form-control">
                                            <option value="0" {{ old('insurance_available', $lead->insurance_available) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('insurance_available', $lead->insurance_available) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9" id="insurance_details_div" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Vendor</label>
                                                <select name="insurance_id" class="form-control">
                                                    <option value="">Select Vendor</option>
                                                    @foreach($insurances as $ins)
                                                        <option value="{{ $ins->id }}" {{ old('insurance_id', $lead->insurance_id) == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control" value="{{ old('insurance_start_date', $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '') }}"></div></div>
                                        <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control" value="{{ old('insurance_end_date', $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '') }}"></div></div>
                                        <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control" value="{{ old('insurance_amount', $lead->insurance_amount) }}"></div></div>
                                    </div>
                                </div>
                            </div>

                            <h6>Hypothecation (HP)</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>HP?</label>
                                        <select name="hypothecation_status" id="hypothecation_status" class="form-control">
                                            <option value="0" {{ old('hypothecation_status', $lead->hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('hypothecation_status', $lead->hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9" id="hp_details_div" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="old_financer" class="form-control" value="{{ old('old_financer', $lead->old_financer) }}"></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="close_amount" class="form-control" value="{{ old('close_amount', $lead->close_amount) }}"></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="emi_paid_count" class="form-control" value="{{ old('emi_paid_count', $lead->emi_paid_count) }}"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    {{-- Loan Amount Breakdown --}}
                    <div id="amount_required_section">
                        <h5>Amount Required</h5>
                        <div class="row">
                            <div class="col-md-4" id="old_loan_close_div">
                                <div class="form-group">
                                    <label>Old Loan Close Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control" value="{{ old('old_loan_close_amount', $lead->old_loan_close_amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Required In Hand</label>
                                    <input type="number" step="0.01" name="required_in_hand_amount" id="required_in_hand_amount" class="form-control" value="{{ old('required_in_hand_amount', $lead->required_in_hand_amount) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Amount</label>
                                    <input type="text" id="total_loan_amount" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RTO Details Section --}}
                <div id="rto_section" style="display:none;">
                    <h5>RTO Details</h5>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control" value="{{ old('rc_number', $lead->rc_number) }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control" value="{{ old('engine_number', $lead->engine_number) }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control" value="{{ old('chassis_number', $lead->chassis_number) }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="rto_mfg_year" class="form-control" value="{{ old('mfg_year', $lead->mfg_year) }}"></div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mfg Name</label>
                                <select name="rto_mfg_name" class="form-control manufacture-select">
                                    <option value="">Select Manufacturer</option>
                                    @foreach($manufactures as $mfg)
                                        <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('rto_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Model Name</label>
                                <select name="rto_model_name" class="form-control vehicle-name-select">
                                    <option value="">Select Manufacturer First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Variant</label>
                                <select name="rto_variant" class="form-control variant-select">
                                    <option value="">Select Vehicle First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="rto_owner_serial_number" class="form-control" value="{{ old('owner_serial_number', $lead->owner_serial_number) }}"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Insurance Available?</label>
                                <select name="insurance_available" id="rto_insurance_available" class="form-control">
                                    <option value="0" {{ old('insurance_available', $lead->insurance_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('insurance_available', $lead->insurance_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_insurance_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor</label>
                                        <select name="insurance_id" class="form-control">
                                            <option value="">Select Vendor</option>
                                            @foreach($insurances as $ins)
                                                <option value="{{ $ins->id }}" {{ old('insurance_id', $lead->insurance_id) == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control" value="{{ old('insurance_start_date', $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control" value="{{ old('insurance_end_date', $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '') }}"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control" value="{{ old('insurance_amount', $lead->insurance_amount) }}"></div></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="rto_hypothecation_status" id="rto_hypothecation_status" class="form-control">
                                    <option value="0" {{ old('hypothecation_status', $lead->hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('hypothecation_status', $lead->hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_hp_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="rto_old_financer" class="form-control" value="{{ old('old_financer', $lead->old_financer) }}"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="rto_close_amount" class="form-control" value="{{ old('close_amount', $lead->close_amount) }}"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="rto_emi_paid_count" class="form-control" value="{{ old('emi_paid_count', $lead->emi_paid_count) }}"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Insurance Details Section --}}
                <div id="insurance_section" style="display:none;">
                    <h5>Insurance Details</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Manufacturer <span class="text-danger">*</span></label>
                                <select name="ins_mfg_name" class="form-control manufacture-select">
                                    <option value="">Select Manufacturer</option>
                                    @foreach($manufactures as $mfg)
                                        <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('ins_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Model Name <span class="text-danger">*</span></label>
                                <select name="ins_model_name" class="form-control vehicle-name-select">
                                    <option value="">Select Manufacturer First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Variant</label>
                                <select name="ins_variant" class="form-control variant-select">
                                    <option value="">Select Vehicle First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="ins_hypothecation_status" class="form-control">
                                    <option value="0" {{ old('hypothecation_status', $lead->hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('hypothecation_status', $lead->hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Co-Applicant --}}
                <h5>Co-Applicant Details</h5>
                <div class="form-group">
                    <label>Has Co-Applicant?</label>
                    <select name="has_co_applicant" id="has_co_applicant" class="form-control" style="width: 200px;">
                        <option value="0" {{ old('has_co_applicant', $lead->has_co_applicant) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('has_co_applicant', $lead->has_co_applicant) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div id="co_applicant_div" style="display:none;">
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Name</label><input type="text" name="co_name" class="form-control" value="{{ old('co_name', $lead->co_name) }}"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Relation</label><input type="text" name="co_relation" class="form-control" value="{{ old('co_relation', $lead->co_relation) }}"></div></div>
                    </div>
                </div>

                <hr>
                {{-- Guarantor --}}
                <h5>Guarantor Details</h5>
                <div class="form-group">
                    <label>Has Guarantor?</label>
                    <select name="has_guarantor" id="has_guarantor" class="form-control" style="width: 200px;">
                        <option value="0" {{ old('has_guarantor', $lead->has_guarantor) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('has_guarantor', $lead->has_guarantor) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                <div id="guarantor_div" style="display:none;">
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Name</label><input type="text" name="guarantor_name" class="form-control" value="{{ old('guarantor_name', $lead->guarantor_name) }}"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Relation</label><input type="text" name="guarantor_relation" class="form-control" value="{{ old('guarantor_relation', $lead->guarantor_relation) }}"></div></div>
                    </div>
                </div>

                <hr>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Team Lead'))
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assign To <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="assigned_to" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @else
                    <input type="hidden" name="assigned_to" value="{{ $lead->assigned_to }}">
                @endif

                <button type="submit" class="btn btn-primary mt-3">{{ old('bank_id') ? 'Save & Qualify' : 'Save Lead' }}</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- Validation ---
            $('#editLeadForm').validate({
                rules: {
                    father_name: { required: true },
                    email: { required: false, email: true },
                    firm_id: { required: true },
                    residence_type: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    job_profile_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        }
                    },
                    other_company_name: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        }
                    },
                    designation: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        }
                    },
                    itr_amount: {
                        required: function(element) {
                            return $("#income_proof_available").val() == '1';
                        }
                    },
                    approx_income: {
                        required: function(element) {
                            return $("#income_proof_available").val() == '0';
                        }
                    },
                    old_loan_close_amount: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#vehicle_purchase_type").val() !== 'New Purchase';
                        },
                        number: true,
                        min: 0
                    },
                    co_name: {
                        required: function(element) {
                            return $("#has_co_applicant").val() == '1';
                        }
                    },
                    co_relation: {
                        required: function(element) {
                            return $("#has_co_applicant").val() == '1';
                        }
                    },
                    guarantor_name: {
                        required: function(element) {
                            return $("#has_guarantor").val() == '1';
                        }
                    },
                    guarantor_relation: {
                        required: function(element) {
                            return $("#has_guarantor").val() == '1';
                        }
                    },
                    assigned_to: {
                        required: function(element) {
                            return $('#assigned_to').length > 0;
                        }
                    },
                    mfg_year: {
                        digits: true
                    },
                    // Loan Specific
                    loan_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        }
                    },
                    vehicle_loan_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type").val() === 'Vehicle Loan';
                        }
                    },
                    vehicle_purchase_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type").val() === 'Vehicle Loan';
                        }
                    },
                    manufacture_id: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type").val() === 'Vehicle Loan' && $("#vehicle_purchase_type").val() === 'New Purchase';
                        }
                    },
                    rc_number: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type").val() === 'Vehicle Loan' && ['Repurchase', 'Refinance', 'Balance Transfer'].includes($("#vehicle_purchase_type").val());
                        }
                    },
                    // RTO Specific
                    rto_rc_number: {
                        required: function(element) {
                            return $("#lead_type").val() === 'RTO';
                        }
                    },
                    // Insurance Specific
                    ins_model_name: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Insurance';
                        }
                    }
                },
                messages: {
                    father_name: "Please enter the father's name",
                    email: {
                        email: "Please enter a valid email address"
                    },
                    firm_id: "Please select a firm",
                    residence_type: "Please select a residence type",
                    current_address_line: "Please enter the current address",
                    permanent_address_line: "Please enter the permanent address",
                    job_profile_type: "Please select a job profile",
                    other_company_name: "Please enter the company name",
                    designation: "Please enter the designation",
                    itr_amount: "Please enter the ITR amount",
                    approx_income: "Please enter the approximate income",
                    old_loan_close_amount: {
                        required: "Please enter the old loan close amount",
                        number: "Please enter a valid number",
                        min: "Amount cannot be negative"
                    },
                    co_name: "Please enter the co-applicant's name",
                    co_relation: "Please enter the co-applicant's relation",
                    guarantor_name: "Please enter the guarantor's name",
                    guarantor_relation: "Please enter the guarantor's relation",
                    assigned_to: "Please select a user to assign the lead to",
                    mfg_year: {
                        digits: "Please enter only digits"
                    },
                    loan_type: "Please select a loan type",
                    vehicle_loan_type: "Please select a vehicle loan type",
                    vehicle_purchase_type: "Please select a purchase type",
                    manufacture_id: "Please select a manufacturer",
                    rc_number: "Please enter the RC number",
                    rto_rc_number: "Please enter the RC number",
                    ins_model_name: "Please enter the model name"
                }
            });

            // --- Location Scripts ---
            function populateStates(selector, selectedState = null) {
                let stateSelects = $(selector);
                stateSelects.empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        stateSelects.each(function() {
                            let currentDropdown = $(this);
                            let preselectedValue = currentDropdown.data('selected');
                            currentDropdown.empty().append('<option value="">Select State</option>');
                            $.each(data, function(key, state) {
                                let option = $('<option></option>').attr('value', state.id).text(state.name);
                                if (state.id == preselectedValue) {
                                    option.prop('selected', true);
                                }
                                currentDropdown.append(option);
                            });
                            currentDropdown.trigger('change');
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
                        url: '{{ url("get-cities") }}/' + stateId,
                        type: 'GET',
                        success: function(data) {
                            $(cityElement).empty().append('<option value="">Select City</option>');
                            $.each(data, function(key, city) {
                                let option = $('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode);
                                let cityToSelect = selectedCity || $(cityElement).data('selected');
                                if (city.id == cityToSelect) {
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

            // Set initial selected data
            $('#current_state').data('selected', '{{ old('current_address_state', $lead->current_address_state) }}');
            $('#current_city').data('selected', '{{ old('current_address_city', $lead->current_address_city) }}');
            $('#permanent_state').data('selected', '{{ old('permanent_address_state', $lead->permanent_address_state) }}');
            $('#permanent_city').data('selected', '{{ old('permanent_address_city', $lead->permanent_address_city) }}');

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

            $('#same_as_current').change(function() {
                if ($(this).is(':checked')) {
                    $('#permanent_address_line').val($('#current_address_line').val());
                    $('#permanent_pincode').val($('#current_pincode').val());
                    let currentStateVal = $('#current_state').val();
                    let currentCityVal = $('#current_city').val();

                    // Set the permanent state dropdown value
                    $('#permanent_state').val(currentStateVal);

                    // Trigger change to load cities, but pass the current city value to be selected
                    populateCities('#permanent_state', '#permanent_city', '#permanent_pincode', currentCityVal);
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Vehicle Logic ---
            function populateVehicleNames(manufactureId, container, selectedVehicleId = null) {
                let vehicleSelect = container.find('.vehicle-name-select');
                vehicleSelect.empty().append('<option value="">Loading...</option>');

                if (manufactureId) {
                    $.ajax({
                        url: '{{ url("get-vehicle-names") }}/' + manufactureId,
                        type: 'GET',
                        success: function(data) {
                            vehicleSelect.empty().append('<option value="">Select Vehicle</option>');
                            $.each(data, function(key, vehicle) {
                                let val = vehicleSelect.attr('name') === 'vehicle_name_id' ? vehicle.id : vehicle.name;
                                let option = $('<option></option>').attr('value', val).text(vehicle.name).data('id', vehicle.id);
                                if (val == selectedVehicleId) {
                                    option.prop('selected', true);
                                }
                                vehicleSelect.append(option);
                            });
                            // Trigger change to load variants if a vehicle is pre-selected
                            if(selectedVehicleId) {
                                vehicleSelect.trigger('change');
                            }
                        }
                    });
                } else {
                    vehicleSelect.empty().append('<option value="">Select Manufacturer First</option>');
                }
            }

            function populateVariants(vehicleName, container, selectedVariant = null) {
                let variantSelect = container.find('.variant-select');
                variantSelect.empty().append('<option value="">Loading...</option>');

                if (vehicleName) {
                    $.ajax({
                        url: '{{ route("get-variants") }}',
                        type: 'GET',
                        data: { vehicleName: vehicleName },
                        success: function(data) {
                            variantSelect.empty().append('<option value="">Select Variant</option>');
                            $.each(data, function(key, item) {
                                let option = $('<option></option>').attr('value', item.variant).text(item.variant);
                                if (item.variant == selectedVariant) {
                                    option.prop('selected', true);
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
                let mfgId = $(this).find(':selected').data('id') || $(this).val();
                let container = $(this).closest('.row');
                populateVehicleNames(mfgId, container);
            });

            $(document).on('change', '.vehicle-name-select', function() {
                let vehicleName = $(this).val();
                let container = $(this).closest('.row');

                // For New Purchase, we need the ID for variants if we use the ID-based API,
                // but since we're using get-variants by name, this is fine.
                let variantToSelect = container.find('.variant-select').data('selected');
                populateVariants(vehicleName, container, variantToSelect);
            });

            // Initial population for Edit mode
            $('.manufacture-select').each(function() {
                let mfgId = $(this).find(':selected').data('id') || $(this).val();
                if(mfgId) {
                    let container = $(this).closest('.row');
                    let vehicleSelect = container.find('.vehicle-name-select');
                    let selectedVehicle = vehicleSelect.attr('name') === 'vehicle_name_id' ? '{{ $lead->vehicle_name_id }}' : (vehicleSelect.attr('name') === 'rto_model_name' ? '{{ $lead->model_name }}' : '{{ $lead->model_name }}');

                    // Store the variant to be selected later
                    let variantSelect = container.find('.variant-select');
                    let selectedVariant = variantSelect.attr('name') === 'vehicle_variant' ? '{{ $lead->vehicle_variant }}' : (variantSelect.attr('name') === 'rto_variant' ? '{{ $lead->variant }}' : '{{ $lead->variant }}');
                    variantSelect.data('selected', selectedVariant);

                    populateVehicleNames(mfgId, container, selectedVehicle);
                }
            });

            // --- Lead Type Logic ---
            $('#lead_type').change(function() {
                let val = $(this).val();
                $('#loan_section, #rto_section, #insurance_section, #job_profile_section').hide();
                // Disable inputs in hidden sections to prevent submission
                $('#loan_section input, #loan_section select, #rto_section input, #rto_section select, #insurance_section input, #insurance_section select, #job_profile_section input, #job_profile_section select').prop('disabled', true);

                if (val === 'Loan') {
                    $('#loan_section, #job_profile_section').show();
                    $('#loan_section input, #loan_section select, #job_profile_section input, #job_profile_section select').prop('disabled', false);
                } else if (val === 'RTO') {
                    $('#rto_section').show();
                    $('#rto_section input, #rto_section select').prop('disabled', false);
                } else if (val === 'Insurance') {
                    $('#insurance_section').show();
                    $('#insurance_section input, #insurance_section select').prop('disabled', false);
                }
            }).trigger('change');

            // --- Other Form Logic ---
            $('#job_profile_type').change(function() {
                let val = $(this).val();
                $('#nature_of_work_div, #salaried_type_div, #business_proof_main_div').hide();
                if (val === 'Self Employed') {
                    $('#nature_of_work_div, #business_proof_main_div').show();
                } else if (val === 'Salaried') {
                    $('#salaried_type_div').show();
                }
            }).trigger('change');

            $('#income_proof_available').change(function() {
                $('#itr_details_div').toggle($(this).val() == '1');
                $('#approx_income_div').toggle($(this).val() == '0');
            }).trigger('change');

            $('#business_proof_available').change(function() {
                $('#business_proof_doc_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#loan_type').change(function() {
                $('#other_loan_name_div').toggle($(this).val() === 'Other');
                $('#vehicle_loan_details_div').toggle($(this).val() === 'Vehicle Loan');
            }).trigger('change');

            $('#vehicle_loan_type').change(function() {
                $('#other_vehicle_loan_name_div').toggle($(this).val() === 'Other');
            }).trigger('change');

            $('#vehicle_purchase_type').change(function() {
                let val = $(this).val();
                $('#new_purchase_div').toggle(val === 'New Purchase');
                $('#repurchase_div').toggle(['Repurchase', 'Refinance', 'Balance Transfer'].includes(val));

                // Hide Old Loan Close Amount if New Purchase
                $('#old_loan_close_div').toggle(val !== 'New Purchase');
                if(val === 'New Purchase') {
                    $('#old_loan_close_amount').val(0).prop('disabled', true);
                } else {
                    $('#old_loan_close_amount').prop('disabled', false);
                }
                calculateTotal();
            }).trigger('change');

            @if($lead->insurance_available) $('#insurance_details_div').show(); @endif
            $('#insurance_available').change(function() {
                $('#insurance_details_div').toggle($(this).val() == '1');
            });

            @if($lead->hypothecation_status) $('#hp_details_div').show(); @endif
            $('#hypothecation_status').change(function() {
                $('#hp_details_div').toggle($(this).val() == '1');
            });

            $('#rto_insurance_available').change(function() {
                $('#rto_insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#rto_hypothecation_status').change(function() {
                $('#rto_hp_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#has_co_applicant').change(function() {
                $('#co_applicant_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#has_guarantor').change(function() {
                $('#guarantor_div').toggle($(this).val() == '1');
            }).trigger('change');

            function calculateTotal() {
                let oldLoan = parseFloat($('#old_loan_close_amount').val()) || 0;
                let inHand = parseFloat($('#required_in_hand_amount').val()) || 0;
                $('#total_loan_amount').val((oldLoan + inHand).toFixed(2));
            }

            $('#old_loan_close_amount, #required_in_hand_amount').on('input', calculateTotal);

            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });

            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop : 0}, 800);
                return false;
            });
        });
    </script>
@stop
