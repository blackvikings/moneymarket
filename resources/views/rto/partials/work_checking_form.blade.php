<form action="{{ route(get_route_prefix() . 'rto.update-work-checklist', $lead->id) }}" method="POST" data-confirm-message="Save this work checklist?">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <h5>Pending from Documents</h5>
            @php
                $checklist = [
                    'rto_hsrp_status' => 'HSRP',
                    'rto_insurance_status' => 'Insurance',
                    'rto_fitness_status' => 'Fitness',
                    'rto_tax_status' => 'Tax',
                    'rto_puc_status' => 'PUC',
                    'rto_echallan_status' => 'E-challan',
                    'rto_ncr_status' => 'NCR',
                    'rto_challan_status' => 'RTO Challan',
                    'rto_permit_status' => 'Permit'
                ];
            @endphp

            @foreach($checklist as $field => $label)
                @php
                    $v = $lead->$field;
                    $isDone = $v === 'Done'
                        || $v === 'Previously Done'
                        || $v === 1 || $v === '1' || $v === true
                        || $v === 2 || $v === '2';
                @endphp
                @if(!$isDone)
                    <div class="form-group">
                        <label>{{ $label }}</label>
                        <select name="{{ $field }}" class="form-control form-control-sm">
                            <option value="Pending" selected>Pending</option>
                            <option value="Done">Done</option>
                        </select>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="col-md-6">
            <h5>New Work Items</h5>
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
    <button type="submit" class="btn btn-primary">Save Work Checklist</button>
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
