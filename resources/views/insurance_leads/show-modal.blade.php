<div class="modal-header">
    <h5 class="modal-title">Insurance Lead Details: {{ $lead->customer_name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Customer Information</strong></h6>
            <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
            <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
            <p><strong>Email:</strong> {{ $lead->email ?? '-' }}</p>
            <p><strong>Aadhar Number:</strong> {{ $lead->aadhar_number }}</p>
            <p><strong>PAN Number:</strong> {{ $lead->pan_card_number ?? '-' }}</p>
            <p><strong>Lead Source:</strong> {{ $lead->lead_source ?? '-' }}
                @if($lead->lead_source == 'Any person' && $lead->lead_source_person_name)
                    ({{ $lead->lead_source_person_name }})
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <h6><strong>Vehicle Details</strong></h6>
            <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : ($lead->mfg_name ?? '-') }}</p>
            <p><strong>Model:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : ($lead->model_name ?? '-') }}</p>
            <p><strong>Variant:</strong> {{ $lead->vehicleName ? $lead->vehicleName->variant : ($lead->variant ?? '-') }}</p>
            <p><strong>RC Number:</strong> {{ $lead->rc_number ?? '-' }}</p>
            <p><strong>Vehicle Type:</strong> {{ $lead->vehicle_loan_type ?? '-' }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>Insurance Details</strong></h6>
    @if($lead->insurance_id)
        <div class="row">
            <div class="col-md-6">
                <p><strong>Vendor:</strong> {{ $lead->insurance->company_name }}</p>
                <p><strong>Policy Period:</strong> {{ $lead->insurance_start_date ? $lead->insurance_start_date->format('d-m-Y') : '-' }} to {{ $lead->insurance_end_date ? $lead->insurance_end_date->format('d-m-Y') : '-' }}</p>
                <p><strong>Type:</strong> {{ $lead->insurance_type_of_insurance }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Premium Amount:</strong> {{ $lead->insurance_premium_amount !== null ? number_format((float) $lead->insurance_premium_amount, 2) : '-' }}</p>
                <p><strong>Collected Amount:</strong> {{ $lead->premium_payment_amount !== null ? number_format((float) $lead->premium_payment_amount, 2) : '-' }}</p>
                <p><strong>Balance Amount:</strong>
                    @if($lead->insurance_premium_amount !== null)
                        {{ number_format(max(0, (float) $lead->insurance_premium_amount - (float) ($lead->premium_payment_amount ?? 0)), 2) }}
                    @else
                        -
                    @endif
                </p>
                <p><strong>IDV Amount:</strong> {{ $lead->insurance_idv_amount !== null ? number_format((float) $lead->insurance_idv_amount, 2) : '-' }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Insurance details not yet updated.</p>
    @endif

    <hr>
    <h6><strong>Address Details</strong></h6>
    <p><strong>Current Address:</strong> {{ $lead->current_address_line }}, {{ $lead->currentCity->name ?? '' }}, {{ $lead->currentState->name ?? '' }} - {{ $lead->current_address_pincode }}</p>

    @if($lead->getMedia('insurance_address_proof')->count() > 0)
        <hr>
        <h6><strong>Documents</strong></h6>
        <p class="text-success"><i class="fas fa-check-circle"></i> {{ $lead->getMedia('insurance_address_proof')->count() }} Address Proof(s) Uploaded</p>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-primary">Edit Details</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
