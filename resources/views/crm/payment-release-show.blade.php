@extends('adminlte::page')

@section('title', 'Payment Release Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Payment Release Details</h1>
            <p class="text-muted mb-0">Lead #{{ $lead->id }} - {{ $lead->customer_name }}</p>
        </div>
        <a href="{{ route(get_route_prefix() . 'payment-release.index') }}" class="btn btn-secondary">
            Back to List
        </a>
    </div>
@stop

@section('content')
    <style>
        .detail-label {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .proof-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }
    </style>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Payment Information</h3>
                    <span class="badge {{ $lead->payment_release_status === 'Done' ? 'badge-success' : 'badge-warning' }}">
                        {{ $lead->payment_release_status ?? 'Pending' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
            <div class="detail-label">Payment Type</div>
            <div class="detail-value">
                @if($lead->payment_type === 'Online')
                    UPI/QR
                @elseif($lead->payment_type === 'Bank Transfer')
                    NEFT/RTGS
                @else
                    {{ $lead->payment_type ?? '-' }}
                @endif
            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Payment Amount</div>
                            <div class="detail-value">{{ $lead->payment_amount !== null ? number_format($lead->payment_amount, 2) : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Disbursal Amount</div>
                            <div class="detail-value">{{ $lead->disbursal_amount !== null ? number_format($lead->disbursal_amount, 2) : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Payment Date</div>
                            <div class="detail-value">{{ $lead->payment_date ? $lead->payment_date->format('d-m-Y') : '-' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="detail-label">Released At</div>
                            <div class="detail-value">{{ $lead->payment_release_detail_at ? $lead->payment_release_detail_at->format('d-m-Y H:i') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Disbursal Date</div>
                            <div class="detail-value">{{ $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">CRM Stage</div>
                            <div class="detail-value">{{ $lead->crm_stage ?? 'New' }}</div>
                        </div>
                    </div>

                    @if($lead->payment_remarks)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="detail-label">Remarks</div>
                                <div class="detail-value">{{ $lead->payment_remarks }}</div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        @if($lead->payment_type === 'Cheque')
                            <div class="col-md-6">
                                <div class="detail-label">Cheque Number</div>
                                <div class="detail-value">{{ $lead->payment_cheque_number ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">Cheque Proof</div>
                                <div class="detail-value">
                                    @if($lead->payment_cheque_image_path)
                                        <a href="{{ asset('storage/' . $lead->payment_cheque_image_path) }}" target="_blank" class="btn btn-sm btn-info">Open Image</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        @elseif($lead->payment_type === 'Online')
                            <div class="col-md-6">
                            <div class="detail-label">UPI / QR Ref.</div>
                            <div class="detail-value">{{ $lead->payment_upi_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">Online Payment Proof</div>
                                <div class="detail-value">
                                    @if($lead->payment_online_payment_image_path)
                                        <a href="{{ asset('storage/' . $lead->payment_online_payment_image_path) }}" target="_blank" class="btn btn-sm btn-info">Open Image</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        @elseif($lead->payment_type === 'Bank Transfer')
                            <div class="col-md-6">
                                <div class="detail-label">Account Holder Name</div>
                                <div class="detail-value">{{ $lead->payment_account_holder_name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">Account Number</div>
                                <div class="detail-value">{{ $lead->payment_account_number ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">Bank Name</div>
                                <div class="detail-value">{{ $lead->payment_bank_name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">IFSC Code</div>
                                <div class="detail-value">{{ $lead->payment_ifsc_code ?? '-' }}</div>
                            </div>
                            <div class="col-md-12">
                                <div class="detail-label">Transfer Proof</div>
                                <div class="detail-value">
                                    @if($lead->payment_bank_transfer_image_path)
                                        <a href="{{ asset('storage/' . $lead->payment_bank_transfer_image_path) }}" target="_blank" class="btn btn-sm btn-info">Open Image</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Lead Summary</h3>
                </div>
                <div class="card-body">
                    <p><strong>Customer:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    <p><strong>Bank:</strong> {{ $lead->bank ? $lead->bank->name : '-' }}</p>
                    <p><strong>Branch:</strong> {{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</p>
                    <p><strong>Assigned To:</strong> {{ $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned' }}</p>
                    <p><strong>Created By:</strong> {{ $lead->createdBy ? $lead->createdBy->name : 'Unknown' }}</p>
                    <p><strong>Converted At:</strong> {{ $lead->converted_at ? $lead->converted_at->format('d-m-Y H:i') : '-' }}</p>
                    <p><strong>Lead Type:</strong> {{ $lead->lead_type ?? '-' }}</p>
                    <p><strong>Lead Status:</strong> {{ $lead->status ?? '-' }}</p>
                </div>
            </div>

            @if($lead->statusLogs && $lead->statusLogs->count())
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Status History</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($lead->statusLogs->sortByDesc('created_at') as $log)
                                <li class="mb-3">
                                    <strong>{{ $log->status ?? '-' }}</strong>
                                    <div class="text-muted small">{{ $log->created_at ? $log->created_at->format('d-m-Y H:i') : '-' }}</div>
                                    @if($log->remark)
                                        <div class="small">{{ $log->remark }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
