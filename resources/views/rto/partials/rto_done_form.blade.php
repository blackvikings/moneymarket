<form action="{{ route(get_route_prefix() . 'rto.update-rto-done', $lead->id) }}" method="POST" enctype="multipart/form-data" data-confirm-message="Submit this RTO final update?">
    @csrf
    <div class="form-group">
        <label>Upload Approved RTO Document <span class="text-danger">*</span> <small class="text-muted">(Max 5MB, JPG, PNG, PDF)</small></label>
        <input type="file" name="rto_approved_doc" class="form-control-file" required>
        @if($lead->rto_approved_doc_path)
            <small><a href="{{ asset('storage/' . $lead->rto_approved_doc_path) }}" target="_blank">View Current Document</a></small>
        @endif
    </div>
    <div class="form-group">
        <label>Remark</label>
        <textarea name="remark" class="form-control" rows="3" placeholder="Enter remark...">{{ old('remark', $lead->remark) }}</textarea>
    </div>
    <p class="text-muted">Choose one action for this lead.</p>

    <button type="submit" name="action_type" value="mark_rto_done" class="btn btn-primary mr-2">Mark RTO Done</button>
    <button type="submit" name="action_type" value="move_to_insurance" class="btn btn-success">Move to Insurance</button>
</form>
