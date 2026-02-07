<div class="modal-header">
    <h5 class="modal-title">Bank Details: {{ $bank->name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p><strong>Bank Name:</strong> {{ $bank->name }}</p>
            <p><strong>Status:</strong> <span class="badge badge-{{ $bank->is_active ? 'success' : 'danger' }}">{{ $bank->is_active ? 'Active' : 'Blocked' }}</span></p>
            <p><strong>Address:</strong> {{ $bank->address_line }}, {{ $bank->city->name ?? '' }}, {{ $bank->state->name ?? '' }} - {{ $bank->pincode }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>Branches</strong></h6>
    @if($bank->branches->count() > 0)
        <div class="row">
            @foreach($bank->branches as $branch)
                <div class="col-md-4 mb-2">
                    <span class="badge badge-info p-2 w-100">{{ $branch->name }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No branches added.</p>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-primary">Edit Bank</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
