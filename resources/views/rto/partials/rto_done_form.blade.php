<form action="{{ route(get_route_prefix() . 'rto.update-rto-done', $lead->id) }}" method="POST" enctype="multipart/form-data">
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
        <textarea name="remark" class="form-control" rows="3" placeholder="Enter any remarks..."></textarea>
    </div>
    <p class="text-muted">Uploading this document will mark the RTO process as complete and move the lead to the Insurance Module.</p>
    <button type="submit" class="btn btn-success">Upload & Move to Insurance</button>
</form>
