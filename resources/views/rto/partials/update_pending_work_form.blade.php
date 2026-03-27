<form action="{{ route(get_route_prefix() . 'rto.update-work-checklist', $lead->id) }}" method="POST">
    @csrf
    @php
        $workItems = [
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
    <div class="row">
        @foreach($workItems as $field => $label)
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ $label }}</label>
                    <select name="{{ $field }}" class="form-control form-control-sm">
                        <option value="Pending" {{ $lead->$field == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $lead->$field == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $lead->$field == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
