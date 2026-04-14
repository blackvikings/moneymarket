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
                <a href="{{ route(get_route_prefix() . 'referral-persons.index') }}" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-user-friends"></i> View Referral Persons
                </a>
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
                        <th>Service Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Referral Person</th>
                        <th>Payment Date</th>
                        <th>Paid By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $referral->lead->customer_name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $serviceType = $referral->lead->lead_type ?? null;
                                    if ($serviceType === 'Loan' && !empty($referral->lead->loan_type)) {
                                        $serviceType = $referral->lead->loan_type;
                                    }
                                @endphp
                                {{ $serviceType ?? 'N/A' }}
                            </td>
                            <td>&#8377;{{ number_format($referral->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $referral->status == 'Paid' ? 'success' : 'warning' }}">
                                    {{ $referral->status }}
                                </span>
                            </td>
                            <td>{{ $referral->lead->lead_source_person_name ?? 'N/A' }}</td>
                            <td>{{ $referral->paid_at ? $referral->paid_at->format('d-m-Y') : '-' }}</td>
                            <td>{{ $referral->paidBy ? $referral->paidBy->name : '-' }}</td>
                            <td>
                                @if($referral->status == 'Pending')
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal{{ $referral->id }}">
                                        Mark as Paid
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No referral logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $referrals->appends(request()->query())->links() }}
        </div>
    </div>

    @foreach($referrals as $referral)
        @if($referral->status == 'Pending')
            <!-- Payment Modal -->
            <div class="modal fade" id="paymentModal{{ $referral->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Mark as Paid</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route(get_route_prefix() . 'referral-logs.markAsPaid', $referral->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Mode of Payment <span class="text-danger">*</span></label>
                                    <select name="payment_mode" class="form-control payment-mode-select" required>
                                        <option value="">Select Mode</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Online">Online</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                <div class="payment-details">
                                    <!-- Dynamic fields will be injected here by JS -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save Payment</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

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
        $('.payment-mode-select').change(function() {
            let mode = $(this).val();
            let detailsContainer = $(this).closest('.modal-body').find('.payment-details');
            detailsContainer.html(''); // Clear previous fields

            let fields = '';
            if (mode === 'Cash') {
                fields = `
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
                `;
            } else if (mode === 'Cheque') {
                fields = `
                    <div class="form-group">
                        <label>Cheque Number <span class="text-danger">*</span></label>
                        <input type="text" name="cheque_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Image</label>
                        <input type="file" name="cheque_image" class="form-control-file">
                    </div>
                `;
            } else if (mode === 'Online') {
                fields = `
                    <div class="form-group">
                        <label>UPI ID <span class="text-danger">*</span></label>
                        <input type="text" name="upi_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Image</label>
                        <input type="file" name="online_payment_image" class="form-control-file">
                    </div>
                `;
            } else if (mode === 'Bank Transfer') {
                fields = `
                    <div class="form-group">
                        <label>Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_holder_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" name="ifsc_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Image</label>
                        <input type="file" name="bank_transfer_image" class="form-control-file">
                    </div>
                `;
            }
            detailsContainer.html(fields);
        });

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
