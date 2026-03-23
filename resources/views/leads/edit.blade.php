@extends('adminlte::page')

@section('title', 'Edit Lead')

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
        .document-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
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
    <h1>Edit Lead</h1>
@stop

@section('content')
    <div id="alert-container"></div>

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

                {{-- This will hold the actual ID of the selected variant --}}
                <input type="hidden" name="vehicle_name_id" id="vehicle_name_id" value="{{ old('vehicle_name_id', $lead->vehicle_name_id) }}">


                {{-- Hidden fields for "Qualify on Save" flow --}}
                <input type="hidden" name="qualify_on_save" value="{{ old('bank_id') ? '1' : '0' }}">
                @if(old('bank_id'))
                    <input type="hidden" name="bank_id" value="{{ old('bank_id') }}">
                    <input type="hidden" name="bank_branch_id" value="{{ old('bank_branch_id') }}">
                @endif

                {{-- Basic Details --}}
                <h5>Basic Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lead Type <span class="text-danger">*</span></label>
                            <select name="lead_type" id="lead_type" class="form-control @error('lead_type') is-invalid @enderror" required>
                                <option value="">Select Lead Type</option>
                                <option value="Loan" {{ old('lead_type', $lead->lead_type) == 'Loan' ? 'selected' : '' }}>Loan</option>
                                <option value="RTO" {{ old('lead_type', $lead->lead_type) == 'RTO' ? 'selected' : '' }}>RTO</option>
                                <option value="Insurance" {{ old('lead_type', $lead->lead_type) == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                            @error('lead_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $lead->customer_name) }}" required>
                            @error('customer_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $lead->father_name) }}" required>
                            @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Enquire At (Firm) <span class="text-danger">*</span></label>
                            <select name="firm_id" id="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
                                <option value="">Select Firm</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $lead->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
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
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $lead->contact_number) }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror" value="{{ old('aadhar_number', $lead->aadhar_number) }}" placeholder="12-digit Aadhar Number">
                            @error('aadhar_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead->email) }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3" id="residence_type_div">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control @error('residence_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="Rented" {{ old('residence_type', $lead->residence_type) == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Own" {{ old('residence_type', $lead->residence_type) == 'Own' ? 'selected' : '' }}>Own</option>
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
                                <option value="Self" {{ old('lead_source', $lead->lead_source) == 'Self' ? 'selected' : '' }}>Self</option>
                                <option value="Social media" {{ old('lead_source', $lead->lead_source) == 'Social media' ? 'selected' : '' }}>Social media</option>
                                <option value="Any person" {{ strtolower(old('lead_source', $lead->lead_source)) == 'any person' ? 'selected' : '' }}>Any person</option>
                            </select>
                            @error('lead_source') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4" id="lead_source_person_name_div" style="display:none;">
                        <div class="form-group">
                            <label>Person Name</label>
                            <div class="input-group">
                                <select name="lead_source_person_name" id="lead_source_person_name" class="form-control select2 @error('lead_source_person_name') is-invalid @enderror">
                                    @if($lead->lead_source_person_name)
                                        <option value="{{ $lead->lead_source_person_name }}" selected>{{ $lead->lead_source_person_name }}</option>
                                    @endif
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addReferralPersonModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            @error('lead_source_person_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Picture <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <input type="file" name="profile_picture" class="form-control-file @error('profile_picture') is-invalid @enderror">
                            @error('profile_picture') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control @error('current_address_line') is-invalid @enderror" value="{{ old('current_address_line', $lead->current_address_line) }}" required>
                            @error('current_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input @error('current_address_pincode') is-invalid @enderror" value="{{ old('current_address_pincode', $lead->current_address_pincode) }}">
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
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control @error('permanent_address_line') is-invalid @enderror" value="{{ old('permanent_address_line', $lead->permanent_address_line) }}" required>
                            @error('permanent_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input @error('permanent_address_pincode') is-invalid @enderror" value="{{ old('permanent_address_pincode', $lead->permanent_address_pincode) }}">
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof Type</label>
                            <select name="proof_id_type" class="form-control @error('proof_id_type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                <option value="Aadhar" {{ old('proof_id_type', $lead->proof_id_type) == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                <option value="Voter Id" {{ old('proof_id_type', $lead->proof_id_type) == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                <option value="Driving Licence" {{ old('proof_id_type', $lead->proof_id_type) == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                <option value="Pancard" {{ old('proof_id_type', $lead->proof_id_type) == 'Pancard' ? 'selected' : '' }}>Pancard</option>
                                <option value="Passport" {{ old('proof_id_type', $lead->proof_id_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                <option value="Electricity Bill" {{ old('proof_id_type', $lead->proof_id_type) == 'Electricity Bill' ? 'selected' : '' }}>Electricity Bill</option>
                            </select>
                            @error('proof_id_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof Upload <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                            <div id="addressProofDropzone" class="dropzone">
                                <div class="dz-message">
                                    <h3>Drop files here or click to upload Document.</h3>
                                    <span class="note">Multiple files allowed. Previews will be shown below.</span>
                                </div>
                            </div>
                        </div>
                        {{-- Existing Documents --}}
                        @if($lead->getMedia('loan_documents')->count() > 0)
                            <div class="mt-2">
                                <h6>Existing Address Proofs:</h6>
                                <div class="row" id="existing-docs">
                                    @foreach($lead->getMedia('loan_documents') as $doc)
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
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
                                    <option value="Self Employed" {{ old('job_profile_type', $lead->job_profile_type) == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                    <option value="Farmer" {{ old('job_profile_type', $lead->job_profile_type) == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                                    <option value="Salaried" {{ old('job_profile_type', $lead->job_profile_type) == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                                </select>
                                @error('job_profile_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="company_name_div">
                            <div class="form-group">
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="other_company_name" class="form-control @error('other_company_name') is-invalid @enderror" value="{{ old('other_company_name', $lead->other_company_name) }}" required>
                                @error('other_company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="designation_div">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $lead->designation) }}" required>
                                @error('designation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="nature_of_work_div" style="display:none;">
                            <div class="form-group">
                                <label>Nature of Work</label>
                                <input type="text" name="nature_of_work" class="form-control @error('nature_of_work') is-invalid @enderror" value="{{ old('nature_of_work', $lead->nature_of_work) }}">
                                @error('nature_of_work') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="business_stability_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Stability</label>
                                <input type="text" name="business_stability" id="business_stability_datepicker" class="form-control @error('business_stability') is-invalid @enderror" value="{{ old('business_stability', $lead->business_stability) }}" placeholder="YYYY">
                                @error('business_stability') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4" id="salaried_type_div" style="display:none;">
                            <div class="form-group">
                                <label>Salaried Type</label>
                                <select name="salaried_type" id="salaried_type" class="form-control @error('salaried_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Government" {{ old('salaried_type', $lead->salaried_type) == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Corporate" {{ old('salaried_type', $lead->salaried_type) == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="Other" {{ old('salaried_type', $lead->salaried_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('salaried_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Income Proof Available?</label>
                                <select name="income_proof_available" id="income_proof_available" class="form-control @error('income_proof_available') is-invalid @enderror">
                                    <option value="0" {{ old('income_proof_available', $lead->income_proof_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('income_proof_available', $lead->income_proof_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('income_proof_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="salary_details_div" style="display:none;">
                                <div class="form-group">
                                    <label>Last Month Salary</label>
                                    <input type="number" step="0.01" name="last_month_salary" class="form-control @error('last_month_salary') is-invalid @enderror" value="{{ old('last_month_salary', $lead->last_month_salary) }}">
                                    @error('last_month_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Salary Slip</label>
                                    <input type="file" name="salary_slip" class="form-control-file @error('salary_slip') is-invalid @enderror">
                                    @error('salary_slip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    @if($lead->salary_slip_path) <small><a href="{{ asset('storage/' . $lead->salary_slip_path) }}" target="_blank">View Current</a></small> @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label>ITR Available?</label>
                                <select name="has_itr" id="has_itr" class="form-control @error('has_itr') is-invalid @enderror">
                                    <option value="0" {{ old('has_itr', $lead->has_itr) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('has_itr', $lead->has_itr) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('has_itr') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="itr_details_div" style="display:none;">
                                <div class="form-group">
                                    <label>Last ITR Amount</label>
                                    <input type="number" step="0.01" name="itr_amount" class="form-control @error('itr_amount') is-invalid @enderror" value="{{ old('itr_amount', $lead->itr_amount) }}">
                                    @error('itr_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div id="itr_years_div" style="display:none;">
                                    <div class="form-group">
                                        <label>ITR Year(s)</label>
                                        <select name="itr_years" class="form-control @error('itr_years') is-invalid @enderror">
                                            <option value="">Select Year</option>
                                            <option value="1" {{ old('itr_years', $lead->itr_years) == 1 ? 'selected' : '' }}>1 Year</option>
                                            <option value="2" {{ old('itr_years', $lead->itr_years) == 2 ? 'selected' : '' }}>2 Year</option>
                                            <option value="3" {{ old('itr_years', $lead->itr_years) == 3 ? 'selected' : '' }}>3 Year</option>
                                        </select>
                                        @error('itr_years') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>ITR Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="itr_document" class="form-control-file @error('itr_document') is-invalid @enderror">
                                    @error('itr_document') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    @if($lead->itr_document_path) <small><a href="{{ asset('storage/' . $lead->itr_document_path) }}" target="_blank">View Current</a></small> @endif
                                </div>
                            </div>
                            <div id="approx_income_div">
                                <div class="form-group">
                                    <label>Approx. Income</label>
                                    <input type="number" step="0.01" name="approx_income" class="form-control @error('approx_income') is-invalid @enderror" value="{{ old('approx_income', $lead->approx_income) }}">
                                    @error('approx_income') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="business_proof_main_div" style="display:none;">
                            <div class="form-group">
                                <label>Business Proof Available?</label>
                                <select name="business_proof_available" id="business_proof_available" class="form-control @error('business_proof_available') is-invalid @enderror">
                                    <option value="0" {{ old('business_proof_available', $lead->business_proof_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('business_proof_available', $lead->business_proof_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('business_proof_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div id="business_proof_doc_div" style="display:none;">
                                <div class="form-group">
                                    <label>Business Proof Document <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
                                    <input type="file" name="business_proof_document" class="form-control-file @error('business_proof_document') is-invalid @enderror">
                                    @error('business_proof_document') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                                <select name="loan_type" id="loan_type_select" class="form-control @error('loan_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Vehicle Loan" {{ old('loan_type', $lead->loan_type) == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                                    <option value="Housing Loan" {{ old('loan_type', $lead->loan_type) == 'Housing Loan' ? 'selected' : '' }}>Housing Loan</option>
                                    <option value="LAP" {{ old('loan_type', $lead->loan_type) == 'LAP' ? 'selected' : '' }}>Loan Against Property (LAP)</option>
                                    <option value="Business Loan" {{ old('loan_type', $lead->loan_type) == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                                    <option value="Personal Loan" {{ old('loan_type', $lead->loan_type) == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                                    <option value="Other" {{ old('loan_type', $lead->loan_type) == 'Other' ? 'selected' : '' }}>Other</option>
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
                                <input type="text" name="other_loan_name" class="form-control @error('other_loan_name') is-invalid @enderror" value="{{ old('other_loan_name', $lead->other_loan_name) }}">
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
                                        <option value="New" {{ old('loan_purchase_type', $lead->loan_purchase_type) == 'New' ? 'selected' : '' }}>New</option>
                                        <option value="Balance Transfer" {{ old('loan_purchase_type', $lead->loan_purchase_type) == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer (BT)</option>
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
                                    <input type="text" name="property_location" class="form-control @error('property_location') is-invalid @enderror" value="{{ old('property_location', $lead->property_location) }}">
                                    @error('property_location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Property Area</label>
                                    <input type="text" name="property_area" class="form-control @error('property_area') is-invalid @enderror" value="{{ old('property_area', $lead->property_area) }}">
                                    @error('property_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Property Approx. Value</label>
                                    <input type="number" step="0.01" name="property_approx_value" class="form-control @error('property_approx_value') is-invalid @enderror" value="{{ old('property_approx_value', $lead->property_approx_value) }}">
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
                                        <option value="Diverted" {{ old('property_type', $lead->property_type) == 'Diverted' ? 'selected' : '' }}>Diverted</option>
                                        <option value="Non Diverted" {{ old('property_type', $lead->property_type) == 'Non Diverted' ? 'selected' : '' }}>Non Diverted</option>
                                        <option value="Agri Land" {{ old('property_type', $lead->property_type) == 'Agri Land' ? 'selected' : '' }}>Agri Land</option>
                                        <option value="other" {{ old('property_type', $lead->property_type) == 'other' ? 'selected' : '' }}>other</option>
                                    </select>
                                    @error('property_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Land Type</label>
                                    <select name="land_type" class="form-control @error('land_type') is-invalid @enderror">
                                        <option value="">Select Land Type</option>
                                        <option value="Farming Land" {{ old('land_type', $lead->land_type) == 'Farming Land' ? 'selected' : '' }}>Farming Land</option>
                                        <option value="Plot" {{ old('land_type', $lead->land_type) == 'Plot' ? 'selected' : '' }}>Plot</option>
                                        <option value="House" {{ old('land_type', $lead->land_type) == 'House' ? 'selected' : '' }}>House</option>
                                        <option value="Flat" {{ old('land_type', $lead->land_type) == 'Flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="Commercial land" {{ old('land_type', $lead->land_type) == 'Commercial land' ? 'selected' : '' }}>Commercial land</option>
                                        <option value="Commercial property" {{ old('land_type', $lead->land_type) == 'Commercial property' ? 'selected' : '' }}>Commercial property</option>
                                        <option value="other" {{ old('land_type', $lead->land_type) == 'other' ? 'selected' : '' }}>other</option>
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
                                        <option value="Car" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Car' ? 'selected' : '' }}>Car</option>
                                        <option value="Commercial Vehicle" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
                                        <option value="Construction Equipment" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Construction Equipment' ? 'selected' : '' }}>Construction Equipment</option>
                                        <option value="Tractor" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Tractor' ? 'selected' : '' }}>Tractor</option>
                                        <option value="Other" {{ old('vehicle_loan_type', $lead->vehicle_loan_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('vehicle_loan_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4" id="other_vehicle_loan_name_div" style="display:none;">
                                <div class="form-group">
                                    <label>Other Vehicle Name</label>
                                    <input type="text" name="other_vehicle_loan_name" class="form-control @error('other_vehicle_loan_name') is-invalid @enderror" value="{{ old('other_vehicle_loan_name', $lead->other_vehicle_loan_name) }}">
                                    @error('other_vehicle_loan_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_purchase_type" id="vehicle_purchase_type" class="form-control @error('vehicle_purchase_type') is-invalid @enderror">
                                        <option value="">Select Purchase Type</option>
                                        <option value="New Purchase" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'New Purchase' ? 'selected' : '' }}>New Purchase</option>
                                        <option value="Repurchase" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Repurchase' ? 'selected' : '' }}>Repurchase</option>
                                        <option value="Refinance" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Refinance' ? 'selected' : '' }}>Refinance</option>
                                        <option value="Balance Transfer" {{ old('vehicle_purchase_type', $lead->vehicle_purchase_type) == 'Balance Transfer' ? 'selected' : '' }}>Balance Transfer</option>
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
                                                    <option value="{{ $mfg->id }}" {{ old('manufacture_id', $lead->manufacture_id) == $mfg->id ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
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
                                        <label>Showroom Price</label>
                                        <input type="number" step="0.01" name="showroom_price" class="form-control @error('showroom_price') is-invalid @enderror" value="{{ old('showroom_price', $lead->showroom_price) }}">
                                        @error('showroom_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>On Road Price</label>
                                        <input type="number" step="0.01" name="on_road_price" class="form-control @error('on_road_price') is-invalid @enderror" value="{{ old('on_road_price', $lead->on_road_price) }}">
                                        @error('on_road_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Repurchase/Refinance/BT --}}
                        <div id="repurchase_div" style="display:none;">
                            <h6>RC Details</h6>
                            <div class="row">
                                <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rc_number" class="form-control @error('rc_number') is-invalid @enderror" value="{{ old('rc_number', $lead->rc_number) }}">@error('rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" value="{{ old('engine_number', $lead->engine_number) }}">@error('engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror" value="{{ old('chassis_number', $lead->chassis_number) }}">@error('chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="mfg_year_datepicker_loan" class="form-control @error('mfg_year') is-invalid @enderror" value="{{ old('mfg_year', $lead->mfg_year) }}" placeholder="MM-YYYY">@error('mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Mfg Name</label>
                                        <div class="input-group">
                                            <select name="rto_mfg_name" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
                                                <option value="">Select Manufacturer</option>
                                                @foreach($manufactures as $mfg)
                                                    <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('rto_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
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
                                <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="owner_serial_number" class="form-control @error('owner_serial_number') is-invalid @enderror" value="{{ old('owner_serial_number', $lead->owner_serial_number) }}">@error('owner_serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>

                            <h6>Insurance</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Insurance Available?</label>
                                        <select name="insurance_available" id="insurance_available" class="form-control @error('insurance_available') is-invalid @enderror">
                                            <option value="0" {{ old('insurance_available', $lead->insurance_available) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('insurance_available', $lead->insurance_available) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('insurance_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-9" id="insurance_details_div" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Insurance Company</label>
                                                <select name="insurance_id" class="form-control @error('insurance_id') is-invalid @enderror">
                                                    <option value="">Select Insurance Company</option>
                                                    @foreach($insurances as $ins)
                                                        <option value="{{ $ins->id }}" {{ old('insurance_id', $lead->insurance_id) == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('insurance_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control @error('insurance_start_date') is-invalid @enderror" value="{{ old('insurance_start_date', $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '') }}">@error('insurance_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                        <div class="col-md-3"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control @error('insurance_end_date') is-invalid @enderror" value="{{ old('insurance_end_date', $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '') }}">@error('insurance_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                        <div class="col-md-2"><div class="form-group"><label>IDV Amount</label><input type="number" step="0.01" name="insurance_amount" class="form-control @error('insurance_amount') is-invalid @enderror" value="{{ old('insurance_amount', $lead->insurance_amount) }}">@error('insurance_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                    </div>
                                </div>
                            </div>

                            <h6>Hypothecation (HP)</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>HP?</label>
                                        <select name="hypothecation_status" id="hypothecation_status" class="form-control @error('hypothecation_status') is-invalid @enderror">
                                            <option value="0" {{ old('hypothecation_status', $lead->hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('hypothecation_status', $lead->hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
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
                                        <input type="text" name="old_financer" class="form-control @error('old_financer') is-invalid @enderror" value="{{ old('old_financer', $lead->old_financer) }}">
                                        @error('old_financer') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>EMI Paid Count</label>
                                        <input type="number" name="emi_paid_count" class="form-control @error('emi_paid_count') is-invalid @enderror" value="{{ old('emi_paid_count', $lead->emi_paid_count) }}">
                                        @error('emi_paid_count') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Old Loan Close Amount</label>
                                        <input type="number" step="0.01" name="old_loan_close_amount" id="old_loan_close_amount" class="form-control @error('old_loan_close_amount') is-invalid @enderror" value="{{ old('old_loan_close_amount', $lead->old_loan_close_amount) }}">
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
                                    <input type="number" step="0.01" name="required_in_hand_amount" id="required_in_hand_amount" class="form-control @error('required_in_hand_amount') is-invalid @enderror" value="{{ old('required_in_hand_amount', $lead->required_in_hand_amount) }}" required>
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
                        <div class="col-md-3"><div class="form-group"><label>RC Number <span class="text-danger">*</span></label><input type="text" name="rto_rc_number" class="form-control @error('rto_rc_number') is-invalid @enderror" value="{{ old('rto_rc_number', $lead->rc_number) }}">@error('rto_rc_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control @error('rto_engine_number') is-invalid @enderror" value="{{ old('rto_engine_number', $lead->engine_number) }}">@error('rto_engine_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control @error('rto_chassis_number') is-invalid @enderror" value="{{ old('rto_chassis_number', $lead->chassis_number) }}">@error('rto_chassis_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="mfg_year_datepicker_rto" class="form-control @error('mfg_year') is-invalid @enderror" value="{{ old('mfg_year', $lead->mfg_year) }}" placeholder="MM-YYYY">@error('mfg_year') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mfg Name</label>
                                <div class="input-group">
                                    <select name="rto_mfg_name" class="form-control manufacture-select @error('rto_mfg_name') is-invalid @enderror">
                                        <option value="">Select Manufacturer</option>
                                        @foreach($manufactures as $mfg)
                                            <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('rto_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
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
                        <div class="col-md-3"><div class="form-group"><label>Owner Srl No</label><input type="text" name="rto_owner_serial_number" class="form-control @error('rto_owner_serial_number') is-invalid @enderror" value="{{ old('rto_owner_serial_number', $lead->owner_serial_number) }}">@error('rto_owner_serial_number') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Insurance Available?</label>
                                <select name="insurance_available" id="rto_insurance_available" class="form-control @error('insurance_available') is-invalid @enderror">
                                    <option value="0" {{ old('insurance_available', $lead->insurance_available) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('insurance_available', $lead->insurance_available) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('insurance_available') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-9" id="rto_insurance_details_div" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Insurance Company</label>
                                        <select name="insurance_id" class="form-control @error('insurance_id') is-invalid @enderror">
                                            <option value="">Select Insurance Company</option>
                                            @foreach($insurances as $ins)
                                                <option value="{{ $ins->id }}" {{ old('insurance_id', $lead->insurance_id) == $ins->id ? 'selected' : '' }}>{{ $ins->company_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('insurance_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4"><div class="form-group"><label>Start Date</label><input type="date" name="insurance_start_date" class="form-control @error('insurance_start_date') is-invalid @enderror" value="{{ old('insurance_start_date', $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '') }}">@error('insurance_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                                <div class="col-md-4"><div class="form-group"><label>End Date</label><input type="date" name="insurance_end_date" class="form-control @error('insurance_end_date') is-invalid @enderror" value="{{ old('insurance_end_date', $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '') }}">@error('insurance_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Hypothecation (HP)?</label>
                                <select name="rto_hypothecation_status" id="rto_hypothecation_status" class="form-control @error('rto_hypothecation_status') is-invalid @enderror">
                                    <option value="0" {{ old('rto_hypothecation_status', $lead->rto_hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('rto_hypothecation_status', $lead->rto_hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
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
                                                <option value="{{ $bank->id }}" {{ old('rto_bank_id', $lead->rto_bank_id) == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NOC Available?</label>
                                        <select name="rto_noc_status" class="form-control">
                                            <option value="No" {{ old('rto_noc_status', $lead->rto_noc_status) == 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ old('rto_noc_status', $lead->rto_noc_status) == 'Yes' ? 'selected' : '' }}>Yes</option>
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
                                    <option value="Vehicle" {{ old('insurance_type', $lead->insurance_type) == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                                    <option value="Health" {{ old('insurance_type', $lead->insurance_type) == 'Health' ? 'selected' : '' }}>Health</option>
                                    <option value="Property" {{ old('insurance_type', $lead->insurance_type) == 'Property' ? 'selected' : '' }}>Property</option>
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
                                                <option value="{{ $mfg->name }}" data-id="{{ $mfg->id }}" {{ old('ins_mfg_name', $lead->mfg_name) == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addManufactureModal">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
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
                                    <input type="number" step="0.01" name="insurance_amount" class="form-control @error('insurance_amount') is-invalid @enderror" value="{{ old('insurance_amount', $lead->insurance_amount) }}">
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
                                        <option value="First" {{ old('insurance_coverage_type', $lead->insurance_coverage_type) == 'First' ? 'selected' : '' }}>First Party</option>
                                        <option value="Third" {{ old('insurance_coverage_type', $lead->insurance_coverage_type) == 'Third' ? 'selected' : '' }}>Third Party</option>
                                        <option value="Addon" {{ old('insurance_coverage_type', $lead->insurance_coverage_type) == 'Addon' ? 'selected' : '' }}>Addon Policy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="ins_addon_desc_div" style="display:none;">
                                <div class="form-group">
                                    <label>Addon Details</label>
                                    <input type="text" name="insurance_addon_description" class="form-control" value="{{ old('insurance_addon_description', $lead->insurance_addon_description) }}" placeholder="Specify addon details">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-3"><div class="form-group"><label>RC Number</label><input type="text" name="rto_rc_number" class="form-control" value="{{ old('rto_rc_number', $lead->rc_number) }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Engine Number</label><input type="text" name="rto_engine_number" class="form-control" value="{{ old('rto_engine_number', $lead->engine_number) }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Chassis Number</label><input type="text" name="rto_chassis_number" class="form-control" value="{{ old('rto_chassis_number', $lead->chassis_number) }}"></div></div>
                             <div class="col-md-3"><div class="form-group"><label>Mfg Year</label><input type="text" name="mfg_year" id="ins_mfg_year_datepicker" class="form-control" value="{{ old('mfg_year', $lead->mfg_year) }}" placeholder="MM-YYYY"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hypothecation (HP)?</label>
                                    <select name="ins_hypothecation_status" id="ins_hypothecation_status" class="form-control">
                                        <option value="0" {{ old('ins_hypothecation_status', $lead->hypothecation_status) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('ins_hypothecation_status', $lead->hypothecation_status) == 1 ? 'selected' : '' }}>Yes</option>
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
                                        <option value="Individual" {{ old('health_policy_type', $lead->health_policy_type) == 'Individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="Family Floater" {{ old('health_policy_type', $lead->health_policy_type) == 'Family Floater' ? 'selected' : '' }}>Family Floater</option>
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
                                    <option value="Aadhar" {{ old('insurance_proof_type', $lead->proof_id_type) == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                    <option value="Voter Id" {{ old('insurance_proof_type', $lead->proof_id_type) == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                    <option value="Driving Licence" {{ old('insurance_proof_type', $lead->proof_id_type) == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                    <option value="Passport" {{ old('insurance_proof_type', $lead->proof_id_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="Electricity Bill" {{ old('insurance_proof_type', $lead->proof_id_type) == 'Electricity Bill' ? 'selected' : '' }}>Electricity Bill</option>
                                </select>
                                @error('insurance_proof_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload Document <small class="text-muted">(JPG, PNG, PDF)</small></label>
                                <input type="file" name="insurance_address_proof" class="form-control-file @error('insurance_address_proof') is-invalid @enderror">
                                @error('insurance_address_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                @if($lead->getFirstMediaUrl('insurance_address_proof'))
                                    <a href="{{ $lead->getFirstMediaUrl('insurance_address_proof') }}" target="_blank">View Current</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Co-Applicant --}}
                <h5>Co-Applicant Details</h5>
                <div class="form-group">
                    <label>Has Co-Applicant?</label>
                    <select name="has_co_applicant" id="has_co_applicant" class="form-control @error('has_co_applicant') is-invalid @enderror" style="width: 200px;">
                        <option value="0" {{ old('has_co_applicant', $lead->has_co_applicant) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('has_co_applicant', $lead->has_co_applicant) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error('has_co_applicant') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div id="co_applicant_div" style="display:none;">
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Name</label><input type="text" name="co_name" class="form-control @error('co_name') is-invalid @enderror" value="{{ old('co_name', $lead->co_name) }}">@error('co_name') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                        <div class="col-md-4"><div class="form-group"><label>Relation</label><input type="text" name="co_relation" class="form-control @error('co_relation') is-invalid @enderror" value="{{ old('co_relation', $lead->co_relation) }}">@error('co_relation') <span class="invalid-feedback">{{ $message }}</span> @enderror</div></div>
                    </div>
                </div>

                <hr>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Team Lead'))
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assign To employee <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <button type="button" id="submitBtn" class="btn btn-primary mt-3">{{ old('bank_id') ? 'Save & Qualify' : 'Save Lead' }}</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
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

            // --- Select2 for Lead Source Person ---
            $('#lead_source_person_name').select2({
                placeholder: 'Search for a person...',
                minimumInputLength: 2,
                width: '100%',
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

            // --- Dropzone Initialization ---
            var myDropzone = new Dropzone("#addressProofDropzone", {
                url: "{{ route(get_route_prefix() . 'leads.update', $lead->id) }}",
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
                        if ($("#editLeadForm").valid()) {
                            if (dz.getQueuedFiles().length > 0) {
                                dz.processQueue();
                            } else {
                                // If no new files, submit form normally via AJAX
                                var formData = new FormData($("#editLeadForm")[0]);
                                $.ajax({
                                    url: $("#editLeadForm").attr('action'),
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        window.location.href = "{{ route(get_route_prefix() . 'leads.index') }}";
                                    },
                                    error: function(err) {
                                        alert("Error updating lead.");
                                    }
                                });
                            }
                        }
                    });

                    this.on("sendingmultiple", function(data, xhr, formData) {
                        var formArray = $("#editLeadForm").serializeArray();
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

            // --- Delete Document Logic ---
            $('.delete-doc').click(function() {
                if(confirm('Are you sure you want to delete this document?')) {
                    var docId = $(this).data('id');
                    var url = "{{ route(get_route_prefix() . 'leads.delete-media', ':id') }}".replace(':id', docId);

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

            // --- Validation ---
            $('#editLeadForm').validate({
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
                            return $('#assigned_to').length > 0;
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
                    assigned_to: "Please select a user to assign the lead to",
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
            $('#firm_id').change(function() {
                let firmId = $(this).val();
                let assignedToSelect = $('#assigned_to');

                if (assignedToSelect.length > 0) { // Only if the dropdown exists (Admin/Team Lead)
                    assignedToSelect.empty().append('<option value="">Loading...</option>');

                    if (firmId) {
                        $.ajax({
                            url: '{{ url("get-employees-by-firm") }}/' + firmId,
                            type: 'GET',
                            success: function(data) {
                                assignedToSelect.empty().append('<option value="">Select User</option>');
                                $.each(data, function(key, user) {
                                    assignedToSelect.append('<option value="' + user.id + '">' + user.name + '</option>');
                                });
                            }
                        });
                    } else {
                        assignedToSelect.empty().append('<option value="">Select Firm First</option>');
                    }
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
            function populateVehicleNames(manufactureId, container, selectedVehicleName = null) {
                let vehicleSelect = container.find('.vehicle-name-select');
                vehicleSelect.empty().append('<option value="">Loading...</option>');

                if (manufactureId) {
                    $.ajax({
                        url: '{{ url("get-vehicle-names") }}/' + manufactureId,
                        type: 'GET',
                        success: function(data) {
                            vehicleSelect.empty().append('<option value="">Select Vehicle</option>');
                            $.each(data, function(key, name) {
                                let option = $('<option></option>').attr('value', name).text(name);
                                if (name == selectedVehicleName) {
                                    option.prop('selected', true);
                                }
                                vehicleSelect.append(option);
                            });
                            // Trigger change to load variants if a vehicle is pre-selected
                            if(selectedVehicleName) {
                                vehicleSelect.trigger('change');
                            }
                        }
                    });
                } else {
                    vehicleSelect.empty().append('<option value="">Select Manufacturer First</option>');
                }
            }

            function populateVariants(manufactureId, vehicleName, container, selectedVariant = null) {
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
                                let option = $('<option></option>').attr('value', item.variant).text(item.variant).data('id', item.id);
                                if (item.variant == selectedVariant) {
                                    option.prop('selected', true);
                                    $('#vehicle_name_id').val(item.id); // Pre-select the ID
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

                let variantToSelect = container.find('.variant-select').data('selected');
                populateVariants(mfgId, vehicleName, container, variantToSelect);
            });

            $(document).on('change', '.variant-select', function() {
                let selectedId = $(this).find(':selected').data('id');
                $('#vehicle_name_id').val(selectedId);
            });

            // Initial population for Edit mode
            $('.manufacture-select').each(function() {
                let mfgId = $(this).find(':selected').data('id') || $(this).val();
                if(mfgId) {
                    let container = $(this).closest('.row');
                    let vehicleSelect = container.find('.vehicle-name-select');
                    let selectedVehicle = vehicleSelect.attr('name') === 'vehicle_name_id_display' ? '{{ $lead->vehicleName->name ?? $lead->model_name }}' : (vehicleSelect.attr('name') === 'rto_model_name' ? '{{ $lead->model_name }}' : '{{ $lead->model_name }}');

                    // Store the variant to be selected later
                    let variantSelect = container.find('.variant-select');
                    let selectedVariant = variantSelect.attr('name') === 'vehicle_variant' ? '{{ $lead->vehicleName->variant ?? $lead->variant }}' : (variantSelect.attr('name') === 'variant' ? '{{ $lead->variant }}' : '{{ $lead->variant }}');
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

                // Residence Type logic
                if (val === 'RTO' || val === 'Insurance') {
                    $('#residence_type_div').hide();
                    $('select[name="residence_type"]').prop('disabled', true);
                    $('#lead_source_div').hide();
                    $('#lead_source').prop('disabled', true);
                    $('#lead_source_person_name_div').hide();
                    $('#lead_source_person_name').prop('disabled', true);
                    $('#default_address_proof_div').hide();
                } else {
                    $('#residence_type_div').show();
                    $('select[name="residence_type"]').prop('disabled', false);
                    $('#lead_source_div').show();
                    $('#lead_source').prop('disabled', false);
                    $('#default_address_proof_div').show();
                }

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
            }).trigger('change');

            // --- Other Form Logic ---
            $('#job_profile_type').change(function() {
                let val = $(this).val();
                $('#nature_of_work_div, #salaried_type_div, #business_proof_main_div, #business_stability_div, #salary_details_div').hide();

                if (val === 'Farmer') {
                    $('#company_name_div, #designation_div').hide();
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
            }).trigger('change');

            // Trigger initial state for ITR
            $('#has_itr').trigger('change');

            $('#business_proof_available').change(function() {
                $('#business_proof_doc_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#loan_type_select').change(function() {
                let val = $(this).val();
                $('#other_loan_name_div').toggle(val === 'Other');
                $('#vehicle_loan_details_div').toggle(val === 'Vehicle Loan');
                $('#property_details_div').toggle(val === 'Housing Loan' || val === 'LAP');

                let subLoanTypeDiv = $('#sub_loan_type_div');
                let subLoanTypeSelect = $('#sub_loan_type');
                let subLoanTypeLabel = $('#sub_loan_type_label');

                let currentSubValue = '{{ $lead->sub_loan_type }}';
                subLoanTypeSelect.empty().append('<option value="">Select Sub Loan Type</option>');

                if (val === 'Housing Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Housing Loan Type');
                    let options = ['Lap', 'Hl', 'Construction', 'P+C', 'Top-up', 'BT+Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt).prop('selected', opt == currentSubValue));
                    });
                } else if (val === 'Business Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Business Loan Type');
                    let options = ['Fresh Loan', 'BT Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt).prop('selected', opt == currentSubValue));
                    });
                } else if (val === 'Personal Loan') {
                    subLoanTypeDiv.show();
                    subLoanTypeLabel.text('Sub Personal Loan Type');
                    let options = ['Fresh Loan', 'BT Top-up'];
                    $.each(options, function(i, opt) {
                        subLoanTypeSelect.append($('<option></option>').val(opt).text(opt).prop('selected', opt == currentSubValue));
                    });
                } else {
                    subLoanTypeDiv.hide();
                }

                // For Personal or Business Loan, show Loan Purchase Type (New/BT)
                $('#loan_purchase_type_div').toggle(val === 'Personal Loan' || val === 'Business Loan');
                if (val !== 'Personal Loan' && val !== 'Business Loan') {
                    $('#loan_purchase_type').val('New').trigger('change');
                }
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
            }).trigger('change');

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
            }).trigger('change');

            $('input[name="insurance_start_date"]').change(function() {
                var startDate = $(this).val();
                if (startDate) {
                    var start = new Date(startDate);
                    start.setFullYear(start.getFullYear() + 1);
                    var endDate = start.toISOString().split('T')[0];
                    $('input[name="insurance_end_date"]').val(endDate);
                }
            });

            $('#insurance_available').change(function() {
                $('#insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#hypothecation_status').change(function() {
                var isHp = $(this).val() == '1';
                $('#hp_related_fields, #total_loan_div').toggle(isHp);

                if (isHp) {
                    $('#hp_related_fields').find('input').prop('disabled', false);
                } else {
                    $('#hp_related_fields').find('input').prop('disabled', true).val('');
                }
                calculateTotal();
            }).trigger('change');

            $('#rto_insurance_available').change(function() {
                $('#rto_insurance_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#rto_hypothecation_status').change(function() {
                $('#rto_hp_details_div').toggle($(this).val() == '1');
            }).trigger('change');

            // --- Insurance Specific Bindings (NEW) ---
            $('#ins_hypothecation_status').val('{{ old('ins_hypothecation_status', $lead->hypothecation_status) }}').change(function() {
                // You can add logic here if HP section needs to be toggled within Insurance
            }).trigger('change');


            $('#has_co_applicant').change(function() {
                $('#co_applicant_div').toggle($(this).val() == '1');
            }).trigger('change');

            $('#has_guarantor').change(function() {
                $('#guarantor_div').toggle($(this).val() == '1');
            }).trigger('change');

            function calculateTotal() {
                let oldLoan = 0;
                if ($('#hypothecation_status').val() == '1') {
                    oldLoan = parseFloat($('#old_loan_close_amount').val()) || 0;
                }
                let inHand = parseFloat($('#required_in_hand_amount').val()) || 0;
                $('#total_loan_amount').val((oldLoan + inHand).toFixed(2));
            }

            $('#old_loan_close_amount, #required_in_hand_amount').on('input', calculateTotal);
            calculateTotal(); // Initial calculation

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

            // --- Lead Source Person Name Logic ---
            $('#lead_source').change(function() {
                if ($(this).val()) {
                    if ($(this).val().toLowerCase() === 'any person') {
                        $('#lead_source_person_name_div').show();
                    } else {
                        $('#lead_source_person_name_div').hide();
                    }
                } else {
                    $('#lead_source_person_name_div').hide();
                }
            }).trigger('change');

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
