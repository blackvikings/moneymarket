<form action="{{ route(get_route_prefix() . 'rto.update-challan', $lead->id) }}" method="POST" data-confirm-message="Mark challan as done?">
    @csrf
    <p>Has the challan been generated and paid?</p>
    <button type="submit" class="btn btn-success">Mark Challan as Done</button>
</form>
