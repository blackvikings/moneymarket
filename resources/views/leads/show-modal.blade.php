<div class="modal-header">
    <h5 class="modal-title">Lead Details: {{ $lead->customer_name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Customer Information</strong></h6>
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
            <h6><strong>Address Details</strong></h6>
            <p><strong>Current Address:</strong> {{ $lead->current_address_line }}, {{ $lead->currentCity->name ?? $lead->current_address_city }}, {{ $lead->currentState->name ?? $lead->current_address_state }} - {{ $lead->current_address_pincode }}</p>
            @if($lead->current_address_proof_path)
                <p><a href="{{ asset('storage/' . $lead->current_address_proof_path) }}" target="_blank" class="btn btn-xs btn-info">View Current Proof</a></p>
            @endif
            <p><strong>Permanent Address:</strong> {{ $lead->permanent_address_line }}, {{ $lead->permanentCity->name ?? $lead->permanent_address_city }}, {{ $lead->permanentState->name ?? $lead->permanent_address_state }} - {{ $lead->permanent_address_pincode }}</p>
            @if($lead->permanent_address_proof_path)
                <p><a href="{{ asset('storage/' . $lead->permanent_address_proof_path) }}" target="_blank" class="btn btn-xs btn-info">View Permanent Proof</a></p>
            @endif
        </div>
    </div>

    @if($lead->follow_up_date)
        <hr>
        <div class="alert alert-info py-2">
            <h6 class="mb-1"><i class="icon fas fa-calendar-alt"></i> <strong>Next Follow-up</strong></h6>
            <strong>Date:</strong> {{ $lead->follow_up_date->format('d-m-Y') }} |
            <strong>Time:</strong> {{ $lead->follow_up_time ?? 'Not specified' }}
        </div>
    @endif

    @if($lead->lead_type == 'Loan')
        <hr>
        <h6><strong>Job Profile</strong></h6>
        <p><strong>Type:</strong> {{ $lead->job_profile_type }}</p>
        <p><strong>Company:</strong> {{ $lead->other_company_name }}</p>
        <p><strong>Designation:</strong> {{ $lead->designation }}</p>

        @if($lead->job_profile_type == 'Self Employed')
            <p><strong>Nature of Work:</strong> {{ $lead->nature_of_work }}</p>
        @elseif($lead->job_profile_type == 'Salaried')
            <p><strong>Salaried Type:</strong> {{ $lead->salaried_type }}</p>
        @endif

        <p><strong>Income Proof:</strong> {{ $lead->income_proof_available ? 'Yes' : 'No' }}</p>
        @if($lead->income_proof_available)
            <p><strong>ITR Amount:</strong> {{ $lead->itr_amount }}</p>
        @else
            <p><strong>Approx Income:</strong> {{ $lead->approx_income }}</p>
        @endif
    @endif

    <hr>
    <h6><strong>{{ $lead->lead_type }} Details</strong></h6>
    @if($lead->lead_type == 'Loan')
        <p><strong>Loan Type:</strong> {{ $lead->loan_type }} {{ $lead->loan_type == 'Other' ? '('.$lead->other_loan_name.')' : '' }}</p>
        @if($lead->loan_type == 'Vehicle Loan')
            <p><strong>Vehicle Type:</strong> {{ $lead->vehicle_loan_type }}</p>
            <p><strong>Purchase Type:</strong> {{ $lead->vehicle_purchase_type }}</p>
            @if($lead->vehicle_purchase_type == 'New Purchase')
                <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : '-' }}</p>
                <p><strong>Vehicle Name:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : '-' }}</p>
                <p><strong>Variant:</strong> {{ $lead->vehicle_variant }}</p>
            @else
                <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
                <p><strong>Model:</strong> {{ $lead->model_name }} ({{ $lead->variant }})</p>
            @endif
        @endif
    @elseif($lead->lead_type == 'RTO')
        <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
        <p><strong>Manufacturer:</strong> {{ $lead->mfg_name }}</p>
        <p><strong>Model:</strong> {{ $lead->model_name }} ({{ $lead->variant }})</p>
    @elseif($lead->lead_type == 'Insurance')
        <p><strong>Manufacturer:</strong> {{ $lead->mfg_name }}</p>
        <p><strong>Model:</strong> {{ $lead->model_name }} ({{ $lead->variant }})</p>
    @endif

    <hr>
    <h6><strong>Status History</strong></h6>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>Status</th>
                <th>Date</th>
                <th>By</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lead->statusLogs->sortByDesc('created_at') as $log)
                <tr>
                    <td>{{ $log->status == 'New' ? 'New Task' : ($log->status == 'Prospect' ? 'Follow up' : str_replace('_', ' ', $log->status)) }}</td>
                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                    <td>{{ $log->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix() . 'leads.edit', $lead->id) }}" class="btn btn-primary">Edit Lead</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
