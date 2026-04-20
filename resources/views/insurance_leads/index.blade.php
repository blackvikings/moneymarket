@extends('adminlte::page')

@section('title', 'Insurance Leads')

@section('content_header')
    <h1>Insurance Leads</h1>
@stop

@section('content')
    <style>
        .icon-action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
    </style>
    <div class="row mb-3">
        <div class="col-md-8">
            <form action="{{ route(get_route_prefix() . 'insurance-leads.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <select name="premium_status" class="form-control">
                        <option value="">All Premium Statuses</option>
                        <option value="Yes" {{ request('premium_status') == 'Yes' ? 'selected' : '' }}>Collected</option>
                        <option value="No" {{ request('premium_status') == 'No' ? 'selected' : '' }}>Not Collected</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="col-md-4 text-right">
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('export insurance lead'))
                <a href="{{ route(get_route_prefix() . 'insurance-leads.export', request()->all()) }}" class="btn btn-success mr-1">Export</a>
            @endif
            <a href="{{ route(get_route_prefix() . 'insurance-leads.create') }}" class="btn btn-primary">Add New Insurance Lead</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Premium Status</th>
                        <th>Premium Amount</th>
                        <th>Collected Date</th>
                        <th>Expiry Date</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>{{ $lead->insurance_type ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $lead->insurance_lead_status == 'Done' ? 'success' : 'warning' }}">
                                    {{ $lead->insurance_lead_status ?? 'In Progress' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $lead->premium_amount_collected == 'Yes' ? 'success' : 'danger' }}">
                                    {{ $lead->premium_amount_collected == 'Yes' ? 'Collected' : 'Not Collected' }}
                                </span>
                            </td>
                            <td>{{ $lead->premium_payment_amount !== null ? number_format($lead->premium_payment_amount, 2) : '-' }}</td>
                            <td>{{ $lead->premium_payment_date ? $lead->premium_payment_date->format('d-m-Y') : '-' }}</td>
                            <td>
                                @if($lead->insurance_end_date)
                                    {{ $lead->insurance_end_date->format('d-m-Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $lead->currentCity->name ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm icon-action-btn view-insurance-lead-btn" data-id="{{ $lead->id }}" title="View Lead" aria-label="View Lead"><i class="fas fa-eye"></i></button>
                                <a href="{{ route(get_route_prefix() . 'leads.full-details', $lead->id) }}" class="btn btn-secondary btn-sm icon-action-btn" title="Full Details" aria-label="Full Details"><i class="fas fa-file-alt"></i></a>
                                <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-warning btn-sm icon-action-btn" title="Edit Lead" aria-label="Edit Lead"><i class="fas fa-pen"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No insurance leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Global View Modal -->
    <div class="modal fade" id="globalViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="globalViewModalContent">
                {{-- Content will be loaded via AJAX --}}
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.view-insurance-lead-btn').click(function() {
                let leadId = $(this).data('id');
                let url = '{{ route(get_route_prefix() . "insurance-leads.show", ":id") }}'.replace(':id', leadId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load lead details.</div>');
                    }
                });
            });
        });
    </script>
@stop
