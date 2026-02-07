<div class="modal-header">
    <h5 class="modal-title">Employee Details: {{ $employee->name }} ({{ $employee->employee_id }})</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Personal Details</strong></h6>
            <p><strong>Name:</strong> {{ $employee->name }}</p>
            <p><strong>Email:</strong> {{ $employee->email }}</p>
            <p><strong>Contact:</strong> {{ $employee->contact_number }}</p>
            <p><strong>DOB:</strong> {{ $employee->dob ? $employee->dob->format('d-m-Y') : '-' }}</p>
            <p><strong>Blood Group:</strong> {{ $employee->blood_group ?? '-' }}</p>
            <p><strong>Status:</strong> <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">{{ $employee->status }}</span></p>
        </div>
        <div class="col-md-6">
            <h6><strong>Official Details</strong></h6>
            <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
            <p><strong>Type:</strong> {{ $employee->employee_type }}</p>
            <p><strong>Joined Firm:</strong> {{ $employee->firm ? $employee->firm->name : '-' }}</p>
            <p><strong>Reporting Manager:</strong> {{ $employee->reportingManager ? $employee->reportingManager->name : '-' }}</p>
            <p><strong>Proof ID Type:</strong> {{ $employee->proof_id_type ?? '-' }}</p>
            @if($employee->address_proof_path)
                <p><a href="{{ asset('storage/' . $employee->address_proof_path) }}" target="_blank" class="btn btn-xs btn-info">View Address Proof</a></p>
            @endif
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Current Address</strong></h6>
            <p>{{ $employee->current_address_line }}</p>
        </div>
        <div class="col-md-6">
            <h6><strong>Permanent Address</strong></h6>
            <p>{{ $employee->permanent_address_line }}</p>
        </div>
    </div>

    @if($employee->education->count() > 0)
        <hr>
        <h6><strong>Education Details</strong></h6>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Passing Year</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->education as $edu)
                    <tr>
                        <td>{{ $edu->degree }}</td>
                        <td>{{ $edu->institution }}</td>
                        <td>{{ $edu->passing_year }}</td>
                        <td>{{ $edu->percentage ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <hr>
    <h6><strong>Account Details</strong></h6>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Bank:</strong> {{ $employee->bank ? $employee->bank->name : '-' }}</p>
            <p><strong>Branch:</strong> {{ $employee->bankBranch ? $employee->bankBranch->name : '-' }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>A/C Holder:</strong> {{ $employee->account_holder_name ?? '-' }}</p>
            <p><strong>IFSC Code:</strong> {{ $employee->ifsc_code ?? '-' }}</p>
        </div>
    </div>

    @if($employee->permissions->count() > 0)
        <hr>
        <h6><strong>Direct Permissions</strong></h6>
        @foreach($employee->permissions as $permission)
            <span class="badge badge-secondary">{{ $permission->name }}</span>
        @endforeach
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary">Edit Employee</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
