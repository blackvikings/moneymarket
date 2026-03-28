@extends('adminlte::page')

@section('title', 'Payment Release')

@section('content_header')
    <h1>Payment Release</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
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
                                @if($lead->payment_release_status === 'Pending' || is_null($lead->payment_release_status))
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal{{ $lead->id }}">
                                        Mark as Done
                                    </button>
                                @else
                                    <form action="{{ route(get_route_prefix() . 'payment-release.toggle-status', $lead->id) }}" method="POST">
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
                        <div class="modal fade" id="paymentModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Payment Release Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route(get_route_prefix() . 'payment-release.store-details', $lead->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Payment Amount <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" name="payment_amount" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Payment Date <span class="text-danger">*</span></label>
                                                <input type="date" name="payment_date" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea name="payment_remarks" class="form-control" rows="3"></textarea>
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
