@extends('adminlte::page')

@section('title', 'Edit Employee')

@section('content_header')
    <h1>Edit Employee</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h5>Personal Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee Type</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $employee->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $employee->contact_number) }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DOB</label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $employee->dob ? $employee->dob->format('Y-m-d') : '') }}">
                            @error('dob') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-control @error('blood_group') is-invalid @enderror" value="{{ old('blood_group', $employee->blood_group) }}">
                            @error('blood_group') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Joined Firm</label>
                            <select name="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $employee->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
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
                        <h6>Current Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" class="form-control @error('current_address_line') is-invalid @enderror" value="{{ old('current_address_line', $employee->current_address_line) }}">
                            @error('current_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="current_address_pincode" class="form-control @error('current_address_pincode') is-invalid @enderror" value="{{ old('current_address_pincode', $employee->current_address_pincode) }}">
                            @error('current_address_pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="current_address_city" class="form-control @error('current_address_city') is-invalid @enderror" value="{{ old('current_address_city', $employee->current_address_city) }}">
                            @error('current_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="current_address_state" class="form-control @error('current_address_state') is-invalid @enderror" value="{{ old('current_address_state', $employee->current_address_state) }}">
                            @error('current_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" class="form-control @error('permanent_address_line') is-invalid @enderror" value="{{ old('permanent_address_line', $employee->permanent_address_line) }}">
                            @error('permanent_address_line') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="permanent_address_pincode" class="form-control @error('permanent_address_pincode') is-invalid @enderror" value="{{ old('permanent_address_pincode', $employee->permanent_address_pincode) }}">
                            @error('permanent_address_pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="permanent_address_city" class="form-control @error('permanent_address_city') is-invalid @enderror" value="{{ old('permanent_address_city', $employee->permanent_address_city) }}">
                            @error('permanent_address_city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="permanent_address_state" class="form-control @error('permanent_address_state') is-invalid @enderror" value="{{ old('permanent_address_state', $employee->permanent_address_state) }}">
                            @error('permanent_address_state') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof (File)</label>
                            <input type="file" name="address_proof" class="form-control-file @error('address_proof') is-invalid @enderror">
                            @error('address_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            @if($employee->address_proof_path)
                                <small><a href="{{ asset('storage/' . $employee->address_proof_path) }}" target="_blank">View Current Proof</a></small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proof ID Type</label>
                            <input type="text" name="proof_id_type" class="form-control @error('proof_id_type') is-invalid @enderror" value="{{ old('proof_id_type', $employee->proof_id_type) }}">
                            @error('proof_id_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Education Details</h5>
                <div id="education-container">
                    @php
                        $educations = old('education', $employee->education->toArray());
                        if (empty($educations)) {
                            $educations = [['degree' => '', 'institution' => '', 'passing_year' => '', 'percentage' => '']];
                        }
                    @endphp
                    @foreach($educations as $index => $edu)
                        <div class="card education-card mt-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Degree</label>
                                            <input type="text" name="education[{{ $index }}][degree]" class="form-control @error('education.'.$index.'.degree') is-invalid @enderror" value="{{ $edu['degree'] }}" required>
                                            @error('education.'.$index.'.degree') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Institution</label>
                                            <input type="text" name="education[{{ $index }}][institution]" class="form-control @error('education.'.$index.'.institution') is-invalid @enderror" value="{{ $edu['institution'] }}" required>
                                            @error('education.'.$index.'.institution') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Passing Year</label>
                                            <input type="text" name="education[{{ $index }}][passing_year]" class="form-control @error('education.'.$index.'.passing_year') is-invalid @enderror" value="{{ $edu['passing_year'] }}" required>
                                            @error('education.'.$index.'.passing_year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Percentage</label>
                                            <input type="text" name="education[{{ $index }}][percentage]" class="form-control @error('education.'.$index.'.percentage') is-invalid @enderror" value="{{ $edu['percentage'] }}">
                                            @error('education.'.$index.'.percentage') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-education">X</button>
                                    </div>
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
                            <label>Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-control @error('reporting_manager_id') is-invalid @enderror">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('reporting_manager_id', $employee->reporting_manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                            @error('reporting_manager_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Account Details</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <select name="bank_id" id="bank_id" class="form-control @error('bank_id') is-invalid @enderror">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $employee->bank_id) == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                @endforeach
                            </select>
                            @error('bank_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <select name="bank_branch_id" id="bank_branch_id" class="form-control @error('bank_branch_id') is-invalid @enderror">
                                <option value="">Select Branch</option>
                                {{-- Populate via JS based on bank selection if needed, or load all --}}
                            </select>
                            @error('bank_branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <input type="text" name="account_holder_name" class="form-control @error('account_holder_name') is-invalid @enderror" value="{{ old('account_holder_name', $employee->account_holder_name) }}">
                            @error('account_holder_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror" value="{{ old('ifsc_code', $employee->ifsc_code) }}">
                            @error('ifsc_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Employee</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let eduIndex = {{ count(old('education', $employee->education->toArray())) - 1 }};
            $('#add-education').click(function() {
                eduIndex++;
                let html = `
                    <div class="card education-card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Degree</label>
                                        <input type="text" name="education[${eduIndex}][degree]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Institution</label>
                                        <input type="text" name="education[${eduIndex}][institution]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Passing Year</label>
                                        <input type="text" name="education[${eduIndex}][passing_year]" class="form-control" required>
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
            });

            $(document).on('click', '.remove-education', function() {
                $(this).closest('.education-card').remove();
            });

            function fetchBranches(bankId, selectedBranchId = null) {
                let branchSelect = $('#bank_branch_id');
                branchSelect.empty().append('<option value="">Select Branch</option>');

                if (bankId) {
                    $.ajax({
                        url: '{{ route("get-branches", ["bank" => ":bankId"]) }}'.replace(':bankId', bankId),
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

            // On page load, if a bank is already selected from old input or db, fetch its branches
            let initialBankId = $('#bank_id').val();
            if (initialBankId) {
                fetchBranches(initialBankId, {{ old('bank_branch_id', $employee->bank_branch_id) ?? 'null' }});
            }
        });
    </script>
@stop
