@extends('adminlte::page')

@section('title', 'Create Employee')

@section('content_header')
    <h1>Create Employee</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h5>Personal Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Employee Type</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
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
                            <label>Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                            @error('contact_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Joined Firm</label>
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
                        <h6>Current Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="current_address_line" class="form-control" value="{{ old('current_address_line') }}">
                        </div>
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="current_address_pincode" class="form-control" value="{{ old('current_address_pincode') }}">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="current_address_city" class="form-control" value="{{ old('current_address_city') }}">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="current_address_state" class="form-control" value="{{ old('current_address_state') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address</h6>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" name="permanent_address_line" class="form-control" value="{{ old('permanent_address_line') }}">
                        </div>
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="permanent_address_pincode" class="form-control" value="{{ old('permanent_address_pincode') }}">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="permanent_address_city" class="form-control" value="{{ old('permanent_address_city') }}">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="permanent_address_state" class="form-control" value="{{ old('permanent_address_state') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof (File)</label>
                            <input type="file" name="address_proof" class="form-control-file @error('address_proof') is-invalid @enderror">
                            @error('address_proof') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proof ID Type</label>
                            <input type="text" name="proof_id_type" class="form-control" placeholder="e.g. Aadhar, PAN" value="{{ old('proof_id_type') }}">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Education Details</h5>
                <div id="education-container">
                    @if(old('education'))
                        @foreach(old('education') as $index => $edu)
                            <div class="card education-card mt-2">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Degree</label>
                                                <input type="text" name="education[{{ $index }}][degree]" class="form-control" value="{{ $edu['degree'] }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Institution</label>
                                                <input type="text" name="education[{{ $index }}][institution]" class="form-control" value="{{ $edu['institution'] }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Passing Year</label>
                                                <input type="text" name="education[{{ $index }}][passing_year]" class="form-control" value="{{ $edu['passing_year'] }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Percentage</label>
                                                <input type="text" name="education[{{ $index }}][percentage]" class="form-control" value="{{ $edu['percentage'] }}">
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
                    @else
                        <div class="card education-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Degree</label>
                                            <input type="text" name="education[0][degree]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Institution</label>
                                            <input type="text" name="education[0][institution]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Passing Year</label>
                                            <input type="text" name="education[0][passing_year]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Percentage</label>
                                            <input type="text" name="education[0][percentage]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-education">X</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-education">Add Education</button>

                <hr>
                <h5>Official Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-control">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('reporting_manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
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
                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
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
                            <input type="text" name="account_holder_name" class="form-control" value="{{ old('account_holder_name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Create Employee</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let eduIndex = {{ count(old('education', [1])) - 1 }};
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

            // On page load, if a bank is already selected from old input, fetch its branches
            let initialBankId = $('#bank_id').val();
            if (initialBankId) {
                fetchBranches(initialBankId, {{ old('bank_branch_id') ?? 'null' }});
            }
        });
    </script>
@stop
