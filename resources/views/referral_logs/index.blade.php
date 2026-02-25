@extends('adminlte::page')

@section('title', 'Referral Logs')

@section('content_header')
    <h1>Referral Logs</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Referral Payments</h3>
            <div class="card-tools">
                <form action="{{ route(get_route_prefix() . 'referral-logs.index') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="status" class="mr-2">Status:</label>
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
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
                            <td>{{ $referral->lead->customer_name }}</td>
                            <td>₹{{ number_format($referral->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $referral->status == 'Paid' ? 'success' : 'warning' }}">
                                    {{ $referral->status }}
                                </span>
                            </td>
                            <td>{{ $referral->lead->lead_source_person_name }}</td>
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
@stop
