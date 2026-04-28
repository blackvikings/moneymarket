@extends('adminlte::page')

@section('title', 'Expiring Insurance Leads')

@section('content_header')
    <h1>Expiring Insurance Leads (Coming 30 Days)</h1>
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
                        <th>Type</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr class="table-warning">
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>{{ $lead->insurance_type ?? '-' }}</td>
                            <td>
                                {{ $lead->insurance_end_date->format('d-m-Y') }}
                            </td>
                            <td>
                                {{ round(max(0, now()->diffInDays($lead->insurance_end_date, false))) }} Days
                            </td>
                            <td>{{ $lead->currentCity->name ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-insurance-lead-btn" data-id="{{ $lead->id }}">View</button>
                                <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-warning btn-sm">Renew</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No expiring insurance leads found.</td>
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
