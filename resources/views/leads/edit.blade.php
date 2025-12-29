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
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('leads.update', $lead->id) }}" method="POST" enctype="multipart/form-data" id="editLeadForm">
                @csrf
                @method('PUT')

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
                            <label>Father Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $lead->father_name) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquire At (Firm)</label>
                            <select name="firm_id" class="form-control">
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
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type</label>
                            <select name="residence_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="Rented" {{ old('residence_type', $lead->residence_type) == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Own" {{ old('residence_type', $lead->residence_type) == 'Own' ? 'selected' : '' }}>Own</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Address Details --}}
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" class="form-control" value="{{ old('current_address_line', $lead->current_address_line) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" class="form-control" value="{{ old('current_address_pincode', $lead->current_address_pincode) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="current_address_city" class="form-control" value="{{ old('current_address_city', $lead->current_address_city) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="current_address_state" class="form-control" value="{{ old('current_address_state', $lead->current_address_state) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" class="form-control" value="{{ old('permanent_address_line', $lead->permanent_address_line) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" class="form-control" value="{{ old('permanent_address_pincode', $lead->permanent_address_pincode) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="permanent_address_city" class="form-control" value="{{ old('permanent_address_city', $lead->permanent_address_city) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="permanent_address_state" class="form-control" value="{{ old('permanent_address_state', $lead->permanent_address_state) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Job Profile --}}
                <h5>Job Profile</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Type</label>
                            <select name="job_profile_type" id="job_profile_type" class="form-control">
                                <option value="">Select Profile</option>
                                <option value="Self Employed" {{ old('job_profile_type', $lead->job_profile_type) == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                <option value="Farmer" {{ old('job_profile_type', $lead->job_profile_type) == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                                <option value="Salaried" {{ old('job_profile_type', $lead->job_profile_type) == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="nature_of_work_div" style="{{ $lead->job_profile_type == 'Self Employed' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Nature of Work</label>
                            <input type="text" name="nature_of_work" class="form-control" value="{{ old('nature_of_work', $lead->nature_of_work) }}">
                        </div>
                    </div>
                    <div class="col-md-4" id="salaried_type_div" style="{{ $lead->job_profile_type == 'Salaried' ? '' : 'display:none;' }}">
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
                    <div class="col-md-4" id="other_company_name_div" style="{{ $lead->salaried_type == 'Other' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="other_company_name" class="form-control" value="{{ old('other_company_name', $lead->other_company_name) }}">
                        </div>
                    </div>
                    <div class="col-md-4" id="designation_div" style="{{ $lead->job_profile_type == 'Salaried' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $lead->designation) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Income Proof Available?</label>
                            <select name="income_proof_available" id="income_proof_available" class="form-control">
                                <option value="0" {{ $lead->income_proof_available == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $lead->income_proof_available == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div id="itr_details_div" style="{{ $lead->income_proof_available == 1 ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label>Last ITR Amount</label>
                                <input type="number" step="0.01" name="itr_amount" class="form-control" value="{{ old('itr_amount', $lead->itr_amount) }}">
                            </div>
                            <div class="form-group">
                                <label>ITR Document</label>
                                <input type="file" name="itr_document" class="form-control-file">
                                @if($lead->itr_document_path)
                                    <small><a href="{{ asset('storage/' . $lead->itr_document_path) }}" target="_blank">View Current Document</a></small>
                                @endif
                            </div>
                        </div>
                        <div id="approx_income_div" style="{{ $lead->income_proof_available == 0 ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label>Approx. Income</label>
                                <input type="number" step="0.01" name="approx_income" class="form-control" value="{{ old('approx_income', $lead->approx_income) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="business_proof_main_div" style="{{ $lead->job_profile_type == 'Self Employed' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Business Proof Available?</label>
                            <select name="business_proof_available" id="business_proof_available" class="form-control">
                                <option value="0" {{ $lead->business_proof_available == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $lead->business_proof_available == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div id="business_proof_doc_div" style="{{ $lead->business_proof_available == 1 ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label>Business Proof Document</label>
                                <input type="file" name="business_proof_document" class="form-control-file">
                                @if($lead->business_proof_document_path)
                                    <small><a href="{{ asset('storage/' . $lead->business_proof_document_path) }}" target="_blank">View Current Document</a></small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Loan Details --}}
                <h5>Loan Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Loan Type Required</label>
                            <select name="loan_type" id="loan_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="Vehicle Loan" {{ $lead->loan_type == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                                <option value="Housing Loan" {{ $lead->loan_type == 'Housing Loan' ? 'selected' : '' }}>Housing Loan</option>
                                <option value="LAP" {{ $lead->loan_type == 'LAP' ? 'selected' : '' }}>Loan Against Property (LAP)</option>
                                <option value="Business Loan" {{ $lead->loan_type == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                                <option value="Personal Loan" {{ $lead->loan_type == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                                <option value="Other" {{ $lead->loan_type == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="other_loan_name_div" style="{{ $lead->loan_type == 'Other' ? '' : 'display:none;' }}">
                        <div class="form-group">
                            <label>Other Loan Name</label>
                            <input type="text" name="other_loan_name" class="form-control" value="{{ old('other_loan_name', $lead->other_loan_name) }}">
                        </div>
                    </div>
                </div>

                {{-- Vehicle Loan Specifics --}}
                <div id="vehicle_loan_details_div" style="{{ $lead->loan_type == 'Vehicle Loan' ? '' : 'display:none;' }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vehicle Type</label>
                                <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control">
                                    <option value="">Select Vehicle Type</option>
                                    <option value="Car" {{ $lead->vehicle_loan_type == 'Car' ? 'selected' : '' }}>Car</option>
                                    <option value="Commercial Vehicle" {{ $lead->vehicle_loan_type == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                    <option value="Construction Equipment" {{ $lead->vehicle_loan_type == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                    <option value="Tractor" {{ $lead->vehicle_loan_type == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                    <option value="Other" {{ $lead->vehicle_loan_type == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" id="other_vehicle_loan_name_div" style="{{ $lead->vehicle_loan_type == 'Other' ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label>Other Vehicle Name</label>
                                <input type="text" name="other_vehicle_loan_name" class="form-control" value="{{ old('other_vehicle_loan_name', $lead->other_vehicle_loan_name) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Type</label>
                                <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control">
                                    <option value="">Select Purchase Type</option>
                                    <option value="New Purchase" {{ $lead->vehicle_purchase_type == 'New Purchase' ? 'selected' : '' }}>New Purchase</option>
                                    <option value="Repurchase" {{ $lead->vehicle_purchase_type == 'Repurchase' ? 'selected' : '' }}>Repurchase</option>
                                    <option value="Refinance" {{ $lead->vehicle_purchase_type == 'Refinance' ? 'selected' : '' }}>Refinance</option>
                                    <option value="Balance Transfer" {{ $lead->vehicle_purchase_type == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- New Purchase --}}
                    <div id="new_purchase_div" style="{{ $lead->vehicle_purchase_type == 'New Purchase' ? '' : 'display:none;' }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Manufacturer</label>
                                    <select name="manufacture_id" class="form-control">
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufactures as $mfg)
                                            <option value="{{ $mfg->id }}" {{ $lead->manufacture_id == $mfg->id ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehicle Name</label>
                                    <input type="text" name="vehicle_variant" class="form-control" placeholder="Variant/Model" value="{{ old('vehicle_variant', $lead->vehicle_variant) }}">
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
                    <div id="repurchase_div" style="{{ in_array($lead->vehicle_purchase_type, ['Repurchase', 'Refinance', 'Balance Transfer']) ? '' : 'display:none;' }}">
                        <h6>RC Details</h6>
                        <div class="row">
                            <div class="col-md-3"><div class="form-group"><label>RC Number</label><input type="text" name="rc_number" class="form-control" value="{{ old('rc_number', $lead->rc_number) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control" value="{{ old('engine_number', $lead->engine_number) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control" value="{{ old('chassis_number', $lead->chassis_number) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" class="form-control" value="{{ old('mfg_year', $lead->mfg_year) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Mfg Name</label><input type="text" name="mfg_name" class="form-control" value="{{ old('mfg_name', $lead->mfg_name) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Model Name</label><input type="text" name="model_name" class="form-control" value="{{ old('model_name', $lead->model_name) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Variant</label><input type="text" name="variant" class="form-control" value="{{ old('variant', $lead->variant) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control" value="{{ old('owner_serial_number', $lead->owner_serial_number) }}"></div></div>
                        </div>

                        <h6>Insurance</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Insurance Available?</label>
                                    <select name="insurance_available" id="insurance_available" class="form-control">
                                        <option value="0" {{ $lead->insurance_available == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $lead->insurance_available == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9" id="insurance_details_div" style="{{ $lead->insurance_available == 1 ? '' : 'display:none;' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor</label>
                                            <select name="insurance_id" class="form-control">
                                                <option value="">Select Vendor</option>
                                                @foreach($insurances as $ins)
                                                    <option value="{{ $ins->id }}" {{ $lead->insurance_id == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control" value="{{ $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '' }}"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control" value="{{ $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '' }}"></div></div>
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
                                        <option value="0" {{ $lead->hypothecation_status == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $lead->hypothecation_status == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9" id="hp_details_div" style="{{ $lead->hypothecation_status == 1 ? '' : 'display:none;' }}">
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
                {{-- References --}}
                <h5>References (Optional)</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Reference 1</h6>
                        <div class="form-group"><label>Name</label><input type="text" name="ref1_name" class="form-control" value="{{ old('ref1_name', $lead->ref1_name) }}"></div>
                        <div class="form-group"><label>Mobile</label><input type="text" name="ref1_mobile" class="form-control" value="{{ old('ref1_mobile', $lead->ref1_mobile) }}"></div>
                        <div class="form-group"><label>Email</label><input type="email" name="ref1_email" class="form-control" value="{{ old('ref1_email', $lead->ref1_email) }}"></div>
                        <div class="form-group"><label>Address</label><textarea name="ref1_address" class="form-control" rows="1">{{ old('ref1_address', $lead->ref1_address) }}</textarea></div>
                    </div>
                    <div class="col-md-6">
                        <h6>Reference 2</h6>
                        <div class="form-group"><label>Name</label><input type="text" name="ref2_name" class="form-control" value="{{ old('ref2_name', $lead->ref2_name) }}"></div>
                        <div class="form-group"><label>Mobile</label><input type="text" name="ref2_mobile" class="form-control" value="{{ old('ref2_mobile', $lead->ref2_mobile) }}"></div>
                        <div class="form-group"><label>Email</label><input type="email" name="ref2_email" class="form-control" value="{{ old('ref2_email', $lead->ref2_email) }}"></div>
                        <div class="form-group"><label>Address</label><textarea name="ref2_address" class="form-control" rows="1">{{ old('ref2_address', $lead->ref2_address) }}</textarea></div>
                    </div>
                </div>

                <hr>
                {{-- Loan Amount Breakdown --}}
                <h5>Amount Required</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Old Loan Close Amount</label>
                            <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control" value="{{ old('old_loan_close_amount', $lead->old_loan_close_amount) }}">
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
                            <input type="text" id="total_loan_amount" class="form-control" disabled value="{{ $lead->total_loan_amount }}">
                        </div>
                    </div>
                </div>

                <hr>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Team Lead'))
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assign To</label>
                            <select name="assigned_to" class="form-control">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                <button type="submit" class="btn btn-primary mt-3">Update Lead</button>
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
            $('#editLeadForm').validate();

            // Re-use the same JS logic from create.blade.php for toggling fields
            // Job Profile Logic
            $('#job_profile_type').change(function() {
                let val = $(this).val();
                $('#nature_of_work_div').hide();
                $('#salaried_type_div').hide();
                $('#other_company_name_div').hide();
                $('#designation_div').hide();
                $('#business_proof_main_div').hide();

                if (val === 'Self Employed') {
                    $('#nature_of_work_div').show();
                    $('#business_proof_main_div').show();
                } else if (val === 'Farmer') {
                    // Farmer specific logic if any
                } else if (val === 'Salaried') {
                    $('#salaried_type_div').show();
                    $('#designation_div').show();
                }
            });

            $('#salaried_type').change(function() {
                if ($(this).val() === 'Other') {
                    $('#other_company_name_div').show();
                } else {
                    $('#other_company_name_div').hide();
                }
            });

            // Income Proof Logic
            $('#income_proof_available').change(function() {
                if ($(this).val() == '1') {
                    $('#itr_details_div').show();
                    $('#approx_income_div').hide();
                } else {
                    $('#itr_details_div').hide();
                    $('#approx_income_div').show();
                }
            });

            // Business Proof Logic
            $('#business_proof_available').change(function() {
                if ($(this).val() == '1') {
                    $('#business_proof_doc_div').show();
                } else {
                    $('#business_proof_doc_div').hide();
                }
            });

            // Loan Type Logic
            $('#loan_type').change(function() {
                let val = $(this).val();
                if (val === 'Other') {
                    $('#other_loan_name_div').show();
                } else {
                    $('#other_loan_name_div').hide();
                }

                if (val === 'Vehicle Loan') {
                    $('#vehicle_loan_details_div').show();
                } else {
                    $('#vehicle_loan_details_div').hide();
                }
            });

            // Vehicle Loan Logic
            $('#vehicle_loan_type').change(function() {
                if ($(this).val() === 'Other') {
                    $('#other_vehicle_loan_name_div').show();
                } else {
                    $('#other_vehicle_loan_name_div').hide();
                }
            });

            $('#vehicle_purchase_type').change(function() {
                let val = $(this).val();
                $('#new_purchase_div').hide();
                $('#repurchase_div').hide();

                if (val === 'New Purchase') {
                    $('#new_purchase_div').show();
                } else if (val === 'Repurchase' || val === 'Refinance' || val === 'Balance Transfer') {
                    $('#repurchase_div').show();
                }
            });

            // Insurance Logic
            $('#insurance_available').change(function() {
                if ($(this).val() == '1') {
                    $('#insurance_details_div').show();
                } else {
                    $('#insurance_details_div').hide();
                }
            });

            // HP Logic
            $('#hypothecation_status').change(function() {
                if ($(this).val() == '1') {
                    $('#hp_details_div').show();
                } else {
                    $('#hp_details_div').hide();
                }
            });

            // Calculate Total Loan Amount
            function calculateTotal() {
                let oldLoan = parseFloat($('#old_loan_close_amount').val()) || 0;
                let inHand = parseFloat($('#required_in_hand_amount').val()) || 0;
                $('#total_loan_amount').val((oldLoan + inHand).toFixed(2));
            }

            $('#old_loan_close_amount, #required_in_hand_amount').on('input', calculateTotal);

            // Back to Top Button Logic
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
