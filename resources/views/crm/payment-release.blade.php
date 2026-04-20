@extends('adminlte::page')

@section('title', 'Payment Release')

@section('content_header')
    <h1>Payment Release</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Bank</th>
                        <th>Branch</th>
                        <th>Disbursal Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>{{ $lead->bank ? $lead->bank->name : '-' }}</td>
                            <td>{{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</td>
                            <td>{{ $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-' }}</td>
                            <td>
                                <span class="badge {{ $lead->payment_release_status === 'Done' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $lead->payment_release_status ?? 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route(get_route_prefix() . 'leads.full-details', $lead->id) }}" class="btn btn-secondary btn-sm icon-action-btn mr-1" title="Full Details" aria-label="Full Details">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                                <a href="{{ route(get_route_prefix() . 'payment-release.show', $lead->id) }}" class="btn btn-info btn-sm">
                                    Payment Release Details
                                </a>
                                @if($lead->payment_release_status === 'Done')
                                    <span class="text-success small">Completed</span>
                                @elseif($lead->payment_release_status === 'Pending' || is_null($lead->payment_release_status))
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal{{ $lead->id }}">
                                        Mark as Done
                                    </button>
                                @else
                                    <form action="{{ route(get_route_prefix() . 'payment-release.toggle-status', $lead->id) }}" method="POST" data-confirm-message="Mark this payment release as pending?">
                                        @csrf
                                        <input type="hidden" name="status" value="Pending">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            Mark as Pending
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Payment Modal -->
                        <div class="modal fade payment-release-modal" id="paymentModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Payment Release Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route(get_route_prefix() . 'payment-release.store-details', $lead->id) }}" method="POST" enctype="multipart/form-data" data-confirm-message="Save these payment release details?">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info py-2">
                                                Release amount is locked to disbursal amount: <strong>{{ number_format((float) $lead->disbursal_amount, 2) }}</strong>
                                            </div>
                                            <div class="form-group">
                                                <label>Payment Type <span class="text-danger">*</span></label>
                                                <select name="payment_type" class="form-control payment-type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="Cash" {{ session('payment_release_lead_id') == $lead->id && old('payment_type') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="Cheque" {{ session('payment_release_lead_id') == $lead->id && old('payment_type') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                    <option value="Online" {{ session('payment_release_lead_id') == $lead->id && old('payment_type') === 'Online' ? 'selected' : '' }}>UPI/QR</option>
                                                    <option value="Bank Transfer" {{ session('payment_release_lead_id') == $lead->id && old('payment_type') === 'Bank Transfer' ? 'selected' : '' }}>NEFT/RTGS</option>
                                                </select>
                                            </div>

                                            <div class="payment-type-fields">
                                                <div class="payment-type-section" data-type="Cash" style="display:none;">
                                                    <fieldset class="payment-type-fieldset" disabled>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="payment_amount" class="form-control"
                                                                    value="{{ number_format((float) $lead->disbursal_amount, 2, '.', '') }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_date') : '' }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </fieldset>
                                                </div>

                                                <div class="payment-type-section" data-type="Cheque" style="display:none;">
                                                    <fieldset class="payment-type-fieldset" disabled>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Cheque Number <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_cheque_number" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_cheque_number') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="payment_amount" class="form-control"
                                                                    value="{{ number_format((float) $lead->disbursal_amount, 2, '.', '') }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_date') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Upload Image</label>
                                                                <input type="file" name="payment_cheque_image" class="form-control-file">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </fieldset>
                                                </div>

                                                <div class="payment-type-section" data-type="Online" style="display:none;">
                                                    <fieldset class="payment-type-fieldset" disabled>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>UPI / QR Ref. <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_upi_id" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_upi_id') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="payment_amount" class="form-control"
                                                                    value="{{ number_format((float) $lead->disbursal_amount, 2, '.', '') }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_date') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Upload Image</label>
                                                                <input type="file" name="payment_online_payment_image" class="form-control-file">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </fieldset>
                                                </div>

                                                <div class="payment-type-section" data-type="Bank Transfer" style="display:none;">
                                                    <fieldset class="payment-type-fieldset" disabled>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="payment_amount" class="form-control"
                                                                    value="{{ number_format((float) $lead->disbursal_amount, 2, '.', '') }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Account Holder Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_account_holder_name" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_account_holder_name') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Account Number <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_account_number" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_account_number') : '' }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Bank Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_bank_name" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_bank_name') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>IFSC Code <span class="text-danger">*</span></label>
                                                                <input type="text" name="payment_ifsc_code" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_ifsc_code') : '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control"
                                                                    value="{{ session('payment_release_lead_id') == $lead->id ? old('payment_date') : '' }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Upload Image</label>
                                                        <input type="file" name="payment_bank_transfer_image" class="form-control-file">
                                                    </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="payment_remarks" class="form-control" rows="3">{{ session('payment_release_lead_id') == $lead->id ? old('payment_remarks') : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Save and Mark as Done</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop

@section('js')
    <script>
        function togglePaymentTypeFields($select) {
            const type = ($select.val() || '').trim();
            const $modal = $select.closest('.payment-release-modal');

            const $sections = $modal.find('.payment-type-section');
            // Disable all controls for inactive sections so only the active section submits values.
            $sections.hide().find('.payment-type-fieldset').prop('disabled', true);

            const $active = $sections.filter(`[data-type="${type}"]`);
            $active.show().find('.payment-type-fieldset').prop('disabled', false);
        }

        $(document).on('change', '.payment-type', function () {
            togglePaymentTypeFields($(this));
        });

        $(document).on('show.bs.modal', '.payment-release-modal', function () {
            const $select = $(this).find('.payment-type');
            if (!$select.val()) {
                $select.val('Cash');
            }
            togglePaymentTypeFields($select);
        });

        $(document).on('submit', 'form[data-confirm-message]', function(event) {
            const message = $(this).data('confirm-message');
            if (message && !window.confirm(message)) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return false;
            }
        });

        $(function () {
            const reopenLeadId = '{{ session('payment_release_lead_id') }}';
            if (reopenLeadId) {
                $('#paymentModal' + reopenLeadId).modal('show');
            }
        });
    </script>
@stop
