@extends('adminlte::page')

@section('title', 'CRM Lead Details')

@section('content_header')
    @php
        $backUrl = url()->previous();
        if (!$backUrl || $backUrl === url()->current()) {
            $backUrl = route(get_route_prefix() . 'crm.index');
        }
    @endphp
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">CRM Lead: {{ $lead->customer_name }}</h1>
        <a href="{{ $backUrl }}" class="btn btn-secondary">Back</a>
    </div>
@stop

@section('content')
    <style>
        /* Custom Tab Design */
        .crm-tabs-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
            overflow-x: auto;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 1rem;
        }
        .crm-tab-container {
            position: relative;
            flex: 1 1 0%;
            min-width: 120px;
            margin-left: -1rem;
            z-index: 1;
        }
        .crm-tab-container:first-child {
            margin-left: 0;
            z-index: 0;
        }
        /* Stacking context */
        .crm-tab-container:nth-child(1) { z-index: 10; }
        .crm-tab-container:nth-child(2) { z-index: 9; }
        .crm-tab-container:nth-child(3) { z-index: 8; }
        .crm-tab-container:nth-child(4) { z-index: 7; }
        .crm-tab-container:nth-child(5) { z-index: 6; }
        .crm-tab-container:nth-child(6) { z-index: 5; }
        .crm-tab-container:nth-child(7) { z-index: 4; }
        .crm-tab-container:nth-child(8) { z-index: 3; }
        .crm-tab-container:nth-child(9) { z-index: 2; }
        .crm-tab-container:nth-child(10) { z-index: 1; }

        .crm-tab-content {
            height: 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-right: 1rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            text-decoration: none !important;
            color: #4b5563;
            background-color: white;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }

        .crm-tab-content:hover {
            background-color: #eff6ff;
        }

        .crm-tab-content.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .crm-tab-content.active .crm-tab-subtitle,
        .crm-tab-content.active .crm-tab-title {
            color: white;
        }

        .crm-tab-content.active .crm-tab-subtitle {
            opacity: 0.8;
        }

        .crm-tab-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1e3a8a;
        }

        .crm-tab-subtitle {
            font-size: 1.25rem;
            font-weight: 800;
            color: #9ca3af;
        }

        /* Clip Path */
        .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 15px 50%);
        }
        .crm-tab-container:first-child .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%);
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
</div>

<script>
    $(document).on('submit', 'form[data-confirm-message]', function(event) {
        const message = $(this).data('confirm-message');
        if (message && !window.confirm(message)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }
    });
</script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lead Details</h4>
        @if($lead->crm_stage !== 'Closed')
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#actionModal">
                Action
            </button>
        @endif
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Lead Details Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer Name:</strong> {{ $lead->customer_name }}</p>
                            <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                            <p><strong>CRM Stage:</strong> <span class="badge badge-primary">{{ $lead->crm_stage ?? 'New' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bank:</strong> {{ $lead->bank->name ?? 'N/A' }}</p>
                            <p><strong>Branch:</strong> {{ $lead->bankBranch->name ?? 'N/A' }}</p>
                            <p><strong>Start Date:</strong> {{ $lead->converted_at ? $lead->converted_at->format('d-m-Y') : $lead->created_at->format('d-m-Y') }}</p>
                            @if($lead->crm_disbursal_at && $lead->converted_at)
                                @php
                                    $startDate = \Carbon\Carbon::parse($lead->converted_at);
                                    $disbursalDate = \Carbon\Carbon::parse($lead->crm_disbursal_at);
                                    $duration = $startDate->diffInDays($disbursalDate);
                                @endphp
                                <p><strong>Disbursal Duration:</strong> {{ $duration }} days</p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    {{-- Full Lead Details Content --}}
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                            <p><strong>Father Name:</strong> {{ $lead->father_name }}</p>
                            <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                            <p><strong>Email:</strong> {{ $lead->email }}</p>
                            <p><strong>Firm:</strong> {{ $lead->firm ? $lead->firm->name : '-' }}</p>
                            <p><strong>Residence Type:</strong> {{ $lead->residence_type }}</p>
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

            {{-- Disbursal Details Card (if applicable) --}}
            @if($lead->crm_stage === 'Disbursal' || $lead->crm_stage === 'Closed')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Disbursal Details</h3>
                        @if($lead->crm_stage === 'Disbursal')
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#disbursalModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @include('crm.partials.disbursal_details')
                    </div>
                </div>
            @endif
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

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stage: {{ $lead->crm_stage }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                @if($lead->crm_stage == 'Agreement')
                    <div class="modal-body">
                        @include('crm.partials.disbursal_form', ['lead' => $lead])
                    </div>
                @elseif($lead->crm_stage == 'Disbursal')
                    <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" data-confirm-message="Close this lead?">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}">
                            <input type="hidden" name="completed_stages[]" value="Closed">
                            <p>Are you sure you want to close this lead?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="action" value="Yes" class="btn btn-success">Yes, Close Lead</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                @else
                    <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" data-confirm-message="Update this lead stage?">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}">

                            @php
                                // Removed 'Agreement' from the list as requested
                                $stagesList = ['New', 'Login', 'FI Document', 'Vehicle Evaluation', 'Credit', 'Approved', 'Agreement'];
                                $allStages = ['New', 'Login', 'FI Document', 'Vehicle Evaluation', 'Credit', 'Approved', 'Agreement', 'Disbursal', 'Closed'];
                                $currentIndex = array_search($lead->crm_stage, $allStages);
                                if ($currentIndex === false) $currentIndex = -1;
                            @endphp

                            <div class="form-group">
                                <label>Stage Progress</label>
                                @foreach($stagesList as $index => $stage)
                                    @php
                                        $stageIndex = array_search($stage, $allStages);
                                        $isChecked = $stageIndex <= $currentIndex;
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input stage-checkbox" type="checkbox"
                                            name="completed_stages[]"
                                            value="{{ $stage }}"
                                            {{ $isChecked ? 'checked disabled' : '' }}>
                                        <label class="form-check-label">{{ $stage }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <p class="text-muted small">Select stages to complete. You can select multiple stages at once.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="action" value="Yes" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Disbursal Modal -->
    <div class="modal fade" id="disbursalModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Disbursal Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('crm.partials.disbursal_form')
                </div>
            </div>
        </div>
    </div>
@stop
