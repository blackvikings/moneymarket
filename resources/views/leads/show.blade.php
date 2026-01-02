@extends('adminlte::page')

@section('title', 'Lead Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lead Details: {{ $lead->customer_name }}</h1>
        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary">Edit Lead</a>
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
                        </div>
                        <div class="col-md-6">
                            <p><strong>Current Address:</strong> {{ $lead->current_address_line }}, {{ $lead->current_address_city }}, {{ $lead->current_address_state }} - {{ $lead->current_address_pincode }}</p>
                            <p><strong>Permanent Address:</strong> {{ $lead->permanent_address_line }}, {{ $lead->permanent_address_city }}, {{ $lead->permanent_address_state }} - {{ $lead->permanent_address_pincode }}</p>
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
                            <p><strong>Variant:</strong> {{ $lead->vehicle_variant }}</p>
                            <p><strong>Showroom Price:</strong> {{ $lead->showroom_price }}</p>
                            <p><strong>On Road Price:</strong> {{ $lead->on_road_price }}</p>
                        @else
                            <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
                            <p><strong>Model:</strong> {{ $lead->model_name }}</p>
                        @endif
                    @endif

                    <hr>
                    <h5>Amount Breakdown</h5>
                    <p><strong>Old Loan Close:</strong> {{ $lead->old_loan_close_amount }}</p>
                    <p><strong>Required In Hand:</strong> {{ $lead->required_in_hand_amount }}</p>
                    <p><strong>Total Loan Amount:</strong> {{ $lead->total_loan_amount }}</p>

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
