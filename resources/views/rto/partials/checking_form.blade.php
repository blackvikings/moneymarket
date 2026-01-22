<form action="{{ route(get_route_prefix() . 'rto.update-checklist', $lead->id) }}" method="POST" class="rto-checking-form">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_hsrp_status" id="hsrp_{{ $lead->id }}" data-remark-target="#hsrp_remark_div_{{ $lead->id }}" {{ $lead->rto_hsrp_status ? 'checked' : '' }}>
                <label class="form-check-label" for="hsrp_{{ $lead->id }}">HSRP</label>
            </div>
            <div class="form-group rto-remark-div" id="hsrp_remark_div_{{ $lead->id }}" style="{{ $lead->rto_hsrp_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_hsrp_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_hsrp_remark }}">
            </div>

            @php
                $hpChecked = $lead->rto_hypothecation_status === 1 || ($lead->rto_hypothecation_status === null && $lead->hypothecation_status == 1);
            @endphp
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_hypothecation_status" id="hypothecation_{{ $lead->id }}" data-remark-target="#hypothecation_remark_div_{{ $lead->id }}" {{ $hpChecked ? 'checked' : '' }}>
                <label class="form-check-label" for="hypothecation_{{ $lead->id }}">Hypothecation</label>
            </div>
            <div class="form-group rto-remark-div" id="hypothecation_remark_div_{{ $lead->id }}" style="{{ $hpChecked ? '' : 'display:none;' }}">
                <input type="text" name="rto_hypothecation_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_hypothecation_remark }}">
            </div>
            <div class="form-group" id="hypothecation_bank_div_{{ $lead->id }}" style="{{ $hpChecked ? '' : 'display:none;' }}">
                <label>Select Bank</label>
                <select name="rto_hypothecation_bank_id" class="form-control">
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ ($lead->rto_hypothecation_bank_id == $bank->id || ($lead->rto_hypothecation_bank_id === null && $lead->bank_id == $bank->id)) ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_insurance_status" id="insurance_{{ $lead->id }}" data-remark-target="#insurance_remark_div_{{ $lead->id }}" {{ $lead->rto_insurance_status ? 'checked' : '' }}>
                <label class="form-check-label" for="insurance_{{ $lead->id }}">Insurance</label>
            </div>
            <div class="form-group rto-remark-div" id="insurance_remark_div_{{ $lead->id }}" style="{{ $lead->rto_insurance_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_insurance_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_insurance_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_fitness_status" id="fitness_{{ $lead->id }}" data-remark-target="#fitness_remark_div_{{ $lead->id }}" {{ $lead->rto_fitness_status ? 'checked' : '' }}>
                <label class="form-check-label" for="fitness_{{ $lead->id }}">Fitness</label>
            </div>
            <div class="form-group rto-remark-div" id="fitness_remark_div_{{ $lead->id }}" style="{{ $lead->rto_fitness_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_fitness_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_fitness_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_tax_status" id="tax_{{ $lead->id }}" data-remark-target="#tax_remark_div_{{ $lead->id }}" {{ $lead->rto_tax_status ? 'checked' : '' }}>
                <label class="form-check-label" for="tax_{{ $lead->id }}">Tax</label>
            </div>
            <div class="form-group rto-remark-div" id="tax_remark_div_{{ $lead->id }}" style="{{ $lead->rto_tax_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_tax_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_tax_remark }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_puc_status" id="puc_{{ $lead->id }}" data-remark-target="#puc_remark_div_{{ $lead->id }}" {{ $lead->rto_puc_status ? 'checked' : '' }}>
                <label class="form-check-label" for="puc_{{ $lead->id }}">PUC</label>
            </div>
            <div class="form-group rto-remark-div" id="puc_remark_div_{{ $lead->id }}" style="{{ $lead->rto_puc_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_puc_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_puc_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_echallan_status" id="echallan_{{ $lead->id }}" data-remark-target="#echallan_remark_div_{{ $lead->id }}" {{ $lead->rto_echallan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="echallan_{{ $lead->id }}">E-challan</label>
            </div>
            <div class="form-group rto-remark-div" id="echallan_remark_div_{{ $lead->id }}" style="{{ $lead->rto_echallan_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_echallan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_echallan_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_ncr_status" id="ncr_{{ $lead->id }}" data-remark-target="#ncr_remark_div_{{ $lead->id }}" {{ $lead->rto_ncr_status ? 'checked' : '' }}>
                <label class="form-check-label" for="ncr_{{ $lead->id }}">NCR</label>
            </div>
            <div class="form-group rto-remark-div" id="ncr_remark_div_{{ $lead->id }}" style="{{ $lead->rto_ncr_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_ncr_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_ncr_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_challan_status" id="challan_{{ $lead->id }}" data-remark-target="#challan_remark_div_{{ $lead->id }}" {{ $lead->rto_challan_status ? 'checked' : '' }}>
                <label class="form-check-label" for="challan_{{ $lead->id }}">RTO Challan</label>
            </div>
            <div class="form-group rto-remark-div" id="challan_remark_div_{{ $lead->id }}" style="{{ $lead->rto_challan_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_challan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_challan_remark }}">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input rto-status-checkbox" name="rto_permit_status" id="permit_{{ $lead->id }}" data-remark-target="#permit_remark_div_{{ $lead->id }}" {{ $lead->rto_permit_status ? 'checked' : '' }}>
                <label class="form-check-label" for="permit_{{ $lead->id }}">Permit</label>
            </div>
            <div class="form-group rto-remark-div" id="permit_remark_div_{{ $lead->id }}" style="{{ $lead->rto_permit_status ? '' : 'display:none;' }}">
                <input type="text" name="rto_permit_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_permit_remark }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next</button>
</form>
