<form action="{{ route(get_route_prefix() . 'rto.update-approval', $lead->id) }}" method="POST" data-confirm-message="Mark this lead as RTO approved?">
    @csrf
    <p class="mb-3">Has the RTO approved the work?</p>
    <div class="d-flex flex-wrap" style="gap: 0.5rem;">
        <button type="submit" class="btn btn-success">Yes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
    </div>
</form>
