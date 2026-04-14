@extends('adminlte::page')

@section('title', 'Edit Employee')

@section('content_header')
    <h1>Edit Employee</h1>
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
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" id="editEmployeeForm">
                @csrf
                @method('PUT')

                <h5 class="text-primary font-weight-bold mb-3">Personal Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee Type <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $employee->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $employee->contact_number) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $employee->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DOB</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $employee->dob ? $employee->dob->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group', $employee->blood_group) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Joined Firm <span class="text-danger">*</span></label>
                            <select name="firm_id" class="form-control" required>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $employee->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="text-primary font-weight-bold mb-3">Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Current Address <span class="text-danger">*</span></h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" id="current_address_line" class="form-control" value="{{ old('current_address_line', $employee->current_address_line) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="current_address_pincode" id="current_pincode" class="form-control pincode-input" value="{{ old('current_address_pincode', $employee->current_address_pincode) }}">
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
                            <h6 class="font-weight-bold">Permanent Address <span class="text-danger">*</span></h6>
                            <div class="form-group mb-0">
                                <input type="checkbox" id="same_as_current"> <label for="same_as_current" class="mb-0 ml-1">Same as Current Address</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" id="permanent_address_line" class="form-control" value="{{ old('permanent_address_line', $employee->permanent_address_line) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="permanent_address_pincode" id="permanent_pincode" class="form-control pincode-input" value="{{ old('permanent_address_pincode', $employee->permanent_address_pincode) }}">
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
                                <option value="Aadhar" {{ old('proof_id_type', $employee->proof_id_type) == 'Aadhar' ? 'selected' : '' }}>Aadhar</option>
                                <option value="Voter Id" {{ old('proof_id_type', $employee->proof_id_type) == 'Voter Id' ? 'selected' : '' }}>Voter Id</option>
                                <option value="Driving Licence" {{ old('proof_id_type', $employee->proof_id_type) == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                <option value="Pancard" {{ old('proof_id_type', $employee->proof_id_type) == 'Pancard' ? 'selected' : '' }}>Pancard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof (File)</label>
                            <input type="file" name="address_proof" class="form-control-file">
                            @if($employee->address_proof_path)
                                <small><a href="{{ asset('storage/' . $employee->address_proof_path) }}" target="_blank">View Current Proof</a></small>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="text-primary font-weight-bold mb-3">Education Details</h5>
                <div id="education-container">
                    @foreach($employee->education as $index => $edu)
                        <div class="card education-card mt-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3"><div class="form-group"><label>Degree</label><input type="text" name="education[{{$index}}][degree]" class="form-control" value="{{ $edu->degree }}" required></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Institution</label><input type="text" name="education[{{$index}}][institution]" class="form-control" value="{{ $edu->institution }}" required></div></div>
                                    <div class="col-md-3"><div class="form-group"><label>Passing Year</label><input type="text" name="education[{{$index}}][passing_year]" class="form-control" value="{{ $edu->passing_year }}" required></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Percentage</label><input type="text" name="education[{{$index}}][percentage]" class="form-control" value="{{ $edu->percentage }}"></div></div>
                                    <div class="col-md-1"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-education">X</button></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-education"><i class="fas fa-plus"></i> Add Education</button>

                <hr>
                <h5 class="text-primary font-weight-bold mb-3">Official Details</h5>
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
                <h5 class="text-primary font-weight-bold mb-3">Account Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="bank_branch_name" id="bank_branch_name" class="form-control" value="{{ old('bank_branch_name', $employee->bank_branch_name) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control" value="{{ old('account_holder_name', $employee->account_holder_name) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number', $employee->account_number) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee->ifsc_code) }}">
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5"><i class="fas fa-save mr-2"></i> Save Employee</button>
                </div>
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
            const employeeDirectPermissions = @json($employee->permissions->pluck('name'));
            const employeeDeniedPermissions = @json($employee->deniedPermissions->pluck('name'));
            const employeeId = "{{ $employee->id }}";

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

                // Sort modules
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
                        let isDirectPermission = employeeDirectPermissions.includes(p.name);
                        let isDenied = employeeDeniedPermissions.includes(p.name);

                        // Determine checked state:
                        // Checked if (In Role AND Not Denied) OR (Direct Permission)
                        // Note: If role changes, we reset to role defaults (all role perms checked)
                        // But if it's the initial load (same role), we respect saved state.

                        let isChecked = false;
                        if (roleName === "{{ $employee->roles->first()->name ?? '' }}") {
                             isChecked = (isRolePermission && !isDenied) || isDirectPermission;
                        } else {
                             // If role changed, default to role permissions
                             isChecked = isRolePermission;
                        }

                        // Visual indicator for role permissions
                        let badge = isRolePermission ? '<span class="badge badge-info role-badge ml-1">Role</span>' : '';

                        html += `
                                    <div class="col-md-6 permission-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]"
                                                   class="custom-control-input"
                                                   id="perm_${p.id}"
                                                   value="${p.name}"
                                                   ${isChecked ? 'checked' : ''}>
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

            // Initial render
            let initialRole = $('#role').val();
            if (initialRole) {
                renderPermissions(initialRole);
            }

            // Re-render on role change
            $('#role').change(function() {
                renderPermissions($(this).val());
                populateManagers($(this).val());
            });


            // --- Reporting Manager Logic ---
            const oldManagerId = "{{ old('reporting_manager_id', $employee->reporting_manager_id) }}";

            function populateManagers(role) {
                let managerSelect = $('#reporting_manager_id');
                managerSelect.empty().append('<option value="">Loading...</option>');

                if (role) {
                    $.ajax({
                        url: '{{ url("admin/get-managers-by-role") }}/' + role + '?exclude_id=' + employeeId,
                        type: 'GET',
                        success: function(data) {
                            managerSelect.empty().append('<option value="">Select Manager</option>');
                            $.each(data, function(index, manager) {
                                let selected = (oldManagerId == manager.id) ? 'selected' : '';
                                managerSelect.append('<option value="' + manager.id + '" ' + selected + '>' + manager.name + '</option>');
                            });

                            if (data.length === 1) {
                                managerSelect.val(data[0].id);
                            }
                        },
                        error: function() {
                            managerSelect.empty().append('<option value="">Error loading managers</option>');
                        }
                    });
                } else {
                    managerSelect.empty().append('<option value="">Select Manager</option>');
                }
            }

            if (initialRole) {
                populateManagers(initialRole);
            }

            // --- Existing Scripts ---
            $('#editEmployeeForm').validate({
                // Rules similar to create, but password not required
                rules: {
                    role: { required: true },
                    name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
                    email: { required: true, email: true },
                    firm_id: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    // address_proof not required on edit
                }
            });

            // --- Location Scripts ---
            function populateStates(selector) {
                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        $(selector).each(function() {
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
                            // Trigger change to load cities if a state is pre-selected
                            if(preselectedValue) {
                                currentDropdown.trigger('change');
                            }
                        });
                    }
                });
            }

            function populateCities(stateElement, cityElement, pincodeElement, selectedCity = null) {
                let stateId = $(stateElement).val();
                $(cityElement).empty().append('<option value="">Loading...</option>');
                if(pincodeElement && !selectedCity) $(pincodeElement).val('');

                if (stateId) {
                    $.ajax({
                        url: '{{ route("get-cities", ["state" => ":stateId"]) }}'.replace(':stateId', stateId),
                        type: 'GET',
                        success: function(data) {
                            $(cityElement).empty().append('<option value="">Select City</option>');
                            let preselectedCity = selectedCity || $(cityElement).data('selected');

                            $.each(data, function(key, city) {
                                let option = $('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode);
                                if (city.id == preselectedCity) {
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
            $('#current_state').data('selected', '{{ old('current_address_state', $employee->current_address_state) }}');
            $('#current_city').data('selected', '{{ old('current_address_city', $employee->current_address_city) }}');
            $('#permanent_state').data('selected', '{{ old('permanent_address_state', $employee->permanent_address_state) }}');
            $('#permanent_city').data('selected', '{{ old('permanent_address_city', $employee->permanent_address_city) }}');

            // Initial population
            populateStates('.state-select');

            // Event Handlers
            $('.state-select').change(function() {
                let citySelect = $(this).closest('.row').find('.city-select');
                let pincodeInput = $(this).closest('.row').find('.pincode-input');
                // Pass null for selectedCity to rely on data-selected attribute or user selection
                populateCities(this, citySelect, pincodeInput);
            });

            $('.city-select').change(function() {
                let pincode = $(this).find(':selected').data('pincode');
                let pincodeInput = $(this).closest('.row').find('.pincode-input');
                if(pincode) pincodeInput.val(pincode);
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
                                // Pass the city ID explicitly to select it after loading cities
                                populateCities(stateSelect, citySelect, null, data.city.id);
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
                    // Trigger change on state to load cities, then select the correct city
                    populateCities($('#permanent_state'), $('#permanent_city'), $('#permanent_pincode'), currentCityVal);
                } else {
                    $('#permanent_address_line, #permanent_pincode, #permanent_state, #permanent_city').val('');
                    $('#permanent_city').empty().append('<option value="">Select State First</option>');
                }
            });

            // --- Education Logic ---
            let eduIndex = {{ $employee->education->count() }};
            $('#add-education').click(function() {
                let html = `
                    <div class="card education-card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3"><div class="form-group"><label>Degree</label><input type="text" name="education[${eduIndex}][degree]" class="form-control" required></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Institution</label><input type="text" name="education[${eduIndex}][institution]" class="form-control" required></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Passing Year</label><input type="text" name="education[${eduIndex}][passing_year]" class="form-control" required></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Percentage</label><input type="text" name="education[${eduIndex}][percentage]" class="form-control"></div></div>
                                <div class="col-md-1"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-education">X</button></div>
                            </div>
                        </div>
                    </div>
                `;
                $('#education-container').append(html);
                eduIndex++;
            });

            $(document).on('click', '.remove-education', function() {
                $(this).closest('.education-card').remove();
            });
        });
    </script>
@stop
