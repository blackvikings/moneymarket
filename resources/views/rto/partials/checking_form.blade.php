<form action="{{ route('rto.update-checklist', $lead->id) }}" method="POST" class="rto-checking-form">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hsrp_status" id="hsrp_{{ $lead->id }}" {{ $lead->rto_hsrp_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hsrp_{{ $lead->id }}">HSRP</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hypothecation_status" id="hypothecation_{{ $lead->id }}" {{ $lead->rto_hypothecation_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hypothecation_{{ $lead->id }}">Hypothecation</label>
            </div>
            <div class="form-group" id="hypothecation_bank_div_{{ $lead->id }}" style="{{ $lead->rto_hypothecation_status ? '' : 'display:none;' }}">
                <label>Select Bank</label>
                <select name="rto_hypothecation_bank_id" class="form-control">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ $lead->rto_hypothecation_bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_insurance_status" id="insurance_{{ $lead->id }}" {{ $lead->rto_insurance_status ? 'checked' : '' }}>
                <label class="form-check-label" for="insurance_{{ $lead->id }}">Insurance</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_fitness_status" id="fitness_{{ $lead->id }}" {{ $lead->rto_fitness_status ? 'checked' : '' }}>
                <label class="form-check-label" for="fitness_{{ $lead->id }}">Fitness</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_tax_status" id="tax_{{ $lead->id }}" {{ $lead->rto_tax_status ? 'checked' : '' }}>
                <label class="form-check-label" for="tax_{{ $lead->id }}">Tax</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_puc_status" id="puc_{{ $lead->id }}" {{ $lead->rto_puc_status ? 'checked' : '' }}>
                <label class="form-check-label" for="puc_{{ $lead->id }}">PUC</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_echallan_status" id="echallan_{{ $lead->id }}" {{ $lead->rto_echallan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="echallan_{{ $lead->id }}">E-challan</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_ncr_status" id="ncr_{{ $lead->id }}" {{ $lead->rto_ncr_status ? 'checked' : '' }}>
                <label class="form-check-label" for="ncr_{{ $lead->id }}">NCR</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_challan_status" id="challan_{{ $lead->id }}" {{ $lead->rto_challan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="challan_{{ $lead->id }}">RTO Challan</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_permit_status" id="permit_{{ $lead->id }}" {{ $lead->rto_permit_status ? 'checked' : '' }}>
                <label class="form-check-label" for="permit_{{ $lead->id }}">Permit</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next</button>
</form>
