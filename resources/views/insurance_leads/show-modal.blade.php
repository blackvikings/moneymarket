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
        </div>
        <div class="col-md-6">
            <h6><strong>Vehicle Details</strong></h6>
            <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : ($lead->mfg_name ?? '-') }}</p>
            <p><strong>Model:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : ($lead->model_name ?? '-') }}</p>
            <p><strong>Variant:</strong> {{ $lead->vehicle_variant ?? ($lead->variant ?? '-') }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>Insurance Details</strong></h6>
    @if($lead->insurance_id)
        <div class="row">
            <div class="col-md-6">
                <p><strong>Vendor:</strong> {{ $lead->insurance->company_name }}</p>
                <p><strong>Policy Period:</strong> {{ $lead->insurance_start_date->format('d-m-Y') }} to {{ $lead->insurance_end_date->format('d-m-Y') }}</p>
                <p><strong>Type:</strong> {{ $lead->insurance_type_of_insurance }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Premium Amount:</strong> {{ number_format($lead->insurance_premium_amount, 2) }}</p>
                <p><strong>IDV Amount:</strong> {{ number_format($lead->insurance_idv_amount, 2) }}</p>
            </div>
        </div>
    @else
        <p class="text-muted">Insurance details not yet updated.</p>
    @endif

    <hr>
    <h6><strong>Address Details</strong></h6>
    <p><strong>Current Address:</strong> {{ $lead->current_address_line }}, {{ $lead->currentCity->name ?? '' }}, {{ $lead->currentState->name ?? '' }} - {{ $lead->current_address_pincode }}</p>
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-primary">Edit Details</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
