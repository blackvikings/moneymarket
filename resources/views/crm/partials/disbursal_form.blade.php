<form action="{{ route(get_route_prefix() . 'crm.store-disbursal', $lead->id) }}" method="POST" class="disbursal-form">
    @csrf
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Disbursal Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Disbursal Date <span class="text-danger">*</span></label>
                        <div class="input-group date" id="disbursal_date_picker_{{ $lead->id }}" data-target-input="nearest">
                            <input type="date" name="disbursal_date" class="form-control datetimepicker-input" data-target="#disbursal_date_picker_{{ $lead->id }}" value="{{ old('disbursal_date', $lead->disbursal_date ? $lead->disbursal_date->format('Y-m-d') : '') }}" required/>
                            <div class="input-group-append" data-target="#disbursal_date_picker_{{ $lead->id }}" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Finance Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" step="0.01" name="finance_amount" class="form-control" placeholder="0.00" value="{{ old('finance_amount', $lead->finance_amount) }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Disbursal Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₹</span>
                            </div>
                            <input type="number" step="0.01" name="disbursal_amount" id="disbursal_amount_{{ $lead->id }}" class="form-control disbursal-amount-input" placeholder="0.00" value="{{ old('disbursal_amount', $lead->disbursal_amount) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Disbursal & Close Lead</button>
    </div>
</form>

<script>
    // Ensure script runs after DOM is loaded and even if loaded via AJAX
    (function() {
        // Removed auto-calculation logic as the total field is removed
    })();
</script>
