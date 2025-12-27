<form action="{{ route('rto.update-checklist', $lead->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hsrp_status" id="hsrp" {{ $lead->rto_hsrp_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hsrp">HSRP</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hypothecation_status" id="hypothecation" {{ $lead->rto_hypothecation_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hypothecation">Hypothecation</label>
            </div>
            <div class="form-group" id="hypothecation_bank_div" style="{{ $lead->rto_hypothecation_status ? '' : 'display:none;' }}">
                <label>Select Bank</label>
                <select name="rto_hypothecation_bank_id" class="form-control">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ $lead->rto_hypothecation_bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_insurance_status" id="insurance" {{ $lead->rto_insurance_status ? 'checked' : '' }}>
                <label class="form-check-label" for="insurance">Insurance</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_fitness_status" id="fitness" {{ $lead->rto_fitness_status ? 'checked' : '' }}>
                <label class="form-check-label" for="fitness">Fitness</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_tax_status" id="tax" {{ $lead->rto_tax_status ? 'checked' : '' }}>
                <label class="form-check-label" for="tax">Tax</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_puc_status" id="puc" {{ $lead->rto_puc_status ? 'checked' : '' }}>
                <label class="form-check-label" for="puc">PUC</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_echallan_status" id="echallan" {{ $lead->rto_echallan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="echallan">E-challan</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_ncr_status" id="ncr" {{ $lead->rto_ncr_status ? 'checked' : '' }}>
                <label class="form-check-label" for="ncr">NCR</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_challan_status" id="challan" {{ $lead->rto_challan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="challan">RTO Challan</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_permit_status" id="permit" {{ $lead->rto_permit_status ? 'checked' : '' }}>
                <label class="form-check-label" for="permit">Permit</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
</form>

@section('js')
    @parent
    <script>
        $(document).ready(function() {
            $('#hypothecation').change(function() {
                if(this.checked) {
                    $('#hypothecation_bank_div').show();
                } else {
                    $('#hypothecation_bank_div').hide();
                }
            });
        });
    </script>
@stop
