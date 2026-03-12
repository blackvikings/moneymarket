<form action="{{ route(get_route_prefix() . 'rto.update-verification', $lead->id) }}" method="POST">
    @csrf
    <p>Confirm if document verification is complete at RTO.</p>
    <button type="submit" class="btn btn-success">Mark Verification as Done</button>
</form>
