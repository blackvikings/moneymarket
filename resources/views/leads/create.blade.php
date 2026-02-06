@extends('adminlte::page')

@section('title', 'Create Lead')

@section('content_header')
    <h1>Create Lead</h1>
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
            <form action="{{ route(get_route_prefix() . 'leads.store') }}" method="POST" enctype="multipart/form-data" id="createLeadForm">
                @csrf

                {{-- Basic Details --}}
                <h5>Basic Details</h5>
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
                            <label>Enquire At (Firm) <span class="text-danger">*</span></label>
                            <select name="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                            @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control @error('residence_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="Rented" {{ old('residence_type') == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Own" {{ old('residence_type') == 'Own' ? 'selected' : '' }}>Own</option>
                            </select>
                            @error('residence_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Lead Type <span class="text-danger">*</span></label>
                            <select name="lead_type" id="lead_type" class="form-control @error('lead_type') is-invalid @enderror" required>
                                <option value="">Select Lead Type</option>
                                <option value="Loan" {{ old('lead_type') == 'Loan' ? 'selected' : '' }}>Loan</option>
                                <option value="RTO" {{ old('lead_type') == 'RTO' ? 'selected' : '' }}>RTO</option>
                                <option value="Insurance" {{ old('lead_type') == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                            @error('lead_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control-file @error('profile_picture') is-invalid @enderror">
                            @error('profile_picture') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control @error('current_address_line') is-invalid @enderror" value="{{ old('current_address_line') }}" required>
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
                                    <select name="current_address_state" id="current_state" class="form-control state-select @error('current_address_state') is-invalid @enderror" required>
                                        <option value="">Select State</option>
                                    </select>
                                    @error('current_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="current_address_city" id="current_city" class="form-control city-select @error('current_address_city') is-invalid @enderror" required>
                                        <option value="">Select State First</option>
                                    </select>
                                    @error('current_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Proof <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <input type="file" name="current_address_proof" class="form-control-file">
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
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control @error('permanent_address_line') is-invalid @enderror" value="{{ old('permanent_address_line') }}" required>
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
                                    <select name="permanent_address_state" id="permanent_state" class="form-control state-select @error('permanent_address_state') is-invalid @enderror" required>
                                        <option value="">Select State</option>
                                    </select>
                                    @error('permanent_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="permanent_address_city" id="permanent_city" class="form-control city-select @error('permanent_address_city') is-invalid @enderror" required>
                                        <option value="">Select State First</option>
                                    </select>
                                    @error('permanent_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        {{-- Removed: Permanent Address Proof File Input --}}
                    </div>
                </div>

                <hr>
                {{-- Job Profile --}}
                <h5>Job Profile <span class="text-danger">*</span></h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Type</label>
                            <select name="job_profile_type" id="job_profile_type" class="form-control" required>
                                <option value="">Select Profile</option>
                                <option value="Self Employed" {{ old('job_profile_type') == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                <option value="Farmer" {{ old('job_profile_type') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                                <option value="Salaried" {{ old('job_profile_type') == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="nature_of_work_div" style="display:none;">
                        <div class="form-group">
                            <label>Nature of Work</label>
                            <input type="text" name="nature_of_work" class="form-control" value="{{ old('nature_of_work') }}">
                        </div>
                    </div>
                    <div class="col-md-4" id="salaried_type_div" style="display:none;">
                        <div class="form-group">
                            <label>Salaried Type</label>
                            <select name="salaried_type" id="salaried_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="Government" {{ old('salaried_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                <option value="Corporate" {{ old('salaried_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                <option value="Other" {{ old('salaried_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="other_company_name_div" style="display:none;">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="other_company_name" class="form-control" value="{{ old('other_company_name') }}">
                        </div>
                    </div>
                    <div class="col-md-4" id="designation_div" style="display:none;">
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Income Proof Available?</label>
                            <select name="income_proof_available" id="income_proof_available" class="form-control">
                                <option value="0" {{ old('income_proof_available') == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('income_proof_available') == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div id="itr_details_div" style="display:none;">
                            <div class="form-group">
                                <label>Last ITR Amount</label>
                                <input type="number" step="0.01" name="itr_amount" class="form-control" value="{{ old('itr_amount') }}">
                            </div>
                            <div class="form-group">
                                <label>ITR Document</label>
                                <input type="file" name="itr_document" class="form-control-file">
                            </div>
                        </div>
                        <div id="approx_income_div">
                            <div class="form-group">
                                <label>Approx. Income</label>
                                <input type="number" step="0.01" name="approx_income" class="form-control" value="{{ old('approx_income') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="business_proof_main_div" style="display:none;">
                        <div class="form-group">
                            <label>Business Proof Available?</label>
                            <select name="business_proof_available" id="business_proof_available" class="form-control">
                                <option value="0" {{ old('business_proof_available') == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('business_proof_available') == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div id="business_proof_doc_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Proof Document</label>
                                <input type="file" name="business_proof_document" class="form-control-file">
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
                                    <option value="Vehicle Loan" {{ old('loan_type') == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                                    <option value="Housing Loan" {{ old('loan_type') == 'Housing Loan' ? 'selected' : '' }}>Housing Loan</option>
                                    <option value="LAP" {{ old('loan_type') == 'LAP' ? 'selected' : '' }}>Loan Against Property (LAP)</option>
                                    <option value="Business Loan" {{ old('loan_type') == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                                    <option value="Personal Loan" {{ old('loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                                    <option value="Other" {{ old('loan_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" id="other_loan_name_div" style="display:none;">
                            <div class="form-group">
                                <label>Other Loan Name</label>
                                <input type="text" name="other_loan_name" class="form-control" value="{{ old('other_loan_name') }}">
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
                                        <option value="Car" {{ old('vehicle_loan_type') == 'Car' ? 'selected' : '' }}>Car</option>
                                        <option value="Commercial Vehicle" {{ old('vehicle_loan_type') == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                        <option value="Construction Equipment" {{ old('vehicle_loan_type') == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                        <option value="Tractor" {{ old('vehicle_loan_type') == 'Tractor' ? 'selected' : '' }}>Tractor</option>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control">
                                        <option value="">Select Purchase Type</option>
                                        <option value="New Purchase" {{ old('vehicle_purchase_type') == 'New Purchase' ? 'selected' : '' }}>New Purchase</option>
                                        <option value="Repurchase" {{ old('vehicle_purchase_type') == 'Repurchase' ? 'selected' : '' }}>Repurchase</option>
                                        <option value="Refinance" {{ old('vehicle_purchase_type') == 'Refinance' ? 'selected' : '' }}>Refinance</option>
                                        <option value="Balance Transfer" {{ old('vehicle_purchase_type') == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer</option>
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
                                        <select name="manufacture_id" class="form-control">
                                            <option value="">Select Manufacturer</option>
                                            @foreach($manufactures as $mfg)
                                                <option value="{{ $mfg->id }}" {{ old('manufacture_id') == $mfg->id ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Vehicle Name</label>
                                        <input type="text" name="vehicle_variant" class="form-control" placeholder="Variant/Model" value="{{ old('vehicle_variant') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Showroom Price</label>
                                        <input type="number" step="0.01" name="showroom_price" class="form-control" value="{{ old('showroom_price') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>On Road Price</label>
                                        <input type="number" step="0.01" name="on_road_price" class="form-control" value="{{ old('on_road_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Repurchase/Refinance/BT --}}
                        <div id="repurchase_div" style="display:none;">
                            <h6>RC Details</h6>
                            <div class="row">
                                <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rc_number" class="form-control" value="{{ old('rc_number') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control" value="{{ old('engine_number') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control" value="{{ old('chassis_number') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" class="form-control" value="{{ old('mfg_year') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Mfg Name</label><input type="text" name="mfg_name" class="form-control" value="{{ old('mfg_name') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Model Name</label><input type="text" name="model_name" class="form-control" value="{{ old('model_name') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Variant</label><input type="text" name="variant" class="form-control" value="{{ old('variant') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control" value="{{ old('owner_serial_number') }}"></div></div>
                            </div>

                            <h6>Insurance</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Insurance Available?</label>
                                        <select name="insurance_available" id="insurance_available" class="form-control">
                                            <option value="0" {{ old('insurance_available') == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('insurance_available') == 1 ? 'selected' : '' }}>Yes</option>
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
                                                        <option value="{{ $ins->id }}" {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control" value="{{ old('insurance_start_date') }}"></div></div>
                                        <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control" value="{{ old('insurance_end_date') }}"></div></div>
                                        <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control" value="{{ old('insurance_amount') }}"></div></div>
                                    </div>
                                </div>
                            </div>

                            <h6>Hypothecation (HP)</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>HP?</label>
                                        <select name="hypothecation_status" id="hypothecation_status" class="form-control">
                                            <option value="0" {{ old('hypothecation_status') == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('hypothecation_status') == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9" id="hp_details_div" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="old_financer" class="form-control" value="{{ old('old_financer') }}"></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="close_amount" class="form-control" value="{{ old('close_amount') }}"></div></div>
                                        <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="emi_paid_count" class="form-control" value="{{ old('emi_paid_count') }}"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    {{-- Loan Amount Breakdown --}}
                    <h5>Amount Required</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Old Loan Close Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control" value="{{ old('old_loan_close_amount') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Required In Hand</label>
                                <input type="number" step="0.01" name="required_in_hand_amount" id="required_in_hand_amount" class="form-control" value="{{ old('required_in_hand_amount') }}">
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

                {{-- RTO Details Section --}}
                <div id="rto_section" style="display:none;">
                    <h5>RTO Details</h5>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control" value="{{ old('rc_number') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control" value="{{ old('engine_number') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control" value="{{ old('chassis_number') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="rto_mfg_year" class="form-control" value="{{ old('mfg_year') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Name</label><input type="text" name="rto_mfg_name" class="form-control" value="{{ old('mfg_name') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Model Name</label><input type="text" name="rto_model_name" class="form-control" value="{{ old('model_name') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Variant</label><input type="text" name="rto_variant" class="form-control" value="{{ old('variant') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="rto_owner_serial_number" class="form-control" value="{{ old('owner_serial_number') }}"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Insurance Available?</label>
                                <select name="insurance_available" id="rto_insurance_available" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
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
                                                <option value="{{ $ins->id }}" {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control" value="{{ old('insurance_start_date') }}"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control" value="{{ old('insurance_end_date') }}"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control" value="{{ old('insurance_amount') }}"></div></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="rto_hypothecation_status" id="rto_hypothecation_status" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_hp_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="rto_old_financer" class="form-control" value="{{ old('old_financer') }}"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="rto_close_amount" class="form-control" value="{{ old('close_amount') }}"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="rto_emi_paid_count" class="form-control" value="{{ old('emi_paid_count') }}"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Insurance Details Section --}}
                <div id="insurance_section" style="display:none;">
                    <h5>Insurance Details</h5>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Model Name <span class="text-danger">*</span></label><input type="text" name="ins_model_name" class="form-control" value="{{ old('model_name') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Variant</label><input type="text" name="ins_variant" class="form-control" value="{{ old('variant') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="ins_owner_serial_number" class="form-control" value="{{ old('owner_serial_number') }}"></div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="ins_hypothecation_status" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Team Lead'))
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assign To</label>
                            <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @else
                    <input type="hidden" name="assigned_to" value="{{ auth()->id() }}">
                @endif

                <button type="submit" class="btn btn-primary mt-3">Create Lead</button>
            </form>
        </div>
    </div>

    {{-- Back to Top Button --}}
    <button id="back-to-top" class="btn btn-primary" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <i class="fas fa-arrow-up"></i>
    </button>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize validation
            $('#createLeadForm').validate({
                rules: {
                    customer_name: { required: true },
                    father_name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
                    email: { required: false, email: true },
                    firm_id: { required: true },
                    residence_type: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    lead_type: { required: true },
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
                    old_loan_close_amount: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        },
                        number: true,
                        min: 0
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
                    customer_name: "Please enter the customer's name",
                    father_name: "Please enter the father's name",
                    contact_number: {
                        required: "Please enter a mobile number",
                        number: "Please enter a valid number",
                        minlength: "Mobile number must be 10 digits",
                        maxlength: "Mobile number must be 10 digits"
                    },
                    email: {
                        email: "Please enter a valid email address"
                    },
                    firm_id: "Please select a firm",
                    residence_type: "Please select a residence type",
                    current_address_line: "Please enter the current address",
                    permanent_address_line: "Please enter the permanent address",
                    lead_type: "Please select a lead type",
                    loan_type: "Please select a loan type",
                    vehicle_loan_type: "Please select a vehicle loan type",
                    vehicle_purchase_type: "Please select a purchase type",
                    manufacture_id: "Please select a manufacturer",
                    rc_number: "Please enter the RC number",
                    old_loan_close_amount: {
                        required: "Please enter the old loan close amount",
                        number: "Please enter a valid number",
                        min: "Amount cannot be negative"
                    },
                    rto_rc_number: "Please enter the RC number",
                    ins_model_name: "Please enter the model name"
                }
            });

            // --- Location Scripts ---
            function populateStates(selector) {
                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        let stateSelects = $(selector);
                        stateSelects.empty().append('<option value="">Select State</option>');
                        $.each(data, function(key, state) {
                            stateSelects.append('<option value="' + state.id + '">' + state.name + '</option>');
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
                    populateCities('#permanent_state', '#permanent_city', '#permanent_pincode', $('#current_city').val());
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Lead Type Logic ---
            $('#lead_type').change(function() {
                let val = $(this).val();
                $('#loan_section, #rto_section, #insurance_section').hide();
                // Disable inputs in hidden sections to prevent submission
                $('#loan_section input, #loan_section select, #rto_section input, #rto_section select, #insurance_section input, #insurance_section select').prop('disabled', true);

                if (val === 'Loan') {
                    $('#loan_section').show();
                    $('#loan_section input, #loan_section select').prop('disabled', false);
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
                $('#nature_of_work_div, #salaried_type_div, #other_company_name_div, #designation_div, #business_proof_main_div').hide();
                if (val === 'Self Employed') {
                    $('#nature_of_work_div, #business_proof_main_div').show();
                } else if (val === 'Salaried') {
                    $('#salaried_type_div, #designation_div').show();
                }
            }).trigger('change');

            $('#salaried_type').change(function() {
                $('#other_company_name_div').toggle($(this).val() === 'Other');
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
            }).trigger('change');

            $('#insurance_available').change(function() {
                $('#insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#rto_insurance_available').change(function() {
                $('#rto_insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#hypothecation_status').change(function() {
                $('#hp_details_div').toggle($(this).val() == '1');
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
