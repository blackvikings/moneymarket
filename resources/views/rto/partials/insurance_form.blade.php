<form action="{{ route(get_route_prefix(). 'rto.update-insurance-details', $lead->id) }}" method="POST" class="rto-insurance-form" data-confirm-message="Save these insurance details?">
    @csrf
    <h5>Vehicle Details</h5>
    <p><strong>Model:</strong> {{ $lead->vehicleName->name ?? 'N/A' }} | <strong>RC Number:</strong> {{ $lead->rc_number ?? 'N/A' }}</p>

    <hr>
    <h5>Insurance Details</h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Select Insurance Company</label>
                <select name="insurance_id" class="form-control" required>
                    <option value="">Select Company</option>
                    @foreach($insurances as $insurance)
                        <option value="{{ $insurance->id }}" {{ $lead->insurance_id == $insurance->id ? 'selected' : '' }}>{{ $insurance->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Insurance Start Date</label>
                <input type="date" name="insurance_start_date" class="form-control" value="{{ $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '' }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Insurance End Date</label>
                <input type="date" name="insurance_end_date" class="form-control" value="{{ $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '' }}" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Type Of Insurance</label>
        @php
            $insuranceTypes = ['Third Party Insurance', 'Commercial Vehicle Insurance', 'Comprehensive Car Insurance', 'Private Car Insurance', 'Personal Accident Cover', 'Zero Depreciation Cover', 'Roadside Assistance'];
            $selectedTypes = explode(', ', $lead->insurance_type_of_insurance);
        @endphp
        <div class="row">
            @foreach($insuranceTypes as $type)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="insurance_type_of_insurance[]" value="{{ $type }}" {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $type }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Premium Amount</label>
                <input type="number" step="0.01" name="insurance_premium_amount" class="form-control" value="{{ $lead->insurance_premium_amount }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>IDV (Insured Declared Value)</label>
                <input type="number" step="0.01" name="insurance_idv_amount" class="form-control" value="{{ $lead->insurance_idv_amount }}" required>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Save Insurance Details</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success mt-3">Save & Next</button>
</form>
