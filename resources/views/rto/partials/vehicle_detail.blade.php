<div class="row">
    <div class="col-md-12">
        <h5>Vehicle Details</h5>
        <p><strong>Vehicle Type:</strong> {{ $lead->vehicle_loan_type ?? 'N/A' }}</p>
        <p><strong>RC Number:</strong> {{ $lead->rc_number ?? 'N/A' }}</p>
        <p><strong>Engine Number:</strong> {{ $lead->engine_number ?? 'N/A' }}</p>
        <p><strong>Chassis Number:</strong> {{ $lead->chassis_number ?? 'N/A' }}</p>
        <p><strong>Model:</strong> {{ $lead->model_name ?? 'N/A' }}</p>
        <div class="alert alert-info">
            Note: You can edit these details in the main Lead Edit page if needed.
        </div>
    </div>
</div>
