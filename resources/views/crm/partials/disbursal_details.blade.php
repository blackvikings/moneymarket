<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Disbursal Information</h3>
            </div>
            <div class="card-body">
                @php
                    $financeAmount = $lead->finance_amount ?? $lead->total_loan_amount;
                    $disbursalAmount = $lead->disbursal_amount;
                @endphp

                <p><strong>Disbursal Date:</strong> {{ $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-' }}</p>
                <p><strong>Finance Amount:</strong>
                    @if($financeAmount !== null)
                        <span>&#8377;</span>{{ number_format($financeAmount, 2) }}
                    @else
                        -
                    @endif
                </p>
                <p><strong>Disbursal Amount:</strong>
                    @if($disbursalAmount !== null)
                        <span>&#8377;</span>{{ number_format($disbursalAmount, 2) }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>
{{--    <div class="col-md-6">--}}
{{--        <div class="card card-outline card-info">--}}
{{--            <div class="card-header">--}}
{{--                <h3 class="card-title">Amount Received Breakdown</h3>--}}
{{--            </div>--}}
{{--            <div class="card-body">--}}
{{--                <p><strong>Amount to Firm:</strong> <span>&#8377;</span>{{ number_format($lead->amt_rec_firm, 2) ?? '-' }}</p>--}}
{{--                <p><strong>Amount to Lead:</strong> <span>&#8377;</span>{{ number_format($lead->amt_rec_lead, 2) ?? '-' }}</p>--}}
{{--                <p><strong>Amount to Old Financer:</strong> <span>&#8377;</span>{{ number_format($lead->amt_rec_old_financer, 2) ?? '-' }}</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>

