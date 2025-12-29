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
            <form action="{{ route('leads.store') }}" method="POST" enctype="multipart/form-data" id="createLeadForm">
                @csrf

                {{-- Basic Details --}}
                <h5>Basic Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquire At (Firm) <span class="text-danger">*</span></label>
                            <select name="firm_id" class="form-control" required>
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Rented" {{ old('residence_type') == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Own" {{ old('residence_type') == 'Own' ? 'selected' : '' }}>Own</option>
                            </select>
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
                            <input type="text" name="current_address_line" class="form-control" value="{{ old('current_address_line') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" class="form-control" value="{{ old('current_address_pincode') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="current_address_city" class="form-control" value="{{ old('current_address_city') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="current_address_state" class="form-control" value="{{ old('current_address_state') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address <span class="text-danger">*</span></h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" class="form-control" value="{{ old('permanent_address_line') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" class="form-control" value="{{ old('permanent_address_pincode') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="permanent_address_city" class="form-control" value="{{ old('permanent_address_city') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="permanent_address_state" class="form-control" value="{{ old('permanent_address_state') }}">
                                </div>
                            </div>
                        </div>
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
                                <option value="0">No</option>
                                <option value="1">Yes</option>
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
                                <option value="0">No</option>
                                <option value="1">Yes</option>
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
                {{-- Loan Details --}}
                <h5>Loan Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Loan Type Required</label>
                            <select name="loan_type" id="loan_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="Vehicle Loan">Vehicle Loan</option>
                                <option value="Housing Loan">Housing Loan</option>
                                <option value="LAP">Loan Against Property (LAP)</option>
                                <option value="Business Loan">Business Loan</option>
                                <option value="Personal Loan">Personal Loan</option>
                                <option value="Other">Other</option>
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
                                <label>Vehicle Type</label>
                                <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control">
                                    <option value="">Select Vehicle Type</option>
                                    <option value="Car">Car</option>
                                    <option value="Commercial Vehicle">Commercial Vehicle</option>
                                    <option value="Construction Equipment">Construction Equipment</option>
                                    <option value="Tractor">Tractor</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" id="other_vehicle_loan_name_div" style="display:none;">
                            <div class="form-group">
                                <label>Other Vehicle Name</label>
                                <input type="text" name="other_vehicle_loan_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Type</label>
                                <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control">
                                    <option value="">Select Purchase Type</option>
                                    <option value="New Purchase">New Purchase</option>
                                    <option value="Repurchase">Repurchase</option>
                                    <option value="Refinance">Refinance</option>
                                    <option value="Balance Transfer">Balance Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- New Purchase --}}
                    <div id="new_purchase_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Manufacturer</label>
                                    <select name="manufacture_id" class="form-control">
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufactures as $mfg)
                                            <option value="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehicle Name</label>
                                    {{-- Ideally loaded via AJAX based on Manufacturer --}}
                                    <input type="text" name="vehicle_variant" class="form-control" placeholder="Variant/Model">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Showroom Price</label>
                                    <input type="number" step="0.01" name="showroom_price" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>On Road Price</label>
                                    <input type="number" step="0.01" name="on_road_price" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Repurchase/Refinance/BT --}}
                    <div id="repurchase_div" style="display:none;">
                        <h6>RC Details</h6>
                        <div class="row">
                            <div class="col-md-3"><div class="form-group"><label>RC Number</label><input type="text" name="rc_number" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Mfg Name</label><input type="text" name="mfg_name" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Model Name</label><input type="text" name="model_name" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Variant</label><input type="text" name="variant" class="form-control"></div></div>
                            <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control"></div></div>
                        </div>

                        <h6>Insurance</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Insurance Available?</label>
                                    <select name="insurance_available" id="insurance_available" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
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
                                                    <option value="{{ $ins->id }}">{{ $ins->company_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control"></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control"></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control"></div></div>
                                </div>
                            </div>
                        </div>

                        <h6>Hypothecation (HP)</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>HP?</label>
                                    <select name="hypothecation_status" id="hypothecation_status" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9" id="hp_details_div" style="display:none;">
                                <div class="row">
                                    <div class="col-md-4"><div class="form-group"><label>Old Financer</label><input type="text" name="old_financer" class="form-control"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>Close Amount</label><input type="number" step="0.01" name="close_amount" class="form-control"></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>EMI Paid Count</label><input type="number" name="emi_paid_count" class="form-control"></div></div>
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
                        <div class="form-group"><label>Name</label><input type="text" name="ref1_name" class="form-control"></div>
                        <div class="form-group"><label>Mobile</label><input type="text" name="ref1_mobile" class="form-control"></div>
                        <div class="form-group"><label>Email</label><input type="email" name="ref1_email" class="form-control"></div>
                        <div class="form-group"><label>Address</label><textarea name="ref1_address" class="form-control" rows="1"></textarea></div>
                    </div>
                    <div class="col-md-6">
                        <h6>Reference 2</h6>
                        <div class="form-group"><label>Name</label><input type="text" name="ref2_name" class="form-control"></div>
                        <div class="form-group"><label>Mobile</label><input type="text" name="ref2_mobile" class="form-control"></div>
                        <div class="form-group"><label>Email</label><input type="email" name="ref2_email" class="form-control"></div>
                        <div class="form-group"><label>Address</label><textarea name="ref2_address" class="form-control" rows="1"></textarea></div>
                    </div>
                </div>

                <hr>
                {{-- Loan Amount Breakdown --}}
                <h5>Amount Required</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Old Loan Close Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control" value="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Required In Hand</label>
                            <input type="number" step="0.01" name="required_in_hand_amount" id="required_in_hand_amount" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" id="total_loan_amount" class="form-control" disabled placeholder="Auto Calculated">
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Co-Applicant --}}
                <h5>Co-Applicant Details</h5>
                <div class="form-group">
                    <label>Has Co-Applicant?</label>
                    <select name="has_co_applicant" id="has_co_applicant" class="form-control" style="width: 200px;">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div id="co_applicant_div" style="display:none;">
                    {{-- Add fields similar to main applicant --}}
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Name</label><input type="text" name="co_name" class="form-control"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Relation</label><input type="text" name="co_relation" class="form-control"></div></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Residence Type</label>
                                <select name="co_residence_type" class="form-control">
                                    <option value="Rented">Rented</option>
                                    <option value="Own">Own</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Simplified Address for brevity in this example, expand as needed --}}
                    <h6>Current Address</h6>
                    <div class="row">
                        <div class="col-md-12"><input type="text" name="co_current_address_line" class="form-control" placeholder="Address Line"></div>
                    </div>
                    {{-- ... Add other address fields ... --}}
                </div>

                <hr>
                {{-- Guarantor --}}
                <h5>Guarantor Details</h5>
                <div class="form-group">
                    <label>Has Guarantor?</label>
                    <select name="has_guarantor" id="has_guarantor" class="form-control" style="width: 200px;">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div id="guarantor_div" style="display:none;">
                    {{-- Add fields similar to co-applicant --}}
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Name</label><input type="text" name="guarantor_name" class="form-control"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Relation</label><input type="text" name="guarantor_relation" class="form-control"></div></div>
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
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
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
                    father_name: { required: true },
                    email: { required: true, email: true },
                    firm_id: { required: true },
                    residence_type: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    job_profile_type: { required: true },
                    nature_of_work: {
                        required: function(element) {
                            return $("#job_profile_type").val() === 'Self Employed';
                        }
                    },
                    salaried_type: {
                        required: function(element) {
                            return $("#job_profile_type").val() === 'Salaried';
                        }
                    },
                    designation: {
                        required: function(element) {
                            return $("#job_profile_type").val() === 'Salaried';
                        }
                    },
                    other_company_name: {
                        required: function(element) {
                            return $("#salaried_type").val() === 'Other';
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
                        required: true,
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
                    }
                },
                messages: {
                    father_name: "Please enter the father's name",
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    firm_id: "Please select a firm",
                    residence_type: "Please select a residence type",
                    current_address_line: "Please enter the current address",
                    permanent_address_line: "Please enter the permanent address",
                    job_profile_type: "Please select a job profile",
                    nature_of_work: "Please enter the nature of work",
                    salaried_type: "Please select a salaried type",
                    designation: "Please enter a designation",
                    other_company_name: "Please enter the company name",
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
                    assigned_to: "Please select a user to assign the lead to"
                }
            });

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

            // Co-Applicant & Guarantor Toggles
            $('#has_co_applicant').change(function() {
                if ($(this).val() == '1') $('#co_applicant_div').show();
                else $('#co_applicant_div').hide();
            });

            $('#has_guarantor').change(function() {
                if ($(this).val() == '1') $('#guarantor_div').show();
                else $('#guarantor_div').hide();
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
