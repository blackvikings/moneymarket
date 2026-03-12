<form action="{{ route(get_route_prefix() . 'rto.upload-doc', $lead->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Upload RTO Document</label>
        <input type="file" name="rto_doc" class="form-control-file" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload & Proceed</button>
</form>
