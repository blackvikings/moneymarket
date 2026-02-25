@extends('adminlte::page')

@section('title', 'Insurance Leads')

@section('content_header')
    <h1>Insurance Leads</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 text-right">
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
                            <td>
                                @if($lead->insurance_end_date)
                                    {{ $lead->insurance_end_date->format('d-m-Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $lead->currentCity->name ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-insurance-lead-btn" data-id="{{ $lead->id }}">View</button>
                                <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No insurance leads found.</td>
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
