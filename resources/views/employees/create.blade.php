@extends('adminlte::page')

@section('title', 'Create Employee')

@section('content_header')
    <h1>Create Employee</h1>
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
        .invalid-feedback {
            display: block;
        }
        .select2-container {
            width: 100% !important;
        }

        /* Permission Card Styling matching Role Edit */
        .permission-card {
            height: 100%;
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .permission-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
        }
        .permission-card .card-body {
            padding: 1.25rem;
        }
        .permission-item {
            margin-bottom: 0.5rem;
        }
        .role-badge {
            font-size: 0.7em;
            vertical-align: text-top;
            opacity: 0.8;
        }
    </style>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" id="createEmployeeForm">
                @csrf

                <h5>Personal Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee Type <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DOB</label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                            @error('dob') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-control @error('blood_group') is-invalid @enderror" value="{{ old('blood_group') }}">
                            @error('blood_group') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Joined Firm <span class="text-danger">*</span></label>
                            <select name="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                            @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address <span class="text-danger">*</span></h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input">
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
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input">
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proof ID Type</label>
                            <select name="proof_id_type" class="form-control">
                                <option value="">Select ID Type</option>
                                <option value="Aadhar" {{ old('proof_id_type') == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                <option value="Voter Id" {{ old('proof_id_type') == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                <option value="Driving Licence" {{ old('proof_id_type') == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                <option value="Pancard" {{ old('proof_id_type') == 'Pancard' ? 'selected' : '' }}>Pancard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof (File) <span class="text-danger">*</span></label>
                            <input type="file" name="address_proof" class="form-control-file @error('address_proof') is-invalid @enderror" required>
                            <small class="form-text text-muted">Allowed types: PDF, JPG, JPEG, PNG. Max size: 2MB.</small>
                            @error('address_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Education Details</h5>
                <div id="education-container">
                    {{-- Education fields will be added here by JS --}}
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-education">Add Education</button>

                <hr>
                <h5>Official Details</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Permissions</label>
                            <p class="text-muted small">
                                <i class="fas fa-info-circle"></i> Permissions inherited from the selected Role are marked with a <span class="badge badge-info">Role</span> badge.
                                Unchecking a role permission will deny it for this user. Checking a non-role permission will grant it directly.
                            </p>
                            <div class="row" id="permissions-container">
                                {{-- Permissions will be populated by JS based on role --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reporting Manager</label>
                            <select name="reporting_manager_id" id="reporting_manager_id" class="form-control">
                                <option value="">Select Manager</option>
                                {{-- Options will be populated by JS --}}
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Account Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="bank_branch_name" id="bank_branch_name" class="form-control" value="{{ old('bank_branch_name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control" value="{{ old('account_holder_name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Employee</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- Permissions Logic ---
            const allPermissions = @json($permissions);
            const roles = @json($roles->load('permissions'));

            function renderPermissions(roleName) {
                let container = $('#permissions-container');
                container.empty();

                let role = roles.find(r => r.name === roleName);
                let rolePermissions = role ? role.permissions.map(p => p.name) : [];

                // Group permissions by module
                let groupedPermissions = {};
                allPermissions.forEach(p => {
                    let module = p.module || 'Other';
                    if (!groupedPermissions[module]) {
                        groupedPermissions[module] = [];
                    }
                    groupedPermissions[module].push(p);
                });

                let sortedModules = Object.keys(groupedPermissions).sort();

                sortedModules.forEach(module => {
                    let perms = groupedPermissions[module];
                    let html = `
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="card permission-card h-100">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">${module}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">`;

                    perms.forEach(p => {
                        let isRolePermission = rolePermissions.includes(p.name);
                        let badge = isRolePermission ? '<span class="badge badge-info role-badge ml-1">Role</span>' : '';

                        html += `
                                    <div class="col-md-6 permission-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]"
                                                   class="custom-control-input"
                                                   id="perm_${p.id}"
                                                   value="${p.name}"
                                                   ${isRolePermission ? 'checked' : ''}>
                                            <label class="custom-control-label" for="perm_${p.id}">
                                                ${p.name} ${badge}
                                            </label>
                                        </div>
                                    </div>`;
                    });

                    html += `   </div>
                            </div>
                        </div>
                    </div>`;
                    container.append(html);
                });
            }

            // --- Reporting Manager Logic ---
            const admins = @json($admins);
            const teamLeads = @json($teamLeads);
            const oldManagerId = "{{ old('reporting_manager_id') }}";

            function populateManagers(role) {
                let managerSelect = $('#reporting_manager_id');
                managerSelect.empty().append('<option value="">Select Manager</option>');

                let managersToDisplay = [];

                if (role === 'Team Lead') {
                    managersToDisplay = admins;
                } else if (role) {
                    // For Tele Caller or any other role, show Team Leads
                    managersToDisplay = teamLeads;
                }

                $.each(managersToDisplay, function(index, manager) {
                    let selected = (oldManagerId == manager.id) ? 'selected' : '';
                    managerSelect.append('<option value="' + manager.id + '" ' + selected + '>' + manager.name + '</option>');
                });

                // Auto-select if only one option exists (excluding the default "Select Manager")
                if (managersToDisplay.length === 1) {
                    managerSelect.val(managersToDisplay[0].id);
                }
            }

            $('#role').change(function() {
                renderPermissions($(this).val());
                populateManagers($(this).val());
            });

            // Initial population on page load (e.g., if validation failed)
            let initialRole = $('#role').val();
            if (initialRole) {
                renderPermissions(initialRole);
                populateManagers(initialRole);
            }

            // --- Existing Scripts ---
            $('#createEmployeeForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                rules: {
                    role: { required: true },
                    name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
                    email: { required: true, email: true },
                    gender: { required: true },
                    password: { required: true, minlength: 8 },
                    password_confirmation: { required: true, equalTo: "#password" },
                    firm_id: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    address_proof: { required: true },
                    account_holder_name: {
                        required: function(element) {
                            return $("#ifsc_code").val().length > 0;
                        }
                    },
                    ifsc_code: {
                        required: function(element) {
                            return $("#account_holder_name").val().length > 0;
                        }
                    }
                },
                messages: {
                    role: "Please select an employee type",
                    name: "Please enter the employee's name",
                    contact_number: {
                        required: "Please enter a contact number",
                        number: "Please enter a valid number",
                        minlength: "Contact number must be 10 digits",
                        maxlength: "Contact number must be 10 digits"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    gender: "Please select a gender",
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 8 characters long"
                    },
                    password_confirmation: {
                        required: "Please confirm the password",
                        equalTo: "Passwords do not match"
                    },
                    firm_id: "Please select a firm",
                    current_address_line: "Please provide the current address",
                    permanent_address_line: "Please provide the permanent address",
                    address_proof: "Please upload an address proof",
                    account_holder_name: "Please enter the account holder's name",
                    ifsc_code: "Please enter the IFSC code"
                }
            });

            let eduIndex = 0;
            function addEducationRow() {
                let html = `
                    <div class="card education-card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Degree</label>
                                        <input type="text" name="education[${eduIndex}][degree]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Institution</label>
                                        <input type="text" name="education[${eduIndex}][institution]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Passing Year</label>
                                        <input type="text" name="education[${eduIndex}][passing_year]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Percentage</label>
                                        <input type="text" name="education[${eduIndex}][percentage]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-block remove-education">X</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#education-container').append(html);
                eduIndex++;
            }

            addEducationRow(); // Add one row by default

            $('#add-education').click(addEducationRow);

            $(document).on('click', '.remove-education', function() {
                $(this).closest('.education-card').remove();
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
                                // alert('Invalid Pincode'); // Suppress alert for better UX
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

                    // Explicitly populate cities for permanent address
                    populateCities('#permanent_state', '#permanent_city', '#permanent_pincode', $('#current_city').val());
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });
        });
    </script>
@stop
