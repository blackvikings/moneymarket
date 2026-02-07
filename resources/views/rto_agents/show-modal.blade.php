<div class="modal-header">
    <h5 class="modal-title">RTO Agent Details: {{ $rtoAgent->name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Agent Name:</strong> {{ $rtoAgent->name }}</p>
            <p><strong>Agent Number:</strong> {{ $rtoAgent->number ?? '-' }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> <span class="badge badge-{{ $rtoAgent->is_active ? 'success' : 'danger' }}">{{ $rtoAgent->is_active ? 'Active' : 'Blocked' }}</span></p>
        </div>
    </div>

    <hr>
    <h6><strong>Assigned Cities</strong></h6>
    @if($rtoAgent->cities->count() > 0)
        <div class="row">
            @foreach($rtoAgent->cities as $city)
                <div class="col-md-4 mb-2">
                    <span class="badge badge-info p-2 w-100">{{ $city->city_name }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No cities assigned.</p>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route('admin.rto-agents.edit', $rtoAgent->id) }}" class="btn btn-primary">Edit Agent</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
