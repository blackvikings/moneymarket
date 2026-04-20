@extends('adminlte::page')

@section('title', 'Lead Details')

@section('css')
    <style>
        .lead-details-page .card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .lead-details-page .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        .lead-details-page .hero-card {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: 0;
        }

        .lead-details-page .hero-card .text-muted,
        .lead-details-page .hero-card small {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .lead-details-page .summary-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            padding: 8px 12px;
            margin: 0 8px 8px 0;
            background: rgba(255, 255, 255, 0.06);
        }

        .lead-details-page .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 0 0 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #edf2f7;
        }

        .lead-details-page .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 18px;
        }

        .lead-details-page .detail-item {
            line-height: 1.5;
        }

        .lead-details-page .detail-item strong {
            display: inline-block;
            min-width: 148px;
            color: #334155;
        }

        .lead-details-page .workflow-stage {
            min-height: 104px;
        }

        .lead-details-page .history-item + .history-item {
            border-top: 1px solid #edf2f7;
            padding-top: 12px;
            margin-top: 12px;
        }

        .lead-details-page .raw-table th {
            width: 32%;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .lead-details-page .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .content-header,
            .main-sidebar,
            .main-header,
            .footer,
            .hero-actions,
            .btn {
                display: none !important;
            }

            .lead-details-page .card {
                box-shadow: none;
            }
        }
    </style>
@stop

@section('content_header')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 12px;">
        <div>
            <h1 class="mb-1">Lead Details: {{ $lead->customer_name }}</h1>
            <div class="text-muted">Profile, workflow, documents, and raw stored data</div>
        </div>
        <div class="d-flex flex-wrap hero-actions" style="gap: 8px;">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route(get_route_prefix() . 'leads.edit', $lead->id) }}" class="btn btn-primary">
                <i class="fas fa-pen mr-1"></i> Edit Lead
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $statusLabel = str_replace('_', ' ', $lead->status ?? 'New');
        $leadTypeLabel = $lead->lead_type ?: '-';
        $assignedName = $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned';
        $createdName = $lead->createdBy ? $lead->createdBy->name : 'Unknown';
        $rawAttributes = collect($lead->getAttributes())
            ->except(['id', 'created_at', 'updated_at', 'deleted_at'])
            ->filter(fn ($value) => !is_null($value) && $value !== '');
        $crmStages = [
            'New' => 'crm_new_at',
            'Login' => 'crm_login_at',
            'FI Document' => 'crm_fi_document_at',
            'Vehicle Evaluation' => 'crm_vehicle_evaluation_at',
            'Credit' => 'crm_credit_at',
            'Approved' => 'crm_approved_at',
            'Agreement' => 'crm_agreement_at',
            'Disbursal' => 'crm_disbursal_at',
        ];
    @endphp

    <div class="lead-details-page">
        <div class="card hero-card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <span class="summary-pill"><i class="fas fa-user"></i> {{ $lead->customer_name }}</span>
                            <span class="summary-pill"><i class="fas fa-tag"></i> {{ $leadTypeLabel }}</span>
                            <span class="summary-pill"><i class="fas fa-stream"></i> {{ $statusLabel }}</span>
                            @if($lead->bank)
                                <span class="summary-pill"><i class="fas fa-university"></i> {{ $lead->bank->name }}</span>
                            @endif
                        </div>
                        <h4 class="mb-1">{{ $lead->father_name ?: 'Lead Overview' }}</h4>
                        <div class="text-muted">
                            Created by {{ $createdName }} | Assigned to {{ $assignedName }} | Created at {{ $lead->created_at->format('d-m-Y H:i') }}
                        </div>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="p-3 rounded border bg-white">
                                    <div class="text-muted small text-uppercase">Contact</div>
                                    <div class="h5 mb-0">{{ $lead->contact_number }}</div>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="p-3 rounded border bg-white">
                                    <div class="text-muted small text-uppercase">City</div>
                                    <div class="h5 mb-0">{{ $lead->currentCity->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded border bg-white">
                                    <div class="text-muted small text-uppercase">Follow Up</div>
                                    <div class="h5 mb-0">{{ $lead->follow_up_date ? $lead->follow_up_date->format('d-m-Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded border bg-white">
                                    <div class="text-muted small text-uppercase">Aging</div>
                                    <div class="h5 mb-0">{{ $lead->aging ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title mb-0">Customer Profile</h3></div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div class="detail-item"><strong>Name:</strong> {{ $lead->customer_name }}</div>
                            <div class="detail-item"><strong>Father Name:</strong> {{ $lead->father_name ?: '-' }}</div>
                            <div class="detail-item"><strong>Contact:</strong> {{ $lead->contact_number ?: '-' }}</div>
                            <div class="detail-item"><strong>Email:</strong> {{ $lead->email ?: '-' }}</div>
                            <div class="detail-item"><strong>Firm:</strong> {{ $lead->firm ? $lead->firm->name : '-' }}</div>
                            <div class="detail-item"><strong>Residence Type:</strong> {{ $lead->residence_type ?: '-' }}</div>
                            <div class="detail-item"><strong>Aadhar Number:</strong> {{ $lead->aadhar_number ?: '-' }}</div>
                            <div class="detail-item"><strong>Profile Picture:</strong> {{ $lead->profile_picture_path ? 'Available' : '-' }}</div>
                        </div>
                        @if($lead->profile_picture_path)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $lead->profile_picture_path) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title mb-0">Addresses</h3></div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <strong>Current Address:</strong>
                                {{ trim(($lead->current_address_line ?? '') . ', ' . ($lead->currentCity->name ?? $lead->current_address_city ?? '') . ', ' . ($lead->currentState->name ?? $lead->current_address_state ?? '') . ' - ' . ($lead->current_address_pincode ?? ''), ', -') ?: '-' }}
                            </div>
                            <div class="detail-item">
                                <strong>Permanent Address:</strong>
                                {{ trim(($lead->permanent_address_line ?? '') . ', ' . ($lead->permanentCity->name ?? $lead->permanent_address_city ?? '') . ', ' . ($lead->permanentState->name ?? $lead->permanent_address_state ?? '') . ' - ' . ($lead->permanent_address_pincode ?? ''), ', -') ?: '-' }}
                            </div>
                            <div class="detail-item">
                                <strong>Current Proof:</strong>
                                @if($lead->current_address_proof_path)
                                    <a href="{{ asset('storage/' . $lead->current_address_proof_path) }}" target="_blank">Open file</a>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="detail-item">
                                <strong>Permanent Proof:</strong>
                                @if($lead->permanent_address_proof_path)
                                    <a href="{{ asset('storage/' . $lead->permanent_address_proof_path) }}" target="_blank">Open file</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title mb-0">Lead Specific Details</h3></div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div class="detail-item"><strong>Lead Source:</strong> {{ $lead->lead_source ?: '-' }}</div>
                            <div class="detail-item"><strong>Source Person:</strong> {{ $lead->lead_source_person_name ?: '-' }}</div>
                            <div class="detail-item"><strong>Job Profile:</strong> {{ $lead->job_profile ?: '-' }}</div>
                            <div class="detail-item"><strong>Income Proof:</strong> {{ $lead->income_proof_available ? 'Yes' : 'No' }}</div>
                        </div>

                        @if($lead->job_profile == 'Self Employed')
                            <div class="section-title mt-3"><h5 class="mb-0">Employment</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>Nature of Work:</strong> {{ $lead->nature_of_work ?: '-' }}</div>
                            </div>
                        @elseif($lead->job_profile == 'Salaried')
                            <div class="section-title mt-3"><h5 class="mb-0">Employment</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>Salaried Type:</strong> {{ $lead->salaried_type ?: '-' }}</div>
                                <div class="detail-item"><strong>Designation:</strong> {{ $lead->designation ?: '-' }}</div>
                                @if($lead->salaried_type == 'Other')
                                    <div class="detail-item"><strong>Company:</strong> {{ $lead->other_company_name ?: '-' }}</div>
                                @endif
                            </div>
                        @endif

                        @if($lead->income_proof_available)
                            <div class="section-title mt-3"><h5 class="mb-0">Income Proof</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>ITR Amount:</strong> {{ $lead->itr_amount ?: '-' }}</div>
                                <div class="detail-item"><strong>Approx Income:</strong> {{ $lead->approx_income ?: '-' }}</div>
                                <div class="detail-item"><strong>ITR Document:</strong> {{ $lead->itr_document_path ? 'Available' : '-' }}</div>
                            </div>
                            @if($lead->itr_document_path)
                                <div class="mt-2"><a href="{{ asset('storage/' . $lead->itr_document_path) }}" target="_blank" class="btn btn-sm btn-info">View ITR Doc</a></div>
                            @endif
                        @endif

                        @if($lead->lead_type == 'Loan')
                            <div class="section-title mt-3"><h5 class="mb-0">Loan Details</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>Loan Type:</strong> {{ $lead->loan_type }} {{ $lead->loan_type == 'Other' ? '('.$lead->other_loan_name.')' : '' }}</div>
                                <div class="detail-item"><strong>Vehicle Loan Type:</strong> {{ $lead->vehicle_loan_type ?: '-' }} {{ $lead->vehicle_loan_type == 'Other' ? '('.$lead->other_vehicle_loan_name.')' : '' }}</div>
                                <div class="detail-item"><strong>Purchase Type:</strong> {{ $lead->vehicle_purchase_type ?: '-' }}</div>
                                <div class="detail-item"><strong>Total Loan Amount:</strong> {{ $lead->total_loan_amount ?: '-' }}</div>
                            </div>
                        @endif

                        @if($lead->lead_type == 'RTO' || $lead->lead_type == 'Insurance')
                            <div class="section-title mt-3"><h5 class="mb-0">{{ $lead->lead_type }} Details</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>RC Number:</strong> {{ $lead->rc_number ?: '-' }}</div>
                                <div class="detail-item"><strong>Engine Number:</strong> {{ $lead->engine_number ?: '-' }}</div>
                                <div class="detail-item"><strong>Chassis Number:</strong> {{ $lead->chassis_number ?: '-' }}</div>
                                <div class="detail-item"><strong>Mfg Year:</strong> {{ $lead->mfg_year ?: '-' }}</div>
                                <div class="detail-item"><strong>Mfg Name:</strong> {{ $lead->mfg_name ?: '-' }}</div>
                                <div class="detail-item"><strong>Model Name:</strong> {{ $lead->model_name ?: '-' }}</div>
                                <div class="detail-item"><strong>Variant:</strong> {{ $lead->variant ?: '-' }}</div>
                                <div class="detail-item"><strong>Owner Srl No:</strong> {{ $lead->owner_serial_number ?: '-' }}</div>
                                <div class="detail-item"><strong>Hypothecation:</strong> {{ $lead->hypothecation_status ? 'Yes' : 'No' }}</div>
                                @if($lead->lead_type == 'RTO')
                                    <div class="detail-item"><strong>Insurance Available:</strong> {{ $lead->insurance_available ? 'Yes' : 'No' }}</div>
                                @endif
                            </div>
                        @endif

                        @if($lead->insurance_available)
                            <div class="section-title mt-3"><h5 class="mb-0">Insurance Policy Details</h5></div>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>Vendor:</strong> {{ $lead->insurance ? $lead->insurance->company_name : '-' }}</div>
                                <div class="detail-item"><strong>Start Date:</strong> {{ $lead->insurance_start_date ? $lead->insurance_start_date->format('d-m-Y') : '-' }}</div>
                                <div class="detail-item"><strong>End Date:</strong> {{ $lead->insurance_end_date ? $lead->insurance_end_date->format('d-m-Y') : '-' }}</div>
                                <div class="detail-item"><strong>Amount:</strong> {{ $lead->insurance_amount ?: '-' }}</div>
                            </div>
                        @endif

                        @if($lead->has_co_applicant || $lead->has_guarantor || $lead->ref1_name || $lead->ref2_name)
                            <div class="section-title mt-3"><h5 class="mb-0">Family & References</h5></div>
                            <div class="detail-grid">
                                @if($lead->has_co_applicant)
                                    <div class="detail-item"><strong>Co-Applicant:</strong> {{ $lead->co_name ?: '-' }} {{ $lead->co_relation ? '(' . $lead->co_relation . ')' : '' }}</div>
                                @endif
                                @if($lead->has_guarantor)
                                    <div class="detail-item"><strong>Guarantor:</strong> {{ $lead->guarantor_name ?: '-' }} {{ $lead->guarantor_relation ? '(' . $lead->guarantor_relation . ')' : '' }}</div>
                                @endif
                                @if($lead->ref1_name)
                                    <div class="detail-item"><strong>Reference 1:</strong> {{ $lead->ref1_name }} {{ $lead->ref1_mobile ? '(' . $lead->ref1_mobile . ')' : '' }}</div>
                                @endif
                                @if($lead->ref2_name)
                                    <div class="detail-item"><strong>Reference 2:</strong> {{ $lead->ref2_name }} {{ $lead->ref2_mobile ? '(' . $lead->ref2_mobile . ')' : '' }}</div>
                                @endif
                            </div>
                        @endif

                        <div class="section-title mt-3"><h5 class="mb-0">Amount Breakdown</h5></div>
                        <div class="detail-grid">
                            <div class="detail-item"><strong>Old Loan Close:</strong> {{ $lead->old_loan_close_amount ?: '-' }}</div>
                            <div class="detail-item"><strong>Required In Hand:</strong> {{ $lead->required_in_hand_amount ?: '-' }}</div>
                            <div class="detail-item"><strong>Old Financer:</strong> {{ $lead->old_financer ?: '-' }}</div>
                            <div class="detail-item"><strong>Close Amount:</strong> {{ $lead->close_amount ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title mb-0">Raw Stored Data</h3></div>
                    <div class="card-body p-0">
                        @if($rawAttributes->count())
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0 raw-table">
                                    <tbody>
                                        @foreach($rawAttributes as $key => $value)
                                            <tr>
                                                <th>{{ \Illuminate\Support\Str::headline($key) }}</th>
                                                <td>
                                                    @if(is_array($value))
                                                        {{ json_encode($value) }}
                                                    @elseif(is_bool($value))
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-3 text-muted">No additional raw fields stored yet.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @if($lead->follow_up_date)
                    <div class="card mb-3">
                        <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-calendar-alt mr-1"></i> Next Follow-up</h3></div>
                        <div class="card-body">
                            <div class="detail-item"><strong>Date:</strong> {{ $lead->follow_up_date->format('d-m-Y') }}</div>
                            <div class="detail-item"><strong>Time:</strong> {{ $lead->follow_up_time ?? 'Not specified' }}</div>
                        </div>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header"><h3 class="card-title mb-0">Status History</h3></div>
                    <div class="card-body">
                        @if($lead->statusLogs->count())
                            @foreach($lead->statusLogs->sortByDesc('created_at') as $log)
                                <div class="history-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $log->status == 'New' ? 'New Task' : ($log->status == 'Prospect' ? 'Follow up' : str_replace('_', ' ', $log->status)) }}</strong>
                                            <div class="text-muted small">By: {{ $log->user ? $log->user->name : 'System' }}</div>
                                        </div>
                                        <span class="text-muted small">{{ $log->created_at->format('d-m-Y H:i') }}</span>
                                    </div>
                                    @if($log->remark)
                                        <div class="text-muted small mt-1">Remark: {{ $log->remark }}</div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">No history available.</p>
                        @endif
                    </div>
                </div>

                @if($lead->bank_id)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Bank Assign Journey</h3>
                            <span class="badge badge-primary">CRM Tracking</span>
                        </div>
                        <div class="card-body">
                            <div class="detail-grid mb-3">
                                <div class="detail-item"><strong>Bank:</strong> {{ $lead->bank->name }}</div>
                                <div class="detail-item"><strong>Branch:</strong> {{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</div>
                            </div>
                            <div class="row">
                                @foreach($crmStages as $stage => $column)
                                    <div class="col-12 col-sm-6 mb-3">
                                        <div class="workflow-stage border rounded p-3 h-100 {{ $lead->$column ? 'bg-light border-success' : 'bg-white' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="font-weight-bold">{{ $stage }}</div>
                                                <span class="badge {{ $lead->$column ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $lead->$column ? 'Done' : 'Pending' }}
                                                </span>
                                            </div>
                                            @if($lead->$column)
                                                <div class="text-muted small"><i class="fas fa-clock mr-1"></i>{{ $lead->$column->format('d-m-Y H:i') }}</div>
                                            @else
                                                <div class="text-muted small">Not reached yet</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($lead->media->count())
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Documents & Media</h3></div>
                        <div class="list-group list-group-flush">
                            @foreach($lead->media as $media)
                                <a href="{{ $media->getFullUrl() }}" target="_blank" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $media->name ?: $media->file_name }}</strong><br>
                                            <small class="text-muted">{{ $media->collection_name }} | {{ $media->file_name }}</small>
                                        </div>
                                        <span class="badge badge-light">Open</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
