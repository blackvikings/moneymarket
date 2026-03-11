@extends('adminlte::page')

@section('title', 'Referral Logs')

@section('content_header')
    <h1>Referral Logs</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Referral Payments</h3>
            <div class="card-tools d-flex align-items-center">
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addReferralPersonModal">
                    <i class="fas fa-plus"></i> Add New Referral Person
                </button>
                <a href="{{ route(get_route_prefix() . 'referral-logs.export', request()->query()) }}" class="btn btn-success btn-sm mr-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <form action="{{ route(get_route_prefix() . 'referral-logs.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <select name="referral_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="Lead" {{ request('referral_type') == 'Lead' ? 'selected' : '' }}>Lead</option>
                            <option value="Employee" {{ request('referral_type') == 'Employee' ? 'selected' : '' }}>Employee</option>
                            <option value="Referral" {{ request('referral_type') == 'Referral' ? 'selected' : '' }}>Referral Person</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <input type="text" name="referral_person" class="form-control form-control-sm" placeholder="Referral Person Name" value="{{ request('referral_person') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lead Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Referral Person</th>
                        <th>Paid At</th>
                        <th>Paid By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $referral->lead->customer_name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($referral->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $referral->status == 'Paid' ? 'success' : 'warning' }}">
                                    {{ $referral->status }}
                                </span>
                            </td>
                            <td>{{ $referral->lead->lead_source_person_name ?? 'N/A' }}</td>
                            <td>{{ $referral->paid_at ? $referral->paid_at->format('d-m-Y H:i A') : '-' }}</td>
                            <td>{{ $referral->paidBy ? $referral->paidBy->name : '-' }}</td>
                            <td>
                                @if($referral->status == 'Pending')
                                    <form action="{{ route(get_route_prefix() . 'referral-logs.markAsPaid', $referral->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this as paid?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">Mark as Paid</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No referral logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $referrals->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Add Referral Person Modal -->
    <div class="modal fade" id="addReferralPersonModal" tabindex="-1" role="dialog" aria-labelledby="addReferralPersonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReferralPersonModalLabel">Add New Referral Person</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addReferralPersonForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Person</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // --- Add Referral Person AJAX ---
        $('#addReferralPersonForm').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: "{{ route('lead-source-persons.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addReferralPersonModal').modal('hide');
                        $('#addReferralPersonForm')[0].reset();
                        alert(response.message);
                        // Optional: Reload page to see if needed, though this page lists logs, not persons directly.
                        // But user wanted the ability to add person from here.
                    }
                },
                error: function(xhr) {
                    alert('Error adding person. Please check inputs.');
                }
            });
        });
    });
</script>
@stop
