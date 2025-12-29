<div class="row">
    <div class="col-md-6">
        <p><strong>Disbursal Date:</strong> {{ $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-' }}</p>
        <p><strong>Finance Amount:</strong> {{ $lead->finance_amount ?? '-' }}</p>
        <p><strong>Disbursal Amount:</strong> {{ $lead->disbursal_amount ?? '-' }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Amount to Firm:</strong> {{ $lead->amt_rec_firm ?? '-' }}</p>
        <p><strong>Amount to Lead:</strong> {{ $lead->amt_rec_lead ?? '-' }}</p>
        <p><strong>Amount to Old Financer:</strong> {{ $lead->amt_rec_old_financer ?? '-' }}</p>
    </div>
</div>
