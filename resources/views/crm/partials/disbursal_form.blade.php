<form action="{{ route('crm.store-disbursal', $lead->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Disbursal Date</label>
                <input type="date" name="disbursal_date" class="form-control" value="{{ old('disbursal_date', $lead->disbursal_date ? $lead->disbursal_date->format('Y-m-d') : '') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Finance Amount</label>
                <input type="number" step="0.01" name="finance_amount" class="form-control" value="{{ old('finance_amount', $lead->finance_amount) }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Disbursal Amount</label>
                <input type="number" step="0.01" name="disbursal_amount" id="disbursal_amount" class="form-control" value="{{ old('disbursal_amount', $lead->disbursal_amount) }}" required>
            </div>
        </div>
    </div>
    <hr>
    <h5>Amount Received At</h5>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>At (Firm Name)</label>
                <input type="number" step="0.01" name="amt_rec_firm" id="amt_rec_firm" class="form-control" value="{{ old('amt_rec_firm', $lead->amt_rec_firm ?? 0) }}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>At Lead</label>
                <input type="number" step="0.01" name="amt_rec_lead" id="amt_rec_lead" class="form-control" value="{{ old('amt_rec_lead', $lead->amt_rec_lead ?? 0) }}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>At Old Financer</label>
                <input type="number" step="0.01" name="amt_rec_old_financer" id="amt_rec_old_financer" class="form-control" value="{{ old('amt_rec_old_financer', $lead->amt_rec_old_financer ?? 0) }}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Total Amount</label>
                <input type="text" id="total_received" class="form-control" disabled>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Save Disbursal & Close Lead</button>
</form>

@section('js')
    @parent
    <script>
        $(document).ready(function() {
            function calculateTotalReceived() {
                let firm = parseFloat($('#amt_rec_firm').val()) || 0;
                let lead = parseFloat($('#amt_rec_lead').val()) || 0;
                let oldFinancer = parseFloat($('#amt_rec_old_financer').val()) || 0;
                let total = firm + lead + oldFinancer;
                $('#total_received').val(total.toFixed(2));

                // Optional: Visual validation
                let disbursalAmount = parseFloat($('#disbursal_amount').val()) || 0;
                if (total.toFixed(2) !== disbursalAmount.toFixed(2)) {
                    $('#total_received').css('border-color', 'red');
                } else {
                    $('#total_received').css('border-color', '');
                }
            }

            $('#amt_rec_firm, #amt_rec_lead, #amt_rec_old_financer, #disbursal_amount').on('input', calculateTotalReceived);

            // Run calculation on page load to populate total if old values exist
            calculateTotalReceived();
        });
    </script>
@stop
