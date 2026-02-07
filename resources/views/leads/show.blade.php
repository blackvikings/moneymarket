@extends('adminlte::page')

@section('title', 'Lead Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lead Details: {{ $lead->customer_name }}</h1>
        <a href="{{ route(get_route_prefix() . 'leads.edit', $lead->id) }}" class="btn btn-primary">Edit Lead</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
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

                    @if($lead->lead_type == 'Loan')
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
                    @endif

                    @if($lead->lead_type == 'RTO')
                        <hr>
                        <h5>RTO Details</h5>
                        <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
                        <p><strong>Engine Number:</strong> {{ $lead->engine_number }}</p>
                        <p><strong>Chassis Number:</strong> {{ $lead->chassis_number }}</p>
                        <p><strong>Mfg Year:</strong> {{ $lead->mfg_year }}</p>
                        <p><strong>Mfg Name:</strong> {{ $lead->mfg_name }}</p>
                        <p><strong>Model Name:</strong> {{ $lead->model_name }}</p>
                        <p><strong>Variant:</strong> {{ $lead->variant }}</p>
                        <p><strong>Owner Srl No:</strong> {{ $lead->owner_serial_number }}</p>
                        <p><strong>Insurance Available:</strong> {{ $lead->insurance_available ? 'Yes' : 'No' }}</p>
                        <p><strong>Hypothecation:</strong> {{ $lead->hypothecation_status ? 'Yes' : 'No' }}</p>
                    @endif

                    @if($lead->lead_type == 'Insurance')
                        <hr>
                        <h5>Insurance Details</h5>
                        <p><strong>Model Name:</strong> {{ $lead->model_name }}</p>
                        <p><strong>Variant:</strong> {{ $lead->variant }}</p>
                        <p><strong>Owner Srl No:</strong> {{ $lead->owner_serial_number }}</p>
                        <p><strong>Hypothecation:</strong> {{ $lead->hypothecation_status ? 'Yes' : 'No' }}</p>
                    @endif

                    @if($lead->insurance_available)
                        <hr>
                        <h5>Insurance Policy Details</h5>
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
        </div>
        <div class="col-md-4">
            @if($lead->follow_up_date)
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Next Follow-up</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Date:</strong> {{ $lead->follow_up_date->format('d-m-Y') }}</p>
                        <p><strong>Time:</strong> {{ $lead->follow_up_time ?? 'Not specified' }}</p>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status History</h3>
                </div>
                <div class="card-body">
                    @if($lead->statusLogs->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($lead->statusLogs->sortByDesc('created_at') as $log)
                                <li class="mb-2">
                                    <strong>{{ $log->status == 'New' ? 'New Task' : ($log->status == 'Prospect' ? 'Follow up' : str_replace('_', ' ', $log->status)) }}</strong> : {{ $log->created_at->format('d-m-Y H:i') }}
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

            @if($lead->bank_id)
                <div class="card mt-3">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Bank Assign Journey</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Bank:</strong> {{ $lead->bank->name }}</p>
                        <p><strong>Branch:</strong> {{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</p>
                        <hr>
                        <ul class="timeline">
                            @php
                                $crmStages = [
                                    'New' => 'crm_new_at',
                                    'Login' => 'crm_login_at',
                                    'FI Document' => 'crm_fi_document_at',
                                    'Vehicle Evaluation' => 'crm_vehicle_evaluation_at',
                                    'Credit' => 'crm_credit_at',
                                    'Approved' => 'crm_approved_at',
                                    'Agreement' => 'crm_agreement_at',
                                    'Disbursal' => 'crm_disbursal_at'
                                ];
                            @endphp
                            @foreach($crmStages as $stage => $column)
                                @if($lead->$column)
                                    <li>
                                        <i class="fas fa-check bg-green"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $lead->$column->format('d-m-Y H:i') }}</span>
                                            <h3 class="timeline-header no-border">{{ $stage }}</h3>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
