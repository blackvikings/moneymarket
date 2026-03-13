<form action="{{ route(get_route_prefix() . 'rto.update-work-checklist', $lead->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <h5>Checklist</h5>
            {{-- HSRP --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">HSRP</label>
                <div class="col-sm-8">
                    <select name="rto_hsrp_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_hsrp_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_hsrp_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_hsrp_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- Insurance --}}
            @php
                $isInsuranceProper = ($lead->insurance_available == 1 && $lead->insurance_end_date && \Carbon\Carbon::parse($lead->insurance_end_date)->isFuture());
                $insuranceStatus = $isInsuranceProper ? 'Previously Done' : ($lead->rto_insurance_status ?? 'Pending');
            @endphp
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Insurance</label>
                <div class="col-sm-8">
                    <select name="rto_insurance_status" class="form-control form-control-sm" {{ $isInsuranceProper ? 'readonly' : '' }}>
                        <option value="Pending" {{ $insuranceStatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $insuranceStatus == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $insuranceStatus == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                    @if($isInsuranceProper)
                        <small class="text-success">Verified from Lead Data (Expires: {{ \Carbon\Carbon::parse($lead->insurance_end_date)->format('d-m-Y') }})</small>
                    @endif
                </div>
            </div>

            {{-- Fitness --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Fitness</label>
                <div class="col-sm-8">
                    <select name="rto_fitness_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_fitness_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_fitness_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_fitness_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- Tax --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Tax</label>
                <div class="col-sm-8">
                    <select name="rto_tax_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_tax_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_tax_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_tax_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- PUC --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">PUC</label>
                <div class="col-sm-8">
                    <select name="rto_puc_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_puc_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_puc_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_puc_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- E-challan --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">E-challan</label>
                <div class="col-sm-8">
                    <select name="rto_echallan_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_echallan_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_echallan_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_echallan_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- NCR --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">NCR</label>
                <div class="col-sm-8">
                    <select name="rto_ncr_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_ncr_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_ncr_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_ncr_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- RTO Challan --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">RTO Challan</label>
                <div class="col-sm-8">
                    <select name="rto_challan_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_challan_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_challan_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_challan_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            {{-- Permit --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Permit</label>
                <div class="col-sm-8">
                    <select name="rto_permit_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->rto_permit_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->rto_permit_status == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->rto_permit_status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h5>Work Items</h5>
            {{-- Name Transfer --}}
            <div class="form-group">
                <label>Name Transfer</label>
                <select name="rto_name_transfer_status" id="rto_name_transfer_status" class="form-control form-control-sm">
                    <option value="No" {{ $lead->rto_name_transfer_status == 'No' ? 'selected' : '' }}>No</option>
                    <option value="Yes" {{ $lead->rto_name_transfer_status == 'Yes' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div id="seller_details_div" style="{{ $lead->rto_name_transfer_status == 'Yes' ? '' : 'display:none;' }}">
                <h6>Seller Details</h6>
                <div class="form-group">
                    <label>Seller Name</label>
                    <input type="text" name="rto_seller_name" class="form-control form-control-sm" value="{{ $lead->rto_seller_name }}">
                </div>
                <div class="form-group">
                    <label>Seller Number</label>
                    <input type="text" name="rto_seller_number" class="form-control form-control-sm" value="{{ $lead->rto_seller_number }}">
                </div>
                <div class="form-group">
                    <label>Seller Address</label>
                    <textarea name="rto_seller_address" class="form-control form-control-sm" rows="2">{{ $lead->rto_seller_address }}</textarea>
                </div>
            </div>

            {{-- Hypothecation Add --}}
            <div class="form-group">
                <label>Hypothecation Add</label>
                <select name="rto_hypothecation_add_status" id="rto_hypothecation_add_status" class="form-control form-control-sm">
                    <option value="No" {{ $lead->rto_hypothecation_add_status == 'No' ? 'selected' : '' }}>No</option>
                    <option value="Yes" {{ $lead->rto_hypothecation_add_status == 'Yes' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div id="hp_add_bank_div" style="{{ $lead->rto_hypothecation_add_status == 'Yes' ? '' : 'display:none;' }}">
                <div class="form-group">
                    <label>Select Bank</label>
                    <select name="rto_hypothecation_add_bank_id" class="form-control form-control-sm">
                        <option value="">Select Bank</option>
                        @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                            <option value="{{ $bank->id }}" {{ $lead->rto_hypothecation_add_bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next (Assign Agent)</button>
</form>

<script>
    $('#rto_name_transfer_status').change(function() {
        $('#seller_details_div').toggle($(this).val() == 'Yes');
    });
    $('#rto_hypothecation_add_status').change(function() {
        $('#hp_add_bank_div').toggle($(this).val() == 'Yes');
    });
</script>
