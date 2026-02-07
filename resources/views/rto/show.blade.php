@extends('adminlte::page')

@section('title', 'RTO Lead Details')

@section('content_header')
    <h1>RTO Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                            <p><strong>Father Name:</strong> {{ $lead->father_name }}</p>
                            <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                            <p><strong>Email:</strong> {{ $lead->email }}</p>
                            <p><strong>Firm:</strong> {{ $lead->firm ? $lead->firm->name : '-' }}</p>
                            <p><strong>Residence Type:</strong> {{ $lead->residence_type }}</p>
                            <p><strong>Aadhar Number:</strong> {{ $lead->aadhar_number }}</p>
                            @if($lead->profile_picture_path)
                                <p><strong>Profile Picture:</strong> <br> <img src="{{ asset('storage/' . $lead->profile_picture_path) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 100px;"></p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Current Address:</strong> {{ $lead->current_address_line }}, {{ $lead->currentCity->name ?? $lead->current_address_city }}, {{ $lead->currentState->name ?? $lead->current_address_state }} - {{ $lead->current_address_pincode }}</p>
                            @if($lead->current_address_proof_path)
                                <p><a href="{{ asset('storage/' . $lead->current_address_proof_path) }}" target="_blank" class="btn btn-sm btn-info">View Current Address Proof</a></p>
                            @endif
                            <p><strong>Permanent Address:</strong> {{ $lead->permanent_address_line }}, {{ $lead->permanentCity->name ?? $lead->permanent_address_city }}, {{ $lead->permanentState->name ?? $lead->permanent_address_state }} - {{ $lead->permanent_address_pincode }}</p>
                            @if($lead->permanent_address_proof_path)
                                <p><a href="{{ asset('storage/' . $lead->permanent_address_proof_path) }}" target="_blank" class="btn btn-sm btn-info">View Permanent Address Proof</a></p>
                            @endif
                        </div>
                    </div>

                    <hr>
                    <h5>Job Profile</h5>
                    <p><strong>Type:</strong> {{ $lead->job_profile_type }}</p>
                    @if($lead->job_profile_type == 'Self Employed')
                        <p><strong>Nature of Work:</strong> {{ $lead->nature_of_work }}</p>
                    @elseif($lead->job_profile_type == 'Salaried')
                        <p><strong>Type:</strong> {{ $lead->salaried_type }}</p>
                        @if($lead->salaried_type == 'Other')
                            <p><strong>Company:</strong> {{ $lead->other_company_name }}</p>
                        @endif
                        <p><strong>Designation:</strong> {{ $lead->designation }}</p>
                    @endif

                    <p><strong>Income Proof:</strong> {{ $lead->income_proof_available ? 'Yes' : 'No' }}</p>
                    @if($lead->income_proof_available)
                        <p><strong>ITR Amount:</strong> {{ $lead->itr_amount }}</p>
                        @if($lead->itr_document_path)
                            <p><a href="{{ asset('storage/' . $lead->itr_document_path) }}" target="_blank" class="btn btn-sm btn-info">View ITR Doc</a></p>
                        @endif
                    @else
                        <p><strong>Approx Income:</strong> {{ $lead->approx_income }}</p>
                    @endif

                    @if($lead->business_proof_available)
                        <p><strong>Business Proof:</strong> Yes</p>
                        @if($lead->business_proof_document_path)
                            <p><a href="{{ asset('storage/' . $lead->business_proof_document_path) }}" target="_blank" class="btn btn-sm btn-info">View Business Proof</a></p>
                        @endif
                    @endif

                    <hr>
                    <h5>Loan Details</h5>
                    <p><strong>Type:</strong> {{ $lead->loan_type }} {{ $lead->loan_type == 'Other' ? '('.$lead->other_loan_name.')' : '' }}</p>
                    @if($lead->loan_type == 'Vehicle Loan')
                        <p><strong>Vehicle Type:</strong> {{ $lead->vehicle_loan_type }} {{ $lead->vehicle_loan_type == 'Other' ? '('.$lead->other_vehicle_loan_name.')' : '' }}</p>
                        <p><strong>Purchase Type:</strong> {{ $lead->vehicle_purchase_type }}</p>

                        @if($lead->vehicle_purchase_type == 'New Purchase')
                            <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : '-' }}</p>
                            <p><strong>Vehicle Name:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : '-' }}</p>
                            <p><strong>Showroom Price:</strong> {{ $lead->showroom_price }}</p>
                            <p><strong>On Road Price:</strong> {{ $lead->on_road_price }}</p>
                        @else
                            <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
                            <p><strong>Engine Number:</strong> {{ $lead->engine_number }}</p>
                            <p><strong>Chassis Number:</strong> {{ $lead->chassis_number }}</p>
                            <p><strong>Mfg Year:</strong> {{ $lead->mfg_year }}</p>
                            <p><strong>Mfg Name:</strong> {{ $lead->mfg_name }}</p>
                            <p><strong>Model Name:</strong> {{ $lead->model_name }}</p>
                            <p><strong>Variant:</strong> {{ $lead->variant }}</p>
                            <p><strong>Owner Srl No:</strong> {{ $lead->owner_serial_number }}</p>
                        @endif
                    @endif

                    @if($lead->insurance_available)
                        <hr>
                        <h5>Insurance Details</h5>
                        <p><strong>Vendor:</strong> {{ $lead->insurance ? $lead->insurance->company_name : '-' }}</p>
                        <p><strong>Start Date:</strong> {{ $lead->insurance_start_date ? $lead->insurance_start_date->format('d-m-Y') : '-' }}</p>
                        <p><strong>End Date:</strong> {{ $lead->insurance_end_date ? $lead->insurance_end_date->format('d-m-Y') : '-' }}</p>
                        <p><strong>Amount:</strong> {{ $lead->insurance_amount }}</p>
                    @endif

                    @if($lead->hypothecation_status)
                        <hr>
                        <h5>Hypothecation Details</h5>
                        <p><strong>Old Financer:</strong> {{ $lead->old_financer }}</p>
                        <p><strong>Close Amount:</strong> {{ $lead->close_amount }}</p>
                        <p><strong>EMI Paid Count:</strong> {{ $lead->emi_paid_count }}</p>
                    @endif

                    <hr>
                    <h5>Amount Breakdown</h5>
                    <p><strong>Old Loan Close:</strong> {{ $lead->old_loan_close_amount }}</p>
                    <p><strong>Required In Hand:</strong> {{ $lead->required_in_hand_amount }}</p>
                    <p><strong>Total Loan Amount:</strong> {{ $lead->total_loan_amount }}</p>

                    @if($lead->has_co_applicant)
                        <hr>
                        <h5>Co-Applicant Details</h5>
                        <p><strong>Name:</strong> {{ $lead->co_name }}</p>
                        <p><strong>Relation:</strong> {{ $lead->co_relation }}</p>
                    @endif

                    @if($lead->has_guarantor)
                        <hr>
                        <h5>Guarantor Details</h5>
                        <p><strong>Name:</strong> {{ $lead->guarantor_name }}</p>
                        <p><strong>Relation:</strong> {{ $lead->guarantor_relation }}</p>
                    @endif

                    @if($lead->ref1_name || $lead->ref2_name)
                        <hr>
                        <h5>References</h5>
                        @if($lead->ref1_name)
                            <p><strong>Reference 1:</strong> {{ $lead->ref1_name }} ({{ $lead->ref1_mobile }})</p>
                        @endif
                        @if($lead->ref2_name)
                            <p><strong>Reference 2:</strong> {{ $lead->ref2_name }} ({{ $lead->ref2_mobile }})</p>
                        @endif
                    @endif

                    <hr>
                    <p><strong>Assigned To:</strong> {{ $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned' }}</p>
                    <p><strong>Created By:</strong> {{ $lead->createdBy ? $lead->createdBy->name : 'Unknown' }}</p>
                    <p><strong>Created At:</strong> {{ $lead->created_at->format('d-m-Y H:i') }}</p>
                    @if($lead->converted_at)
                        <p><strong>Converted At:</strong> {{ $lead->converted_at->format('d-m-Y H:i') }}</p>
                        <p><strong>Aging:</strong> {{ $lead->aging }}</p>
                    @endif
                </div>
            </div>

            {{-- RTO Specific Details --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RTO Process Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>RTO Stage:</strong> {{ $lead->rto_stage }}</p>
                    <p><strong>RTO Aging:</strong> {{ $lead->rto_aging }}</p>
                    @if($lead->rto_address_proof_path)
                        <p><a href="{{ asset('storage/' . $lead->rto_address_proof_path) }}" target="_blank" class="btn btn-sm btn-info">View RTO Address Proof</a></p>
                    @endif

                    @if($lead->rto_stage != 'New')
                        <hr>
                        <h5>Checking Details</h5>
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
                    @endif

                    @if($lead->rto_vehicle_type)
                        <hr>
                        <h5>Vehicle & RTO Details</h5>
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
                    @endif

                    @if($lead->loan_return_date)
                        <hr>
                        <h5>Loan Return Details</h5>
                        <p><strong>Date:</strong> {{ $lead->loan_return_date->format('d-m-Y') }}</p>
                        <p><strong>Reason:</strong> {{ $lead->loan_return_reason }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status History</h3>
                </div>
                <div class="card-body">
                    @if($lead->statusLogs->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($lead->statusLogs->sortByDesc('created_at') as $log)
                                <li class="mb-2">
                                    <strong>{{ $log->status == 'New' ? 'New Task' : str_replace('_', ' ', $log->status) }}</strong> : {{ $log->created_at->format('d-m-Y H:i') }}
                                    <br>
                                    <small class="text-muted">
                                        By: {{ $log->user ? $log->user->name : 'System' }}
                                        @if($log->remark)
                                            <br>Remark: {{ $log->remark }}
                                        @endif
                                    </small>
                                </li>
                                <hr>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No history available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
