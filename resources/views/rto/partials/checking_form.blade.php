<form action="{{ route(get_route_prefix() . 'rto.update-checklist', $lead->id) }}" method="POST" class="rto-checking-form">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hsrp_status" id="hsrp_{{ $lead->id }}" {{ $lead->rto_hsrp_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hsrp_{{ $lead->id }}">HSRP</label>
                <input type="text" name="rto_hsrp_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_hsrp_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_hypothecation_status" id="hypothecation_{{ $lead->id }}" {{ $lead->rto_hypothecation_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hypothecation_{{ $lead->id }}">Hypothecation</label>
                <input type="text" name="rto_hypothecation_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_hypothecation_remark }}">
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
                <input type="text" name="rto_insurance_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_insurance_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_fitness_status" id="fitness_{{ $lead->id }}" {{ $lead->rto_fitness_status ? 'checked' : '' }}>
                <label class="form-check-label" for="fitness_{{ $lead->id }}">Fitness</label>
                <input type="text" name="rto_fitness_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_fitness_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_tax_status" id="tax_{{ $lead->id }}" {{ $lead->rto_tax_status ? 'checked' : '' }}>
                <label class="form-check-label" for="tax_{{ $lead->id }}">Tax</label>
                <input type="text" name="rto_tax_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_tax_remark }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_puc_status" id="puc_{{ $lead->id }}" {{ $lead->rto_puc_status ? 'checked' : '' }}>
                <label class="form-check-label" for="puc_{{ $lead->id }}">PUC</label>
                <input type="text" name="rto_puc_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_puc_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_echallan_status" id="echallan_{{ $lead->id }}" {{ $lead->rto_echallan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="echallan_{{ $lead->id }}">E-challan</label>
                <input type="text" name="rto_echallan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_echallan_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_ncr_status" id="ncr_{{ $lead->id }}" {{ $lead->rto_ncr_status ? 'checked' : '' }}>
                <label class="form-check-label" for="ncr_{{ $lead->id }}">NCR</label>
                <input type="text" name="rto_ncr_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_ncr_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_challan_status" id="challan_{{ $lead->id }}" {{ $lead->rto_challan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="challan_{{ $lead->id }}">RTO Challan</label>
                <input type="text" name="rto_challan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_challan_remark }}">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="rto_permit_status" id="permit_{{ $lead->id }}" {{ $lead->rto_permit_status ? 'checked' : '' }}>
                <label class="form-check-label" for="permit_{{ $lead->id }}">Permit</label>
                <input type="text" name="rto_permit_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_permit_remark }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next</button>
</form>
