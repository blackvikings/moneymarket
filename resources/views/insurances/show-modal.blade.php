<div class="modal-header">
    <h5 class="modal-title">Insurance Details: {{ $insurance->company_name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Company Name:</strong> {{ $insurance->company_name }}</p>
            <p><strong>Agent Name:</strong> {{ $insurance->agent_name }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> <span class="badge badge-{{ $insurance->is_active ? 'success' : 'danger' }}">{{ $insurance->is_active ? 'Active' : 'Blocked' }}</span></p>
            <p><strong>Address:</strong> {{ $insurance->address_line }}, {{ $insurance->city->name ?? '' }}, {{ $insurance->state->name ?? '' }} - {{ $insurance->pincode }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>Insurance Types & Sub Types</strong></h6>
    @if($insurance->types->count() > 0)
        @foreach($insurance->types as $type)
            <div class="card mb-2">
                <div class="card-body py-2">
                    <strong>{{ $type->name }}</strong>
                    <div class="mt-1">
                        @foreach($type->subTypes as $subType)
                            <span class="badge badge-secondary">{{ $subType->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No types added.</p>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix(). 'insurances.edit', $insurance->id) }}" class="btn btn-primary">Edit Insurance</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
