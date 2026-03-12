<form action="{{ route(get_route_prefix() . 'rto.update-rto-done', $lead->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Upload Final Document (Approved/Completed)</label>
        <input type="file" name="final_doc" class="form-control-file" required>
    </div>
    <button type="submit" class="btn btn-success btn-block">Complete RTO & Move to Insurance</button>
</form>
