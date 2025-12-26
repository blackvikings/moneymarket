@extends('adminlte::page')

@section('title', 'View Employee')

@section('content_header')
    <h1>View Employee</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Personal Details</h5>
            <div class="row">
                <div class="col-md-4"><strong>Employee Type:</strong> {{ $employee->employee_type }}</div>
                <div class="col-md-4"><strong>Name:</strong> {{ $employee->name }}</div>
                <div class="col-md-4"><strong>Contact Number:</strong> {{ $employee->contact_number }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Email:</strong> {{ $employee->email }}</div>
                <div class="col-md-4"><strong>DOB:</strong> {{ $employee->dob ? $employee->dob->format('d-m-Y') : 'N/A' }}</div>
                <div class="col-md-4"><strong>Blood Group:</strong> {{ $employee->blood_group }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Joined Firm:</strong> {{ $employee->firm ? $employee->firm->name : 'N/A' }}</div>
                <div class="col-md-4"><strong>Status:</strong> {{ $employee->status }}</div>
            </div>

            <hr>
            <h5>Address Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <h6>Current Address</h6>
                    <p>
                        {{ $employee->current_address_line }}<br>
                        {{ $employee->current_address_city }}, {{ $employee->current_address_state }} - {{ $employee->current_address_pincode }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Permanent Address</h6>
                    <p>
                        {{ $employee->permanent_address_line }}<br>
                        {{ $employee->permanent_address_city }}, {{ $employee->permanent_address_state }} - {{ $employee->permanent_address_pincode }}
                    </p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Proof ID Type:</strong> {{ $employee->proof_id_type }}
                </div>
                <div class="col-md-6">
                    <strong>Address Proof:</strong>
                    @if($employee->address_proof_path)
                        <a href="{{ asset('storage/' . $employee->address_proof_path) }}" target="_blank">View Proof</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>

            <hr>
            <h5>Education Details</h5>
            <table class="table table-bordered">
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
                            <td>{{ $edu->percentage }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>
            <h5>Official Details</h5>
            <div class="row">
                <div class="col-md-6"><strong>Reporting Manager:</strong> {{ $employee->reportingManager ? $employee->reportingManager->name : 'N/A' }}</div>
            </div>

            <hr>
            <h5>Account Details</h5>
            <div class="row">
                <div class="col-md-3"><strong>Bank Name:</strong> {{ $employee->bank ? $employee->bank->name : 'N/A' }}</div>
                <div class="col-md-3"><strong>Branch Name:</strong> {{ $employee->bankBranch ? $employee->bankBranch->name : 'N/A' }}</div>
                <div class="col-md-3"><strong>Account Holder:</strong> {{ $employee->account_holder_name }}</div>
                <div class="col-md-3"><strong>IFSC Code:</strong> {{ $employee->ifsc_code }}</div>
            </div>

            <hr>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
@stop
