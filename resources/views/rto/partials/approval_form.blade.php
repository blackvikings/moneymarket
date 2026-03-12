<form action="{{ route(get_route_prefix() . 'rto.update-approval', $lead->id) }}" method="POST">
    @csrf
    <p>Has the RTO approved the work?</p>
    <button type="submit" class="btn btn-success">Mark as RTO Approved</button>
</form>
