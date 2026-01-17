<form action="{{ route(get_route_prefix() . 'rto.update-vehicle-detail', $lead->id) }}" method="POST" class="rto-vehicle-detail-form" enctype="multipart/form-data">
    @csrf
    @php
        $rtoVehicleType = $lead->rto_vehicle_type;
        if (empty($rtoVehicleType)) {
            if ($lead->vehicle_purchase_type == 'New Purchase') {
                $rtoVehicleType = 'New Vehicle';
            } elseif (in_array($lead->vehicle_purchase_type, ['Repurchase', 'Refinance', 'Balance Transfer'])) {
                $rtoVehicleType = 'Old Vehicle';
            }
        }
    @endphp
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="rto_vehicle_type" class="form-control rto-vehicle-type-select" required>
                    <option value="">Select Type</option>
                    <option value="New Vehicle" {{ $rtoVehicleType == 'New Vehicle' ? 'selected' : '' }}>New Vehicle</option>
                    <option value="Old Vehicle" {{ $rtoVehicleType == 'Old Vehicle' ? 'selected' : '' }}>Old Vehicle</option>
                </select>
            </div>
        </div>
    </div>

    <div class="old-vehicle-details-section" style="{{ $rtoVehicleType == 'Old Vehicle' ? '' : 'display:none;' }}">
        <hr>
        <h5>Old Vehicle Details</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Old Financer</label>
                    <input type="text" name="old_financer" class="form-control" value="{{ $lead->old_financer }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group form-check" style="margin-top: 40px;">
                    <input type="checkbox" class="form-check-input" name="rto_loan_closed_status" {{ $lead->rto_loan_closed_status ? 'checked' : '' }}>
                    <label class="form-check-label">Loan Closed</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group form-check" style="margin-top: 40px;">
                    <input type="checkbox" class="form-check-input" name="rto_hp_termination_status" {{ $lead->rto_hp_termination_status ? 'checked' : '' }}>
                    <label class="form-check-label">HP Termination</label>
                </div>
            </div>
        </div>

        <h6>Closer Deposited</h6>
        <div class="closer-deposits-container">
            @foreach($lead->rtoCloserDeposits as $index => $deposit)
                <div class="row closer-deposit-row">
                    <div class="col-md-5"><div class="form-group"><input type="number" step="0.01" name="closer_deposits[{{$index}}][amount]" class="form-control" placeholder="Amount" value="{{$deposit->amount}}"></div></div>
                    <div class="col-md-5"><div class="form-group"><input type="date" name="closer_deposits[{{$index}}][date]" class="form-control" value="{{$deposit->date->format('Y-m-d')}}"></div></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-success add-closer-deposit">Add Deposit</button>
    </div>

    <hr>
    <h5>RTO Details</h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Select RTO Agent</label>
                <select name="rto_agent_id" class="form-control">
                    <option value="">Select Agent</option>
                    @foreach($rtoAgents as $agent)
                        <option value="{{ $agent->id }}" {{ $lead->rto_agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <h6>Challan For</h6>
    <div class="challans-container">
        @foreach($lead->rtoChallans as $index => $challan)
            <div class="row challan-row">
                <div class="col-md-4"><div class="form-group"><input type="text" name="challans[{{$index}}][type]" class="form-control" placeholder="Challan Type" value="{{$challan->challan_type}}"></div></div>
                <div class="col-md-3"><div class="form-group"><input type="date" name="challans[{{$index}}][date]" class="form-control" value="{{$challan->date->format('Y-m-d')}}"></div></div>
                <div class="col-md-3"><div class="form-group"><input type="number" step="0.01" name="challans[{{$index}}][amount]" class="form-control" placeholder="Amount" value="{{$challan->amount}}"></div></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-success add-challan">Add Challan</button>

    <hr>
    <div class="row">
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_challan_verified" class="form-check-input" {{ $lead->rto_challan_verified ? 'checked' : '' }}><label class="form-check-label">Challan Verify</label></div></div>
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_verification_status" class="form-check-input" {{ $lead->rto_verification_status ? 'checked' : '' }}><label class="form-check-label">Verification</label></div></div>
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_approval_status" class="form-check-input" {{ $lead->rto_approval_status ? 'checked' : '' }}><label class="form-check-label">Approval</label></div></div>
    </div>

    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Upload Approved RTO Document</label>
                <input type="file" name="rto_approved_doc" class="form-control-file">
                @if($lead->rto_approved_doc_path)
                    <small><a href="{{ asset('storage/' . $lead->rto_approved_doc_path) }}" target="_blank">View Current Document</a></small>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Save Vehicle Details</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success mt-3">Save & Next</button>
</form>
