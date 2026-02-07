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
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" id="editEmployeeForm">
                @csrf
                @method('PUT')

                <h5>Personal Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee Type <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
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
                            <label>DOB</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $employee->dob ? $employee->dob->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group', $employee->blood_group) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
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
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address <span class="text-danger">*</span></h6>
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
                            <h6>Permanent Address <span class="text-danger">*</span></h6>
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
                            <label>Address Proof (File)</label>
                            <input type="file" name="address_proof" class="form-control-file">
                            @if($employee->address_proof_path)
                                <small><a href="{{ asset('storage/' . $employee->address_proof_path) }}" target="_blank">View Current Proof</a></small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Proof ID Type</label>
                            <input type="text" name="proof_id_type" class="form-control" placeholder="e.g. Aadhar, PAN" value="{{ old('proof_id_type', $employee->proof_id_type) }}">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Education Details</h5>
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
                <button type="button" class="btn btn-success btn-sm" id="add-education">Add Education</button>

                <hr>
                <h5>Official Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assign Individual Permissions</label>
                            <select name="permissions[]" id="permissions" class="form-control select2" multiple="multiple">
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', $employee->permissions->pluck('name')->toArray())) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">These permissions will be assigned directly to the user in addition to their role's permissions.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-control">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('reporting_manager_id', $employee->reporting_manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
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
                            <select name="bank_id" id="bank_id" class="form-control">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $employee->bank_id) == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <select name="bank_branch_id" id="bank_branch_id" class="form-control">
                                <option value="">Select Branch</option>
                            </select>
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
                            <label>IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee->ifsc_code) }}">
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
            $('.select2').select2({
                placeholder: "Select Permissions",
                allowClear: true
            });

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

            // --- Education & Bank Logic ---
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

            function fetchBranches(bankId, selectedBranchId = null) {
                let branchSelect = $('#bank_branch_id');
                branchSelect.empty().append('<option value="">Select Branch</option>');

                if (bankId) {
                    $.ajax({
                        url: '{{ route("admin.get-branches", ["bank" => ":bankId"]) }}'.replace(':bankId', bankId),
                        type: 'GET',
                        success: function(data) {
                            $.each(data, function(key, branch) {
                                let selected = selectedBranchId == branch.id ? 'selected' : '';
                                branchSelect.append('<option value="' + branch.id + '" ' + selected + '>' + branch.name + '</option>');
                            });
                        }
                    });
                }
            }

            $('#bank_id').change(function() {
                fetchBranches($(this).val());
            });

            let initialBankId = $('#bank_id').val();
            if (initialBankId) {
                fetchBranches(initialBankId, {{ old('bank_branch_id', $employee->bank_branch_id) ?? 'null' }});
            }
        });
    </script>
@stop
