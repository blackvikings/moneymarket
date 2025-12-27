<form action="{{ route('rto.update-vehicle-detail', $lead->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="rto_vehicle_type" id="rto_vehicle_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="New Vehicle" {{ $lead->rto_vehicle_type == 'New Vehicle' ? 'selected' : '' }}>New Vehicle</option>
                    <option value="Old Vehicle" {{ $lead->rto_vehicle_type == 'Old Vehicle' ? 'selected' : '' }}>Old Vehicle</option>
                </select>
            </div>
        </div>
    </div>

    <div id="old_vehicle_details" style="{{ $lead->rto_vehicle_type == 'Old Vehicle' ? '' : 'display:none;' }}">
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
                    <input type="checkbox" class="form-check-input" name="rto_loan_closed_status" id="rto_loan_closed_status" {{ $lead->rto_loan_closed_status ? 'checked' : '' }}>
                    <label class="form-check-label" for="rto_loan_closed_status">Loan Closed</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group form-check" style="margin-top: 40px;">
                    <input type="checkbox" class="form-check-input" name="rto_hp_termination_status" id="rto_hp_termination_status" {{ $lead->rto_hp_termination_status ? 'checked' : '' }}>
                    <label class="form-check-label" for="rto_hp_termination_status">HP Termination</label>
                </div>
            </div>
        </div>

        <h6>Closer Deposited</h6>
        <div id="closer-deposits-container">
            @foreach($lead->rtoCloserDeposits as $index => $deposit)
                <div class="row closer-deposit-row">
                    <div class="col-md-5"><div class="form-group"><input type="number" step="0.01" name="closer_deposits[{{$index}}][amount]" class="form-control" placeholder="Amount" value="{{$deposit->amount}}"></div></div>
                    <div class="col-md-5"><div class="form-group"><input type="date" name="closer_deposits[{{$index}}][date]" class="form-control" value="{{$deposit->date->format('Y-m-d')}}"></div></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-success" id="add-closer-deposit">Add Deposit</button>
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
    <div id="challans-container">
        @foreach($lead->rtoChallans as $index => $challan)
            <div class="row challan-row">
                <div class="col-md-4"><div class="form-group"><input type="text" name="challans[{{$index}}][type]" class="form-control" placeholder="Challan Type" value="{{$challan->challan_type}}"></div></div>
                <div class="col-md-3"><div class="form-group"><input type="date" name="challans[{{$index}}][date]" class="form-control" value="{{$challan->date->format('Y-m-d')}}"></div></div>
                <div class="col-md-3"><div class="form-group"><input type="number" step="0.01" name="challans[{{$index}}][amount]" class="form-control" placeholder="Amount" value="{{$challan->amount}}"></div></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-success" id="add-challan">Add Challan</button>

    <hr>
    <div class="row">
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_challan_verified" class="form-check-input" {{ $lead->rto_challan_verified ? 'checked' : '' }}><label class="form-check-label">Challan Verify</label></div></div>
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_verification_status" class="form-check-input" {{ $lead->rto_verification_status ? 'checked' : '' }}><label class="form-check-label">Verification</label></div></div>
        <div class="col-md-4"><div class="form-group form-check"><input type="checkbox" name="rto_approval_status" class="form-check-input" {{ $lead->rto_approval_status ? 'checked' : '' }}><label class="form-check-label">Approval</label></div></div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Save Vehicle Details</button>
</form>

@section('js')
    @parent
    <script>
        $(document).ready(function() {
            $('#rto_vehicle_type').change(function() {
                if ($(this).val() === 'Old Vehicle') {
                    $('#old_vehicle_details').show();
                } else {
                    $('#old_vehicle_details').hide();
                }
            });

            let depositIndex = {{ $lead->rtoCloserDeposits->count() }};
            $('#add-closer-deposit').click(function() {
                let html = `
                    <div class="row closer-deposit-row">
                        <div class="col-md-5"><div class="form-group"><input type="number" step="0.01" name="closer_deposits[${depositIndex}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-5"><div class="form-group"><input type="date" name="closer_deposits[${depositIndex}][date]" class="form-control"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                $('#closer-deposits-container').append(html);
                depositIndex++;
            });

            let challanIndex = {{ $lead->rtoChallans->count() }};
            $('#add-challan').click(function() {
                let html = `
                    <div class="row challan-row">
                        <div class="col-md-4"><div class="form-group"><input type="text" name="challans[${challanIndex}][type]" class="form-control" placeholder="Challan Type"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="date" name="challans[${challanIndex}][date]" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="number" step="0.01" name="challans[${challanIndex}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                $('#challans-container').append(html);
                challanIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('.row').remove();
            });
        });
    </script>
@stop
