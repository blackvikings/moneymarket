@extends('adminlte::page')

@section('title', 'Insurances')

@section('content_header')
    <h1>Insurances</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create insurance')
                        <a href="{{ route(get_route_prefix(). 'insurances.create') }}" class="btn btn-primary">Create Insurance</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->can('export insurance'))
                        <a href="{{ route(get_route_prefix(). 'insurances.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route(get_route_prefix(). 'insurances.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="company_name" class="form-control" placeholder="Search by Company Name" value="{{ request('company_name') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="vehicle_type" class="form-control">
                            <option value="">All Vehicle Types</option>
                            <option value="2 Wheeler" {{ request('vehicle_type') == '2 Wheeler' ? 'selected' : '' }}>2 Wheeler</option>
                            <option value="3 Wheeler" {{ request('vehicle_type') == '3 Wheeler' ? 'selected' : '' }}>3 Wheeler</option>
                            <option value="4 Wheeler" {{ request('vehicle_type') == '4 Wheeler' ? 'selected' : '' }}>4 Wheeler</option>
                            <option value="Commercial" {{ request('vehicle_type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route(get_route_prefix(). 'insurances.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Agent Name</th>
                        <th>Vehicle Type</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insurances as $insurance)
                        <tr>
                            <td>{{ $insurance->company_name }}</td>
                            <td>{{ $insurance->agent_name }}</td>
                            <td>{{ $insurance->vehicle_type }}</td>
                            <td>
                                @if($insurance->address_line)
                                    {{ $insurance->address_line }},<br>
                                    {{ $insurance->city->name ?? '' }}, {{ $insurance->state->name ?? '' }} - {{ $insurance->pincode }}
                                @else
                                    {{ $insurance->address }}
                                @endif
                            </td>
                            <td>
                                @if ($insurance->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('view insurance')
                                        <button type="button" class="btn btn-sm btn-info view-insurance-btn mr-1" data-id="{{ $insurance->id }}">View</button>
                                    @endcan
                                    @can('edit insurance')
                                        <a href="{{ route(get_route_prefix(). 'insurances.edit', $insurance->id) }}" class="btn btn-sm btn-primary mr-1">Edit</a>
                                        <form action="{{ route(get_route_prefix(). 'insurances.toggle-status', $insurance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $insurance->is_active ? 'btn-warning' : 'btn-success' }}">
                                                {{ $insurance->is_active ? 'Block' : 'Unblock' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $insurances->links() }}
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
            $('.view-insurance-btn').click(function() {
                let insuranceId = $(this).data('id');
                let url = '{{ route(get_route_prefix() . "insurances.show", ":id") }}'.replace(':id', insuranceId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load insurance details.</div>');
                    }
                });
            });
        });
    </script>
@stop
