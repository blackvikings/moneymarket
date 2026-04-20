<form action="{{ route(get_route_prefix() . 'rto.upload-doc', $lead->id) }}" method="POST" enctype="multipart/form-data" data-confirm-message="Proceed to verification?">
    @csrf
    <div class="form-group">
        <label>Upload RTO Document (Optional)</label>
        <input type="file" name="rto_doc" class="form-control-file">
        @if(!empty($lead->rto_workflow_doc_path))
            <small class="d-block mt-1">
                <a href="{{ asset('storage/' . $lead->rto_workflow_doc_path) }}" target="_blank">View Current Document</a>
            </small>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">Proceed to Verification</button>
</form>
