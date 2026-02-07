<div class="modal-header">
    <h5 class="modal-title">RTO Lead Details: {{ $lead->customer_name }}</h5>
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
            <p><strong>Aadhar Number:</strong> {{ $lead->aadhar_number }}</p>
            <p><strong>RTO Stage:</strong> <span class="badge badge-info">{{ $lead->rto_stage ?? 'New' }}</span></p>
        </div>
        <div class="col-md-6">
            <h6><strong>Vehicle Details</strong></h6>
            <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : ($lead->mfg_name ?? '-') }}</p>
            <p><strong>Model:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : ($lead->model_name ?? '-') }}</p>
            <p><strong>Variant:</strong> {{ $lead->vehicle_variant ?? ($lead->variant ?? '-') }}</p>
            <p><strong>RC Number:</strong> {{ $lead->rc_number ?? '-' }}</p>
            <p><strong>Mfg Year:</strong> {{ $lead->mfg_year ?? '-' }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>RTO Checklist Status</strong></h6>
    <div class="row">
        <div class="col-md-4"><p><strong>HSRP:</strong> {!! $lead->rto_hsrp_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
        <div class="col-md-4"><p><strong>Hypothecation:</strong> {!! $lead->rto_hypothecation_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
        <div class="col-md-4"><p><strong>Insurance:</strong> {!! $lead->rto_insurance_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
        <div class="col-md-4"><p><strong>Fitness:</strong> {!! $lead->rto_fitness_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
        <div class="col-md-4"><p><strong>Tax:</strong> {!! $lead->rto_tax_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
        <div class="col-md-4"><p><strong>PUC:</strong> {!! $lead->rto_puc_status ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}</p></div>
    </div>

    @if($lead->rto_agent_id)
        <hr>
        <h6><strong>Agent Details</strong></h6>
        <p><strong>Assigned Agent:</strong> {{ $lead->rtoAgent->name }} ({{ $lead->rtoAgent->number }})</p>
    @endif

    @if($lead->rto_stage == 'Loan Return')
        <hr>
        <div class="alert alert-warning">
            <h6><strong>Loan Return Details</strong></h6>
            <p><strong>Date:</strong> {{ $lead->loan_return_date ? $lead->loan_return_date->format('d-m-Y') : '-' }}</p>
            <p><strong>Reason:</strong> {{ $lead->loan_return_reason }}</p>
        </div>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix() . 'rto.index') }}" class="btn btn-secondary">Back to List</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
