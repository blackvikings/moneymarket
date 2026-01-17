@extends('adminlte::page')

@section('title', 'RTO Lead Details')

@section('content_header')
    <h1>RTO Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    <p><strong>RTO Aging:</strong> {{ $lead->rto_aging }}</p>
                    @if($lead->rto_address_proof_path)
                        <p><a href="{{ asset('storage/' . $lead->rto_address_proof_path) }}" target="_blank">View Address Proof</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Checking Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>HSRP:</strong> {{ $lead->rto_hsrp_status ? 'Yes' : 'No' }}</p>
                            <p><strong>Hypothecation:</strong> {{ $lead->rto_hypothecation_status ? 'Yes' : 'No' }}</p>
                            @if($lead->rto_hypothecation_status)
                                <p><strong>Bank:</strong> {{ $lead->bank ? $lead->bank->name : '-' }}</p>
                            @endif
                            <p><strong>Insurance:</strong> {{ $lead->rto_insurance_status ? 'Yes' : 'No' }}</p>
                            <p><strong>Fitness:</strong> {{ $lead->rto_fitness_status ? 'Yes' : 'No' }}</p>
                            <p><strong>Tax:</strong> {{ $lead->rto_tax_status ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>PUC:</strong> {{ $lead->rto_puc_status ? 'Yes' : 'No' }}</p>
                            <p><strong>E-challan:</strong> {{ $lead->rto_echallan_status ? 'Yes' : 'No' }}</p>
                            <p><strong>NCR:</strong> {{ $lead->rto_ncr_status ? 'Yes' : 'No' }}</p>
                            <p><strong>RTO Challan:</strong> {{ $lead->rto_challan_status ? 'Yes' : 'No' }}</p>
                            <p><strong>Permit:</strong> {{ $lead->rto_permit_status ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Return Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $lead->loan_return_date ? $lead->loan_return_date->format('d-m-Y') : '-' }}</p>
                    <p><strong>Reason:</strong> {{ $lead->loan_return_reason }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle & RTO Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Vehicle Type:</strong> {{ $lead->rto_vehicle_type }}</p>
                    @if($lead->rto_vehicle_type == 'Old Vehicle')
                        <p><strong>Old Financer:</strong> {{ $lead->old_financer }}</p>
                        <p><strong>Loan Closed:</strong> {{ $lead->rto_loan_closed_status ? 'Yes' : 'No' }}</p>
                        <p><strong>HP Termination:</strong> {{ $lead->rto_hp_termination_status ? 'Yes' : 'No' }}</p>
                        <h6>Closer Deposits:</h6>
                        <ul>
                            @forelse($lead->rtoCloserDeposits as $deposit)
                                <li>{{ $deposit->amount }} on {{ $deposit->date->format('d-m-Y') }}</li>
                            @empty
                                <li>No deposits found.</li>
                            @endforelse
                        </ul>
                    @endif
                    <p><strong>RTO Agent:</strong> {{ $lead->rtoAgent ? $lead->rtoAgent->name : '-' }}</p>
                    <h6>Challans:</h6>
                    <ul>
                        @forelse($lead->rtoChallans as $challan)
                            <li>{{ $challan->challan_type }} - {{ $challan->amount }} on {{ $challan->date->format('d-m-Y') }}</li>
                        @empty
                            <li>No challans found.</li>
                        @endforelse
                    </ul>
                    <p><strong>Challan Verified:</strong> {{ $lead->rto_challan_verified ? 'Yes' : 'No' }}</p>
                    <p><strong>Verification:</strong> {{ $lead->rto_verification_status ? 'Yes' : 'No' }}</p>
                    <p><strong>Approval:</strong> {{ $lead->rto_approval_status ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
