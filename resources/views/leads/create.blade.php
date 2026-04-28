@extends('adminlte::page')

@section('title', 'Create Lead')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
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
        .select2-container .select2-selection--multiple {
            min-height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            padding: 2px 6px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e9ecef !important;
            border: 1px solid #ced4da !important;
            color: #212529 !important;
            border-radius: 0.2rem !important;
            margin-top: 4px !important;
            padding: 2px 24px 2px 8px !important;
            position: relative;
            line-height: 1.2 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #495057 !important;
            position: absolute !important;
            right: 6px !important;
            left: auto !important;
            top: 50% !important;
            transform: translateY(-50%);
            margin-right: 0 !important;
            padding: 0 2px !important;
            border: 0 !important;
            font-size: 14px !important;
            line-height: 1 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 0 !important;
        }
        .select2-dropdown {
            z-index: 2000 !important;
        }
        /* Fix for Select2 inside Input Group to keep button inline */
        .input-group {
            flex-wrap: nowrap;
        }
        .input-group .select2-container {
            width: auto !important;
            flex-grow: 1;
        }
        .input-group .select2-selection--single {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    </style>
@stop

@section('content_header')
    <h1>Add New Lead</h1>
@stop

@section('content')
    @php
        $canCreateReferralPerson = auth()->user()->hasRole('admin') || auth()->user()->can('create referral person');
        $canCreateVehicleManufacture = auth()->user()->hasRole('admin') || auth()->user()->can('create vehicle manufacture');
        $canCreateInsurance = auth()->user()->hasRole('admin') || auth()->user()->can('create insurance');
    @endphp
    <div class="card">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route(get_route_prefix() . 'leads.store') }}" method="POST" enctype="multipart/form-data" id="addLeadForm">
                @csrf

                {{-- This will hold the actual ID of the selected variant --}}
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id">

                {{-- Basic Details --}}
                <h5>Basic Details</h5>
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                            @error('customer_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name') }}" required>
                            @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Enquire At (Firm) <span class="text-danger">*</span></label>
                            <select name="firm_id" id="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror" value="{{ old('aadhar_number') }}" placeholder="12-digit Aadhar Number">
                            @error('aadhar_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3" id="residence_type_div">
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
                    <div class="col-md-4" id="lead_source_div">
                        <div class="form-group">
                            <label>Lead Source</label>
                            <select name="lead_source" id="lead_source" class="form-control @error('lead_source') is-invalid @enderror">
                                <option value="">Select Source</option>
                                <option value="Self" {{ old('lead_source') == 'Self' ? 'selected' : '' }}>Self</option>
                                <option value="Social media" {{ old('lead_source') == 'Social media' ? 'selected' : '' }}>Social media</option>
                                <option value="Referral" {{ in_array(strtolower((string) old('lead_source')), ['any person', 'referral person', 'referral']) ? 'selected' : '' }}>Referral</option>
                                <option value="Employee" {{ old('lead_source') == 'Employee' ? 'selected' : '' }}>Employee</option>
                                <option value="Other" {{ old('lead_source') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('lead_source') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4" id="lead_source_person_name_div" style="display:none;">
                        <div class="form-group">
                            <label id="lead_source_person_name_label">Referral Person Name</label>
                            <div class="input-group" id="lead_source_person_name_input_group">
                                <select name="lead_source_person_name" id="lead_source_person_name" class="form-control select2 @error('lead_source_person_name') is-invalid @enderror">
                                    @if(old('lead_source_person_name'))
                                        <option value="{{ old('lead_source_person_name') }}" selected>{{ old('lead_source_person_name') }}</option>
                                    @endif
                                    {{-- Options populated via Select2 AJAX --}}
                                </select>
                                @if($canCreateReferralPerson)
                                    <div class="input-group-append" id="lead_source_person_name_add_btn_wrap">
                                        <button class="btn btn-outline-secondary" type="button" id="lead_source_person_name_add_btn" data-toggle="modal" data-target="#addReferralPersonModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @error('lead_source_person_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Picture <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
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
                    </div>
                </div>
                <div class="row" id="default_address_proof_div">
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
                            <label>Address Proof Upload <small class="text-muted">(Max 5MB per file, JPG, PNG, PDF)</small></label>
                            <div id="addressProofDropzone" class="dropzone">
                                <div class="dz-message">
                                    <h3>Drop files here or click to upload Document.</h3>
                                    <span class="note">Multiple files allowed. Previews will be shown below.</span>
                                </div>
                            </div>
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
                                <select name="job_profile_type" id="job_profile_type" class="form-control @error('job_profile_type') is-invalid @enderror" required>
                                    <option value="">Select Profile</option>
                                    <option value="Self Employed" {{ old('job_profile_type') == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                    <option value="Farmer" {{ old('job_profile_type') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                                    <option value="Salaried" {{ old('job_profile_type') == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                                </select>
                                @error('job_profile_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="company_name_div">
                            <div class="form-group">
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="other_company_name" class="form-control @error('other_company_name') is-invalid @enderror" value="{{ old('other_company_name') }}" required>
                                @error('other_company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="designation_div">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" required>
                                @error('designation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="nature_of_work_div" style="display:none;">
                            <div class="form-group">
                                <label>Nature of Work</label>
                                <input type="text" name="nature_of_work" class="form-control @error('nature_of_work') is-invalid @enderror" value="{{ old('nature_of_work') }}">
                                @error('nature_of_work') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="business_stability_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Stability</label>
                                <input type="text" name="business_stability" id="business_stability_datepicker" class="form-control @error('business_stability') is-invalid @enderror" value="{{ old('business_stability') }}" placeholder="YYYY">
                                @error('business_stability') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="salaried_type_div" style="display:none;">
                            <div class="form-group">
                                <label>Salaried Type</label>
                                <select name="salaried_type" id="salaried_type" class="form-control @error('salaried_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Government" {{ old('salaried_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Corporate" {{ old('salaried_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Other" {{ old('salaried_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('salaried_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row" id="farmer_fields" style="display:none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agri Land Area</label>
                                <input type="text" name="agri_land_area" class="form-control @error('agri_land_area') is-invalid @enderror" value="{{ old('agri_land_area') }}">
                                @error('agri_land_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agri Land Document <small class="text-muted">(Optional, Max 5MB)</small></label>
                                <input type="file" name="agri_land_document" class="form-control-file @error('agri_land_document') is-invalid @enderror">
                                @error('agri_land_document') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Income Proof Available?</label>
                                <select name="income_proof_available" id="income_proof_available" class="form-control @error('income_proof_available') is-invalid @enderror">
                                    <option value="0" {{ old('income_proof_available') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('income_proof_available') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('income_proof_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="salary_details_div" style="display:none;">
                                <div class="form-group">
                                    <label>Last Month Salary</label>
                                    <input type="number" step="0.01" name="last_month_salary" class="form-control @error('last_month_salary') is-invalid @enderror" value="{{ old('last_month_salary') }}">
                                    @error('last_month_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Salary Slip</label>
                                    <input type="file" name="salary_slip" class="form-control-file @error('salary_slip') is-invalid @enderror">
                                    @error('salary_slip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>ITR Available?</label>
                                <select name="has_itr" id="has_itr" class="form-control @error('has_itr') is-invalid @enderror">
                                    <option value="0" {{ old('has_itr') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('has_itr') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('has_itr') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="itr_details_div" style="display:none;">
                                <div class="form-group">
                                    <label>Last ITR Amount</label>
                                    <input type="number" step="0.01" name="itr_amount" class="form-control @error('itr_amount') is-invalid @enderror" value="{{ old('itr_amount') }}">
                                    @error('itr_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div id="itr_years_div" style="display:none;">
                                    <div class="form-group">
                                        <label>ITR Year(s)</label>
                                        <select name="itr_years" class="form-control @error('itr_years') is-invalid @enderror">
                                            <option value="">Select Year</option>
                                            <option value="1" {{ old('itr_years') == 1 ? 'selected' : '' }}>1 Year</option>
                                            <option value="2" {{ old('itr_years') == 2 ? 'selected' : '' }}>2 Year</option>
                                            <option value="3" {{ old('itr_years') == 3 ? 'selected' : '' }}>3 Year</option>
                                        </select>
                                        @error('itr_years') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>ITR Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="itr_document" class="form-control-file @error('itr_document') is-invalid @enderror">
                                    @error('itr_document') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div id="approx_income_div">
                                <div class="form-group">
                                    <label>Approx. Income</label>
                                    <input type="number" step="0.01" name="approx_income" class="form-control @error('approx_income') is-invalid @enderror" value="{{ old('approx_income') }}">
                                    @error('approx_income') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="business_proof_main_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Proof Available?</label>
                                <select name="business_proof_available" id="business_proof_available" class="form-control @error('business_proof_available') is-invalid @enderror">
                                    <option value="0" {{ old('business_proof_available') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('business_proof_available') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('business_proof_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="business_proof_doc_div" style="display:none;">
                                <div class="form-group">
                                    <label>Business Proof Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="business_proof_document" class="form-control-file @error('business_proof_document') is-invalid @enderror">
                                    @error('business_proof_document') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                                <select name="loan_type" id="loan_type_select" class="form-control @error('loan_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Vehicle Loan" {{ old('loan_type') == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                                    <option value="Housing Loan" {{ old('loan_type') == 'Housing Loan' ? 'selected' : '' }}>Housing Loan</option>
                                    <option value="LAP" {{ old('loan_type') == 'LAP' ? 'selected' : '' }}>Loan Against Property (LAP)</option>
                                    <option value="Business Loan" {{ old('loan_type') == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                                    <option value="Personal Loan" {{ old('loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                                    <option value="Other" {{ old('loan_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('loan_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="sub_loan_type_div" style="display:none;">
                            <div class="form-group">
                                <label id="sub_loan_type_label">Sub Loan Type</label>
                                <select name="sub_loan_type" id="sub_loan_type" class="form-control @error('sub_loan_type') is-invalid @enderror">
                                    <option value="">Select Sub Loan Type</option>
                                    {{-- Options populated via JS --}}
                                </select>
                                @error('sub_loan_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="other_loan_name_div" style="display:none;">
                            <div class="form-group">
                                <label>Other Loan Name</label>
                                <input type="text" name="other_loan_name" class="form-control @error('other_loan_name') is-invalid @enderror" value="{{ old('other_loan_name') }}">
                                @error('other_loan_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Purchase Type for Personal/Business Loan --}}
                    <div id="loan_purchase_type_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Type</label>
                                    <select name="loan_purchase_type" id="loan_purchase_type" class="form-control @error('loan_purchase_type') is-invalid @enderror">
                                        <option value="New" {{ old('loan_purchase_type') == 'New' ? 'selected' : '' }}>New</option>
                                        <option value="Balance Transfer" {{ old('loan_purchase_type') == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer (BT)</option>
                                    </select>
                                    @error('loan_purchase_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Details (for Housing Loan/LAP) --}}
                    <div id="property_details_div" style="display:none;">
                        <h6>Property Details</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Property Location</label>
                                    <input type="text" name="property_location" class="form-control @error('property_location') is-invalid @enderror" value="{{ old('property_location') }}">
                                    @error('property_location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Property Area</label>
                                    <input type="text" name="property_area" class="form-control @error('property_area') is-invalid @enderror" value="{{ old('property_area') }}">
                                    @error('property_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Property Approx. Value</label>
                                    <input type="number" step="0.01" name="property_approx_value" class="form-control @error('property_approx_value') is-invalid @enderror" value="{{ old('property_approx_value') }}">
                                    @error('property_approx_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Property Type</label>
                                    <select name="property_type" class="form-control @error('property_type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        <option value="Diverted" {{ old('property_type') == 'Diverted' ? 'selected' : '' }}>Diverted</option>
                                        <option value="Non Diverted" {{ old('property_type') == 'Non Diverted' ? 'selected' : '' }}>Non Diverted</option>
                                        <option value="Agri Land" {{ old('property_type') == 'Agri Land' ? 'selected' : '' }}>Agri Land</option>
                                        <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>other</option>
                                    </select>
                                    @error('property_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Land Type</label>
                                    <select name="land_type" class="form-control @error('land_type') is-invalid @enderror">
                                        <option value="">Select Land Type</option>
                                        <option value="Farming Land" {{ old('land_type') == 'Farming Land' ? 'selected' : '' }}>Farming Land</option>
                                        <option value="Plot" {{ old('land_type') == 'Plot' ? 'selected' : '' }}>Plot</option>
                                        <option value="House" {{ old('land_type') == 'House' ? 'selected' : '' }}>House</option>
                                        <option value="Flat" {{ old('land_type') == 'Flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="Commercial land" {{ old('land_type') == 'Commercial land' ? 'selected' : '' }}>Commercial land</option>
                                        <option value="Commercial property" {{ old('land_type') == 'Commercial property' ? 'selected' : '' }}>Commercial property</option>
                                        <option value="other" {{ old('land_type') == 'other' ? 'selected' : '' }}>other</option>
                                    </select>
                                    @error('land_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Loan Specifics --}}
                    <div id="vehicle_loan_details_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_loan_type" id="vehicle_loan_type" class="form-control @error('vehicle_loan_type') is-invalid @enderror">
                                        <option value="">Select Vehicle Type</option>
                                        <option value="Car" {{ old('vehicle_loan_type') == 'Car' ? 'selected' : '' }}>Car</option>
                                        <option value="Commercial Vehicle" {{ old('vehicle_loan_type') == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                        <option value="Construction Equipment" {{ old('vehicle_loan_type') == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                        <option value="Tractor" {{ old('vehicle_loan_type') == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                        <option value="2 Wheeler" {{ old('vehicle_loan_type') == '2 Wheeler' ? 'selected' : '' }}>2 Wheeler</option>
                                        <option value="3 Wheeler" {{ old('vehicle_loan_type') == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                                        <option value="Other" {{ old('vehicle_loan_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('vehicle_loan_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4" id="other_vehicle_loan_name_div" style="display:none;">
                                <div class="form-group">
                                    <label>Other Vehicle Name</label>
                                    <input type="text" name="other_vehicle_loan_name" class="form-control @error('other_vehicle_loan_name') is-invalid @enderror" value="{{ old('other_vehicle_loan_name') }}">
                                    @error('other_vehicle_loan_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control @error('vehicle_purchase_type') is-invalid @enderror">
                                        <option value="">Select Purchase Type</option>
                                        <option value="New Purchase" {{ old('vehicle_purchase_type') == 'New Purchase' ? 'selected' : '' }}>New Purchase</option>
                                        <option value="Repurchase" {{ old('vehicle_purchase_type') == 'Repurchase' ? 'selected' : '' }}>Repurchase</option>
                                        <option value="Refinance" {{ old('vehicle_purchase_type') == 'Refinance' ? 'selected' : '' }}>Refinance</option>
                                        <option value="Balance Transfer" {{ old('vehicle_purchase_type') == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer</option>
                                    </select>
                                    @error('vehicle_purchase_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- New Purchase --}}
                        <div id="new_purchase_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Manufacturer <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="manufacture_id" class="form-control manufacture-select @error('manufacture_id') is-invalid @enderror">
                                                <option value="">Select Manufacturer</option>
                                                @foreach($manufactures as $mfg)
                                                    <option value="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($canCreateVehicleManufacture)
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        @error('manufacture_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Vehicle Name <span class="text-danger">*</span></label>
                                        <select name="vehicle_name_id_display" class="form-control vehicle-name-select @error('vehicle_name_id_display') is-invalid @enderror">
                                            <option value="">Select Manufacturer First</option>
                                        </select>
                                        @error('vehicle_name_id_display') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Variant</label>
                                        <select name="vehicle_variant" class="form-control variant-select @error('vehicle_variant') is-invalid @enderror @error('vehicle_name_id') is-invalid @enderror">
                                            <option value="">Select Vehicle First</option>
                                        </select>
                                        @error('vehicle_variant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        @error('vehicle_name_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ex-showroom Price</label>
                                        <input type="number" step="0.01" name="showroom_price" class="form-control @error('showroom_price') is-invalid @enderror" value="{{ old('showroom_price') }}">
                                        @error('showroom_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>On Road Price</label>
                                        <input type="number" step="0.01" name="on_road_price" class="form-control @error('on_road_price') is-invalid @enderror" value="{{ old('on_road_price') }}">
                                        @error('on_road_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Repurchase/Refinance/BT --}}
                        <div id="repurchase_div" style="display:none;">
                            <h6>RC Details</h6>
                            <div class="row">
                                <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rc_number" class="form-control @error('rc_number') is-invalid @enderror" value="{{ old('rc_number') }}">@error('rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" value="{{ old('engine_number') }}">@error('engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror" value="{{ old('chassis_number') }}">@error('chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="mfg_year_datepicker_loan" class="form-control @error('mfg_year') is-invalid @enderror" value="{{ old('mfg_year') }}" placeholder="MM-YYYY">@error('mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mfg Name</label>
                                        <div class="input-group">
                                            <select name="rto_mfg_name" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
                                                <option value="">Select Manufacturer</option>
                                                @foreach($manufactures as $mfg)
                                                    <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($canCreateVehicleManufacture)
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
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
                                        <select name="variant" class="form-control variant-select @error('variant') is-invalid @enderror @error('vehicle_name_id') is-invalid @enderror">
                                            <option value="">Select Vehicle First</option>
                                        </select>
                                        @error('variant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        @error('vehicle_name_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control @error('owner_serial_number') is-invalid @enderror" value="{{ old('owner_serial_number') }}">@error('owner_serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>

                            <h6>Insurance</h6>
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
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="mb-0">Insurance Company</label>
                                                    @if($canCreateInsurance)
                                                        <button type="button" class="btn btn-link btn-sm p-0 add-insurance-company-btn" title="Add Insurance Company">
                                                            <i class="fas fa-plus-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <select name="insurance_id" class="form-control @error('insurance_id') is-invalid @enderror">
                                                    <option value="">Select Insurance Company</option>
                                                    @foreach($insurances as $ins)
                                                        <option value="{{ $ins->id }}" {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('insurance_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3"><div class="form-group"><label>From Date</label><input type="date" name="insurance_start_date" class="form-control @error('insurance_start_date') is-invalid @enderror" value="{{ old('insurance_start_date') }}">@error('insurance_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                        <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control @error('insurance_end_date') is-invalid @enderror" value="{{ old('insurance_end_date') }}">@error('insurance_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                        <div class="col-md-2"><div class="form-group"><label>IDV Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control @error('insurance_amount') is-invalid @enderror" value="{{ old('insurance_amount') }}">@error('insurance_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                    </div>
                                </div>
                            </div>

                            <h6>Hypothecation (HP)</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>HP?</label>
                                        <select name="hypothecation_status" id="hypothecation_status" class="form-control @error('hypothecation_status') is-invalid @enderror">
                                            <option value="0" {{ old('hypothecation_status') == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('hypothecation_status') == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('hypothecation_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Loan Amount Breakdown --}}
                    <div id="amount_required_section">
                        <h5>Amount Required</h5>
                        {{-- Fields for HP Yes --}}
                        <div id="hp_related_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Financer Name</label>
                                        <select name="old_financer" class="form-control hp-optional-field @error('old_financer') is-invalid @enderror">
                                            <option value="">Select Financer</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->name }}" {{ old('old_financer') == $bank->name ? 'selected' : '' }}>{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('old_financer') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>EMI Paid Count</label>
                                        <input type="number" name="emi_paid_count" class="form-control @error('emi_paid_count') is-invalid @enderror" value="{{ old('emi_paid_count') }}">
                                        @error('emi_paid_count') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Old Loan Close Amount</label>
                                        <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control @error('old_loan_close_amount') is-invalid @enderror" value="{{ old('old_loan_close_amount') }}">
                                        @error('old_loan_close_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Fields for both HP Yes and No --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Required Amount (In Hand) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="required_in_hand_amount" id="required_in_hand_amount" class="form-control @error('required_in_hand_amount') is-invalid @enderror" value="{{ old('required_in_hand_amount') }}" required>
                                    @error('required_in_hand_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4" id="total_loan_div" style="display: none;">
                                <div class="form-group">
                                    <label>Total Loan Required</label>
                                    <input type="text" id="total_loan_amount" name="total_loan_amount" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RTO Details Section --}}
                <div id="rto_section" style="display:none;">
                    <h5>RTO Details</h5>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control @error('rto_rc_number') is-invalid @enderror" value="{{ old('rto_rc_number') }}">@error('rto_rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control @error('rto_engine_number') is-invalid @enderror" value="{{ old('rto_engine_number') }}">@error('rto_engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control @error('rto_chassis_number') is-invalid @enderror" value="{{ old('rto_chassis_number') }}">@error('rto_chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="mfg_year_datepicker_rto" class="form-control @error('mfg_year') is-invalid @enderror" value="{{ old('mfg_year') }}" placeholder="MM-YYYY">@error('mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mfg Name</label>
                                <div class="input-group">
                                    <select name="rto_mfg_name" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufactures as $mfg)
                                            <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                        @endforeach
                                    </select>
                                    @if($canCreateVehicleManufacture)
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('rto_mfg_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Model Name <span class="text-danger">*</span></label>
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="mb-0">Insurance Company</label>
                                            @if($canCreateInsurance)
                                                <button type="button" class="btn btn-link btn-sm p-0 add-insurance-company-btn" title="Add Insurance Company">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <select name="insurance_id" class="form-control @error('insurance_id') is-invalid @enderror">
                                            <option value="">Select Insurance Company</option>
                                            @foreach($insurances as $ins)
                                                <option value="{{ $ins->id }}" {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('insurance_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4"><div class="form-group"><label>From Date</label><input type="date" name="insurance_start_date" class="form-control @error('insurance_start_date') is-invalid @enderror" value="{{ old('insurance_start_date') }}">@error('insurance_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-4"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control @error('insurance_end_date') is-invalid @enderror" value="{{ old('insurance_end_date') }}">@error('insurance_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Bank</label>
                                        <select name="rto_bank_id" class="form-control @error('rto_bank_id') is-invalid @enderror">
                                            <option value="">Select Bank</option>
                                            @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                                                <option value="{{ $bank->id }}" {{ old('rto_bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NOC Available?</label>
                                        <select name="rto_noc_status" class="form-control">
                                            <option value="No" {{ old('rto_noc_status') == 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ old('rto_noc_status') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Insurance Details Section --}}
                <div id="insurance_section" style="display:none;">
                    <h5>Insurance Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Insurance Type <span class="text-danger">*</span></label>
                                <select name="insurance_type" id="insurance_type" class="form-control @error('insurance_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Vehicle" {{ old('insurance_type') == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                                    <option value="Health" {{ old('insurance_type') == 'Health' ? 'selected' : '' }}>Health</option>
                                    <option value="Property" {{ old('insurance_type') == 'Property' ? 'selected' : '' }}>Property</option>
                                </select>
                                @error('insurance_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Insurance Fields --}}
                    <div id="ins_vehicle_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Manufacturer <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="ins_mfg_name" class="form-control manufacture-select @error('ins_mfg_name') is-invalid @enderror">
                                            <option value="">Select Manufacturer</option>
                                            @foreach($manufactures as $mfg)
                                                <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}">{{ $mfg->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($canCreateVehicleManufacture)
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    @error('ins_mfg_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Model Name <span class="text-danger">*</span></label>
                                    <select name="ins_model_name" class="form-control vehicle-name-select @error('ins_model_name') is-invalid @enderror">
                                        <option value="">Select Manufacturer First</option>
                                    </select>
                                    @error('ins_model_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Variant</label>
                                    <select name="ins_variant" class="form-control variant-select @error('ins_variant') is-invalid @enderror @error('vehicle_name_id') is-invalid @enderror">
                                        <option value="">Select Vehicle First</option>
                                    </select>
                                    @error('ins_variant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    @error('vehicle_name_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>IDV Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="insurance_amount" class="form-control @error('insurance_amount') is-invalid @enderror" value="{{ old('insurance_amount') }}">
                                    @error('insurance_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Coverage Type</label>
                                    <select name="insurance_coverage_type" class="form-control" id="insurance_coverage_type">
                                        <option value="">Select Type</option>
                                        <option value="First" {{ old('insurance_coverage_type') == 'First' ? 'selected' : '' }}>First Party</option>
                                        <option value="Third" {{ old('insurance_coverage_type') == 'Third' ? 'selected' : '' }}>Third Party</option>
                                        <option value="Addon" {{ old('insurance_coverage_type') == 'Addon' ? 'selected' : '' }}>Addon Policy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="ins_addon_desc_div" style="display:none;">
                                <div class="form-group">
                                    <label>Addon Details</label>
                                    <input type="text" name="insurance_addon_description" class="form-control" value="{{ old('insurance_addon_description') }}" placeholder="Specify addon details">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-3"><div class="form-group"><label>RC Number</label><input type="text" name="rto_rc_number" class="form-control" value="{{ old('rto_rc_number') }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control" value="{{ old('rto_engine_number') }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control" value="{{ old('rto_chassis_number') }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="ins_mfg_year_datepicker" class="form-control" value="{{ old('mfg_year') }}" placeholder="MM-YYYY"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hypothecation (HP)?</label>
                                    <select name="ins_hypothecation_status" class="form-control">
                                        <option value="0" {{ old('ins_hypothecation_status') == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('ins_hypothecation_status') == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Health Insurance Fields --}}
                    <div id="ins_health_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Policy Type</label>
                                    <select name="health_policy_type" class="form-control @error('health_policy_type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        <option value="Individual" {{ old('health_policy_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="Family Floater" {{ old('health_policy_type') == 'Family Floater' ? 'selected' : '' }}>Family Floater</option>
                                    </select>
                                    @error('health_policy_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <hr>
                    <h6>Insurance Documents</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address Document Type</label>
                                <select name="insurance_proof_type" class="form-control @error('insurance_proof_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Aadhar" {{ old('insurance_proof_type') == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                    <option value="Voter Id" {{ old('insurance_proof_type') == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                    <option value="Driving Licence" {{ old('insurance_proof_type') == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                    <option value="Passport" {{ old('insurance_proof_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="Electricity Bill" {{ old('insurance_proof_type') == 'Electricity Bill' ? 'selected' : '' }}>Electricity Bill</option>
                                </select>
                                @error('insurance_proof_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload Document <small class="text-muted">(JPG, PNG, PDF)</small></label>
                                <input type="file" name="insurance_address_proof" class="form-control-file @error('insurance_address_proof') is-invalid @enderror">
                                @error('insurance_address_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6>Premium Amount Collected</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Premium Amount Collected? <span class="text-danger">*</span></label>
                                <select name="premium_amount_collected" id="premium_amount_collected" class="form-control @error('premium_amount_collected') is-invalid @enderror">
                                    <option value="">Select</option>
                                    <option value="Yes" {{ old('premium_amount_collected') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old('premium_amount_collected', 'No') == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('premium_amount_collected') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="premium_payment_type_div" style="display:none;">
                            <div class="form-group">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="premium_payment_type" id="premium_payment_type" class="form-control @error('premium_payment_type') is-invalid @enderror">
                                    <option value="">Select</option>
                                    <option value="Cash" {{ old('premium_payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Cheque" {{ old('premium_payment_type') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Online" {{ old('premium_payment_type') == 'Online' ? 'selected' : '' }}>UPI/QR</option>
                                        <option value="Bank Transfer" {{ old('premium_payment_type') == 'Bank Transfer' ? 'selected' : '' }}>NEFT/RTGS</option>
                                </select>
                                @error('premium_payment_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div id="premium_payment_fields"></div>
                </div>

                <div id="co_guarantor_section">
                    <hr>
                    {{-- Co-Applicant --}}
                    <h5>Co-Applicant Details</h5>
                    <div class="form-group">
                        <label>Has Co-Applicant?</label>
                        <select name="has_co_applicant" id="has_co_applicant" class="form-control @error('has_co_applicant') is-invalid @enderror" style="width: 200px;">
                            <option value="0" {{ old('has_co_applicant') == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('has_co_applicant') == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('has_co_applicant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div id="co_applicant_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4"><div class="form-group"><label>Co-Applicant Name</label><input type="text" name="co_name" class="form-control @error('co_name') is-invalid @enderror" value="{{ old('co_name') }}">@error('co_name') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label>Co-Applicant Relation</label><input type="text" name="co_relation" class="form-control @error('co_relation') is-invalid @enderror" value="{{ old('co_relation') }}">@error('co_relation') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        </div>
                    </div>

                    <hr>
                    {{-- Guarantor --}}
                    <h5>Guarantor Details</h5>
                    <div class="form-group">
                        <label>Has Guarantor?</label>
                        <select name="has_guarantor" id="has_guarantor" class="form-control @error('has_guarantor') is-invalid @enderror" style="width: 200px;">
                            <option value="0" {{ old('has_guarantor') == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('has_guarantor') == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('has_guarantor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div id="guarantor_div" style="display:none;">
                        <div class="row">
                            <div class="col-md-4"><div class="form-group"><label>Guarantor Name</label><input type="text" name="guarantor_name" class="form-control @error('guarantor_name') is-invalid @enderror" value="{{ old('guarantor_name') }}">@error('guarantor_name') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label>Guarantor Relation</label><input type="text" name="guarantor_relation" class="form-control @error('guarantor_relation') is-invalid @enderror" value="{{ old('guarantor_relation') }}">@error('guarantor_relation') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        </div>
                    </div>
                </div>

                <hr>
                @if(auth()->user()->can('assign lead'))
                <div class="row" id="assignment_section">
                    <div class="col-md-6">
                        <div class="form-group" id="assigned_to_group">
                            <label>Assign To employee </label>
                            <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" >
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-role="{{ $user->roles->first()->name ?? '' }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="reporting_manager_group">
                            <label>Reporting Manager</label>
                            <select name="reporting_manager_ids[]" id="reporting_manager_id" class="form-control select2 @error('reporting_manager_ids') is-invalid @enderror" multiple>
                                <option value="">Select an employee first</option>
                            </select>
                            @error('reporting_manager_ids') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">Create Lead</button>
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

    <!-- Add Insurance Company Modal -->
    <div class="modal fade" id="addInsuranceCompanyModal" tabindex="-1" role="dialog" aria-labelledby="addInsuranceCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInsuranceCompanyModalLabel">Add New Insurance Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addInsuranceCompanyForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Agent Name <span class="text-danger">*</span></label>
                            <input type="text" name="agent_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type" class="form-control" required>
                                <option value="2 Wheeler">2 Wheeler</option>
                                <option value="3 Wheeler">3 Wheeler</option>
                                <option value="4 Wheeler" selected>4 Wheeler</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Insurance Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            const currentUser = {
                id: {{ auth()->id() }},
                name: @json(auth()->user()->name),
                reporting_manager_id: @json(auth()->user()->reporting_manager_id),
                reporting_manager_ids: @json(auth()->user()->reporting_manager_id ? [auth()->user()->reporting_manager_id] : []),
            };
            const initialReportingManagerIds = @json(collect(old('reporting_manager_ids', old('reporting_manager_id') ? [old('reporting_manager_id')] : []))->map(fn($id) => (int) $id)->filter(fn($id) => $id > 0)->values()->all());
            let activeInsuranceCompanySelect = null;

            function normalizedLeadSourceValue() {
                const value = ($('#lead_source').val() || '').trim().toLowerCase();

                if (['referral person', 'referral', 'any person'].includes(value)) {
                    return 'referral';
                }

                return value;
            }

            function getLeadSourceConfig() {
                const source = normalizedLeadSourceValue();

                if (source === 'self') {
                    return {
                        showAssignment: false,
                        showPersonName: false,
                        label: 'Referral Person Name',
                        placeholder: 'Search referral person...',
                        showAddButton: true,
                    };
                }

                if (source === 'referral') {
                    return {
                        showAssignment: true,
                        showPersonName: true,
                        label: 'Referral Person Name',
                        placeholder: 'Search referral person or type name...',
                        showAddButton: true,
                    };
                }

                if (source === 'other') {
                    return {
                        showAssignment: true,
                        showPersonName: true,
                        label: 'Name',
                        placeholder: 'Enter name',
                        showAddButton: false,
                    };
                }

                return {
                    showAssignment: true,
                    showPersonName: false,
                    label: 'Referral Person Name',
                    placeholder: 'Search referral person...',
                    showAddButton: true,
                };
            }

            function ensureCurrentUserOption() {
                const assignedToSelect = $('#assigned_to');
                if (!assignedToSelect.find('option[value="' + currentUser.id + '"]').length) {
                    assignedToSelect.append(new Option(currentUser.name, currentUser.id, true, true));
                }
            }

            function updateLeadSourcePersonUI() {
                const config = getLeadSourceConfig();
                const source = normalizedLeadSourceValue();
                const personDiv = $('#lead_source_person_name_div');
                const label = $('#lead_source_person_name_label');

                label.text(config.label);
                if (config.showPersonName) {
                    personDiv.show();
                } else {
                    personDiv.hide();
                }
            }

            function populateManagers(userId, selectedManagerIds = []) {
                let managerSelect = $('#reporting_manager_id');
                managerSelect.empty().append('<option value="">Loading...</option>');
                const selectedSet = new Set((selectedManagerIds || []).map(id => String(id)));

                if (userId) {
                    $.ajax({
                        url: '{{ url("admin/get-managers-by-user") }}/' + userId,
                        type: 'GET',
                        success: function(data) {
                            managerSelect.empty();
                            $.each(data, function(index, manager) {
                                let isSelected = selectedSet.has(String(manager.id)) ? 'selected' : '';
                                managerSelect.append('<option value="' + manager.id + '" ' + isSelected + '>' + manager.name + '</option>');
                            });
                            managerSelect.trigger('change');
                        },
                        error: function() {
                            managerSelect.empty().append('<option value="">Error loading managers</option>');
                        }
                    });
                } else {
                    managerSelect.empty().append('<option value="">Select an employee first</option>');
                }
            }

            function syncAssignmentSection() {
                const config = getLeadSourceConfig();

                if (!config.showAssignment) {
                    ensureCurrentUserOption();
                    $('#assigned_to').val(String(currentUser.id));
                    populateManagers(currentUser.id, currentUser.reporting_manager_ids);
                    $('#assignment_section').hide();
                } else {
                    $('#assignment_section').show();
                }

                updateLeadSourcePersonUI();
            }

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

            // --- Select2 for Reporting Managers (multi-select) ---
            const reportingManagerSelect = $('#reporting_manager_id');
            if (reportingManagerSelect.length) {
                reportingManagerSelect.select2({
                    placeholder: 'Select Reporting Manager(s)',
                    width: '100%',
                    closeOnSelect: true,
                    allowClear: false
                });
            }
            // --- Month/Year Datepicker ---
            $('#mfg_year_datepicker_loan, #mfg_year_datepicker_rto, #ins_mfg_year_datepicker').datepicker({
                format: "mm-yyyy",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            });

            // --- Business Stability Year Picker ---
            $('#business_stability_datepicker').datepicker({
                format: "yyyy",
                startView: "years",
                minViewMode: "years",
                autoclose: true
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

            $(document).on('click', '.add-insurance-company-btn', function() {
                activeInsuranceCompanySelect = $(this).closest('.form-group').find('select[name="insurance_id"]');
                $('#addInsuranceCompanyModal').modal('show');
            });

            $('#addInsuranceCompanyForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route(get_route_prefix() . 'insurances.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            let newOption = new Option(response.insurance.company_name, response.insurance.id, true, true);
                            if (activeInsuranceCompanySelect && activeInsuranceCompanySelect.length) {
                                activeInsuranceCompanySelect.append(newOption).trigger('change');
                            } else {
                                $('select[name="insurance_id"]').append(newOption).trigger('change');
                            }
                            $('#addInsuranceCompanyModal').modal('hide');
                            $('#addInsuranceCompanyForm')[0].reset();
                            $('#addInsuranceCompanyForm select[name="vehicle_type"]').val('4 Wheeler');
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error adding insurance company. Please check inputs.');
                    }
                });
            });

            // --- Dropzone Initialization ---
            var myDropzone = new Dropzone("#addressProofDropzone", {
                url: "{{ route(get_route_prefix() . 'leads.store') }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                paramName: "current_address_proof",
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
                        if ($("#addLeadForm").valid()) {
                            if (dz.getQueuedFiles().length > 0) {
                                dz.processQueue();
                            } else {
                                // If no files in dropzone, submit form normally via AJAX
                                var formData = new FormData($("#addLeadForm")[0]);
                                $.ajax({
                                    url: $("#addLeadForm").attr('action'),
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        window.location.href = "{{ route(get_route_prefix() . 'leads.index') }}";
                                    },
                                    error: function(err) {
                                        alert("Error creating lead. Please check all required fields.");
                                    }
                                });
                            }
                        }
                    });

                    this.on("sendingmultiple", function(data, xhr, formData) {
                        var formArray = $("#addLeadForm").serializeArray();
                        $.each(formArray, function(i, field) {
                            formData.append(field.name, field.value);
                        });
                        // Append other file inputs
                        var profilePic = $('input[name="profile_picture"]')[0].files[0];
                        if(profilePic) formData.append('profile_picture', profilePic);

                        var salarySlip = $('input[name="salary_slip"]')[0].files[0];
                        if(salarySlip) formData.append('salary_slip', salarySlip);

                        var itrDoc = $('input[name="itr_document"]')[0].files[0];
                        if(itrDoc) formData.append('itr_document', itrDoc);

                        var businessProof = $('input[name="business_proof_document"]')[0].files[0];
                        if(businessProof) formData.append('business_proof_document', businessProof);

                        var insAddressProof = $('input[name="insurance_address_proof"]')[0].files[0];
                        if(insAddressProof) formData.append('insurance_address_proof', insAddressProof);
                    });

                    this.on("successmultiple", function(files, response) {
                        window.location.href = "{{ route(get_route_prefix() . 'leads.index') }}";
                    });

                    this.on("errormultiple", function(files, response) {
                        alert("Error uploading files. Please check form data.");
                    });
                }
            });

            // --- Validation ---
            $('#addLeadForm').validate({
                rules: {
                    customer_name: { required: true },
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
                            return $("#hypothecation_status").val() == '1';
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
                            return $('#assigned_to').length > 0 && $('#assignment_section').is(':visible');
                        }
                    },
                    'reporting_manager_ids[]': {
                        required: function(element) {
                            return $('#reporting_manager_id').length > 0 && $('#assignment_section').is(':visible');
                        }
                    },
                    lead_source_person_name: {
                        required: function(element) {
                            return ['referral', 'other'].includes(normalizedLeadSourceValue());
                        }
                    },
                    // Loan Specific
                    loan_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan';
                        }
                    },
                    vehicle_loan_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type_select").val() === 'Vehicle Loan';
                        }
                    },
                    vehicle_purchase_type: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type_select").val() === 'Vehicle Loan';
                        }
                    },
                    manufacture_id: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type_select").val() === 'Vehicle Loan' && $("#vehicle_purchase_type").val() === 'New Purchase';
                        }
                    },
                    rc_number: {
                        required: function(element) {
                            return $("#lead_type").val() === 'Loan' && $("#loan_type_select").val() === 'Vehicle Loan' && ['Repurchase', 'Refinance', 'Balance Transfer'].includes($("#vehicle_purchase_type").val());
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
                    customer_name: "Please enter customer name",
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
                    loan_type: "Please select a loan type",
                    vehicle_loan_type: "Please select a vehicle loan type",
                    vehicle_purchase_type: "Please select a purchase type",
                    manufacture_id: "Please select a manufacturer",
                    rc_number: "Please enter the RC number",
                    rto_rc_number: "Please enter the RC number",
                    ins_model_name: "Please enter the model name"
                }
            });

            // --- Firm to Employee Logic ---
            function loadEmployees() {
                let assignedToSelect = $('#assigned_to');
                if (assignedToSelect.length > 0) {
                    assignedToSelect.empty().append('<option value="">Loading...</option>');
                    $.ajax({
                        url: '{{ route("get-employees-by-firm") }}',
                        type: 'GET',
                        success: function(data) {
                            assignedToSelect.empty().append('<option value="">Select User</option>');
                            $.each(data, function(key, user) {
                                assignedToSelect.append('<option value="' + user.id + '">' + user.name + '</option>');
                            });
                            syncAssignmentSection();
                        }
                    });
                }
            }
            loadEmployees();

            // --- Reporting Manager Logic ---
            $('#lead_source').on('change', function() {
                syncAssignmentSection();
                if ($('#assignment_section').is(':visible')) {
                    populateManagers($('#assigned_to').val(), []);
                }
            });

            $('#assigned_to').change(function() {
                if ($('#assignment_section').is(':visible')) {
                    populateManagers($(this).val(), []);
                }
            });

            syncAssignmentSection();
            if ($('#assignment_section').is(':visible') && $('#assigned_to').val()) {
                populateManagers($('#assigned_to').val(), initialReportingManagerIds);
            }

            // --- Location Scripts ---
            function populateStates(selector) {
                let stateSelects = $(selector);
                stateSelects.empty().append('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        stateSelects.empty().append('<option value="">Select State</option>');
                        $.each(data, function(key, state) {
                            stateSelects.append('<option value="' + state.id + '">' + state.name + '</option>');
                        });
                    }
                });
            }

            function populateCities(stateElement, cityElement, pincodeElement) {
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
                                $(cityElement).append($('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode));
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
                                // Load cities for the found state, then select the city
                                let stateId = data.state.id;
                                $(citySelect).empty().append('<option value="">Loading...</option>');

                                $.ajax({
                                    url: '{{ url("get-cities") }}/' + stateId,
                                    type: 'GET',
                                    success: function(cities) {
                                        $(citySelect).empty().append('<option value="">Select City</option>');
                                        $.each(cities, function(key, city) {
                                            $(citySelect).append($('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode));
                                        });
                                        // Select the city
                                        $(citySelect).val(data.city.id);
                                    }
                                });
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

                    $('#permanent_state').val(currentStateVal);
                    // Need to populate cities for permanent state
                    let permanentCitySelect = $('#permanent_city');
                    permanentCitySelect.empty().append('<option value="">Loading...</option>');

                    if (currentStateVal) {
                         $.ajax({
                            url: '{{ url("get-cities") }}/' + currentStateVal,
                            type: 'GET',
                            success: function(cities) {
                                permanentCitySelect.empty().append('<option value="">Select City</option>');
                                $.each(cities, function(key, city) {
                                    permanentCitySelect.append($('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode));
                                });
                                permanentCitySelect.val(currentCityVal);
                            }
                        });
                    }
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Vehicle Logic ---
            function populateVehicleNames(manufactureId, container) {
                let vehicleSelect = container.find('.vehicle-name-select');
                vehicleSelect.empty().append('<option value="">Loading...</option>');

                if (manufactureId) {
                    $.ajax({
                        url: '{{ url("get-vehicle-names") }}/' + manufactureId,
                        type: 'GET',
                        success: function(data) {
                            vehicleSelect.empty().append('<option value="">Select Vehicle</option>');
                            $.each(data, function(key, name) {
                                vehicleSelect.append('<option value="' + name + '">' + name + '</option>');
                            });
                        }
                    });
                } else {
                    vehicleSelect.empty().append('<option value="">Select Manufacturer First</option>');
                }
            }

            function populateVariants(manufactureId, vehicleName, container) {
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
                                variantSelect.append($('<option></option>').attr('value', item.variant).text(item.variant).data('id', item.id));
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

                populateVariants(mfgId, vehicleName, container);
            });

            $(document).on('change', '.variant-select', function() {
                let selectedId = $(this).find(':selected').data('id');
                $('#vehicle_name_id').val(selectedId);
            });

            // --- Lead Type Logic ---
            $('#lead_type').change(function() {
                let val = $(this).val();
                $('#loan_section, #rto_section, #insurance_section, #job_profile_section').hide();
                // Disable inputs in hidden sections to prevent submission
                $('#loan_section input, #loan_section select, #rto_section input, #rto_section select, #insurance_section input, #insurance_section select, #job_profile_section input, #job_profile_section select').prop('disabled', true);

                // Residence Type logic
                if (val === 'RTO' || val === 'Insurance') {
                    $('#residence_type_div').hide();
                    $('select[name="residence_type"]').prop('disabled', true);
                    if (val === 'RTO') {
                        $('#lead_source_div').hide();
                        $('#lead_source').prop('disabled', true);
                        $('#lead_source_person_name_div').hide();
                        $('#lead_source_person_name').prop('disabled', true);
                    } else {
                        $('#lead_source_div').show();
                        $('#lead_source').prop('disabled', false);
                        $('#lead_source_person_name').prop('disabled', false);
                    }
                    $('#default_address_proof_div').hide();
                } else {
                    $('#residence_type_div').show();
                    $('select[name="residence_type"]').prop('disabled', false);
                    $('#lead_source_div').show();
                    $('#lead_source').prop('disabled', false);
                    $('#lead_source_person_name').prop('disabled', false);
                    $('#default_address_proof_div').show();
                }

                syncAssignmentSection();
                toggleCoGuarantorSection();

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
                 // After showing/hiding main sections, re-trigger hypothecation logic
                $('#hypothecation_status').trigger('change');
            }).trigger('change');

            // --- Insurance Type Logic ---
            $('#insurance_type').change(function() {
                let val = $(this).val();
                $('#ins_vehicle_div, #ins_health_div').hide();

                if (val === 'Vehicle') {
                    $('#ins_vehicle_div').show();
                } else if (val === 'Health') {
                    $('#ins_health_div').show();
                }
            }).trigger('change');

            $('#insurance_coverage_type').change(function() {
                if($(this).val() === 'Addon') {
                    $('#ins_addon_desc_div').show();
                } else {
                    $('#ins_addon_desc_div').hide();
                }
            });

            // --- Other Form Logic ---
            $('#job_profile_type').change(function() {
                let val = $(this).val();
                $('#nature_of_work_div, #salaried_type_div, #business_proof_main_div, #business_stability_div, #salary_details_div, #farmer_fields').hide();

                if (val === 'Farmer') {
                    $('#company_name_div, #designation_div').hide();
                    $('#farmer_fields').show();
                } else {
                    $('#company_name_div, #designation_div').show();
                }

                if (val === 'Self Employed') {
                    $('#nature_of_work_div, #business_proof_main_div, #business_stability_div').show();
                } else if (val === 'Salaried') {
                    $('#salaried_type_div').show();
                    if ($('#income_proof_available').val() == '1') {
                        $('#salary_details_div').show();
                    }
                }
            }).trigger('change');

            $('#income_proof_available').change(function() {
                let profileType = $('#job_profile_type').val();
                if (profileType === 'Salaried' && $(this).val() == '1') {
                    $('#salary_details_div').show();
                } else {
                    $('#salary_details_div').hide();
                }
                $('#approx_income_div').toggle($(this).val() == '0');
            }).trigger('change');

            $('#has_itr').change(function() {
                $('#itr_details_div').toggle($(this).val() == '1');
                $('#itr_years_div').toggle($(this).val() == '1');
            });

            $('#business_proof_available').change(function() {
                $('#business_proof_doc_div').toggle($(this).val() == '1');
            });

            function toggleCoGuarantorSection() {
                const isInsurance = $('#lead_type').val() === 'Insurance' || $('#loan_type_select').val() === 'Insurance';

                if (isInsurance) {
                    $('#co_guarantor_section').hide();
                    $('#has_co_applicant').val('0');
                    $('#co_applicant_div').hide();
                    $('#has_guarantor').val('0');
                    $('#guarantor_div').hide();
                    return;
                }

                $('#co_guarantor_section').show();
                $('#co_applicant_div').toggle($('#has_co_applicant').val() == '1');
                $('#guarantor_div').toggle($('#has_guarantor').val() == '1');
            }

            $('#loan_type_select').change(function() {
                let val = $(this).val();
                $('#other_loan_name_div').toggle(val === 'Other');
                $('#vehicle_loan_details_div').toggle(val === 'Vehicle Loan');
                $('#property_details_div').toggle(val === 'Housing Loan' || val === 'LAP');

                let subLoanTypeDiv = $('#sub_loan_type_div');
                let subLoanTypeSelect = $('#sub_loan_type');
                let subLoanTypeLabel = $('#sub_loan_type_label');

                subLoanTypeSelect.empty().append('<option value="">Select Sub Loan Type</option>');

                if (val === 'Housing Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Housing Loan Type');
                    let options = ['Lap', 'Hl', 'Construction', 'P+C', 'Top-up', 'BT+Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt));
                    });
                } else if (val === 'Business Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Business Loan Type');
                    let options = ['Fresh Loan', 'BT Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt));
                    });
                } else if (val === 'Personal Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Personal Loan Type');
                    let options = ['Fresh Loan', 'BT Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt));
                    });
                } else {
                    subLoanTypeDiv.hide();
                }

                // For Personal or Business Loan, show Loan Purchase Type (New/BT)
                $('#loan_purchase_type_div').toggle(val === 'Personal Loan' || val === 'Business Loan');
                if (val !== 'Personal Loan' && val !== 'Business Loan') {
                    $('#loan_purchase_type').val('New').trigger('change');
                }

                toggleCoGuarantorSection();
            }).trigger('change');

            $('#loan_purchase_type').change(function() {
                let val = $(this).val();
                if (val === 'Balance Transfer') {
                    $('#hypothecation_status').val('1').trigger('change');
                }
                calculateTotal();
            });

            $('#sub_loan_type').change(function() {
                let val = $(this).val();
                let loanType = $('#loan_type_select').val();
                if ((loanType === 'Business Loan' || loanType === 'Personal Loan') && val === 'BT Top-up') {
                    $('#loan_purchase_type').val('Balance Transfer').trigger('change');
                } else if ((loanType === 'Business Loan' || loanType === 'Personal Loan') && val === 'Fresh Loan') {
                    $('#loan_purchase_type').val('New').trigger('change');
                }
            });

            $('#vehicle_loan_type').change(function() {
                $('#other_vehicle_loan_name_div').toggle($(this).val() === 'Other');
            });

            $('#vehicle_purchase_type').change(function() {
                let val = $(this).val();
                $('#new_purchase_div').toggle(val === 'New Purchase');
                $('#repurchase_div').toggle(['Repurchase', 'Refinance', 'Balance Transfer'].includes(val));

                if (val === 'Balance Transfer') {
                    $('#hypothecation_status').val('1').trigger('change');
                } else if (val === 'New Purchase') {
                    $('#hypothecation_status').val('0').trigger('change');
                }
                calculateTotal();
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

            $('#insurance_available').change(function() {
                $('#insurance_details_div').toggle($(this).val() == '1');
            });

            $('#hypothecation_status').change(function() {
                var isHp = $(this).val() == '1';
                $('#hp_related_fields, #total_loan_div').toggle(isHp);

                if (isHp) {
                    $('#hp_related_fields').find('input, select').prop('disabled', false).prop('required', false);
                } else {
                    $('#hp_related_fields').find('input').prop('disabled', true).val('');
                    $('#hp_related_fields').find('select').prop('disabled', true).prop('required', false).val('');
                }
                calculateTotal();
            });


            $('#rto_insurance_available').change(function() {
                $('#rto_insurance_details_div').toggle($(this).val() == '1');
            });

            $('#rto_hypothecation_status').change(function() {
                $('#rto_hp_details_div').toggle($(this).val() == '1');
            });

            $('#has_co_applicant').change(function() {
                if ($('#lead_type').val() === 'Insurance' || $('#loan_type_select').val() === 'Insurance') {
                    $(this).val('0');
                    $('#co_applicant_div').hide();
                    return;
                }
                $('#co_applicant_div').toggle($(this).val() == '1');
            });

            $('#has_guarantor').change(function() {
                if ($('#lead_type').val() === 'Insurance' || $('#loan_type_select').val() === 'Insurance') {
                    $(this).val('0');
                    $('#guarantor_div').hide();
                    return;
                }
                $('#guarantor_div').toggle($(this).val() == '1');
            });

            toggleCoGuarantorSection();

            function calculateTotal() {
                let oldLoan = 0;
                if ($('#hypothecation_status').val() == '1') {
                    oldLoan = parseFloat($('#old_loan_close_amount').val()) || 0;
                }
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

            // --- Premium Amount Collected (Insurance) ---
            function renderPremiumPaymentFields() {
                const type = ($('#premium_payment_type').val() || '').trim();
                let fields = '';

                if (type === 'Cash') {
                    fields = `
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="premium_payment_amount" class="form-control" value="{{ old('premium_payment_amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" value="{{ old('premium_payment_date') }}" required>
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
                                    <input type="text" name="premium_cheque_number" class="form-control" value="{{ old('premium_cheque_number') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="premium_payment_amount" class="form-control" value="{{ old('premium_payment_amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" value="{{ old('premium_payment_date') }}" required>
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
                                    <input type="text" name="premium_upi_id" class="form-control" value="{{ old('premium_upi_id') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="premium_payment_amount" class="form-control" value="{{ old('premium_payment_amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" value="{{ old('premium_payment_date') }}" required>
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
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="premium_payment_amount" class="form-control" value="{{ old('premium_payment_amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_holder_name" class="form-control" value="{{ old('premium_account_holder_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_account_number" class="form-control" value="{{ old('premium_account_number') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="premium_payment_date" class="form-control" value="{{ old('premium_payment_date') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_bank_name" class="form-control" value="{{ old('premium_bank_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>IFSC Code <span class="text-danger">*</span></label>
                                    <input type="text" name="premium_ifsc_code" class="form-control" value="{{ old('premium_ifsc_code') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input type="file" name="premium_bank_transfer_image" class="form-control-file">
                        </div>
                    `;
                }

                $('#premium_payment_fields').html(fields);
            }

            function togglePremiumSection() {
                const collected = ($('#premium_amount_collected').val() || '').trim();
                if (collected === 'Yes') {
                    $('#premium_payment_type_div').show();
                    $('#premium_payment_type').prop('disabled', false);
                    if (!$('#premium_payment_type').val()) {
                        $('#premium_payment_type').val('Cash');
                    }
                    renderPremiumPaymentFields();
                } else {
                    $('#premium_payment_type_div').hide();
                    $('#premium_payment_type').val('').prop('disabled', true);
                    $('#premium_payment_fields').html('');
                }
            }

            $('#premium_amount_collected').on('change', togglePremiumSection);
            $('#premium_payment_type').on('change', renderPremiumPaymentFields);
            togglePremiumSection();

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

                            // Also add to selects that use name as value (like rto_mfg_name)
                            let newOptionByName = `<option value="${response.manufacture.name}" data-id="${response.manufacture.id}">${response.manufacture.name}</option>`;
                            $('select[name="rto_mfg_name"], select[name="ins_mfg_name"]').append(newOptionByName);

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
        });
    </script>
@stop
