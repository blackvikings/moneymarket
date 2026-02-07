<div class="modal-header">
    <h5 class="modal-title">Firm Details: {{ $firm->name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p><strong>Firm Name:</strong> {{ $firm->name }}</p>
            <p><strong>Status:</strong> <span class="badge badge-{{ $firm->is_active ? 'success' : 'danger' }}">{{ $firm->is_active ? 'Active' : 'Blocked' }}</span></p>
            <p><strong>Address:</strong> {{ $firm->address_line }}, {{ $firm->city->name ?? '' }}, {{ $firm->state->name ?? '' }} - {{ $firm->pincode }}</p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{ route('admin.firms.edit', $firm->id) }}" class="btn btn-primary">Edit Firm</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
