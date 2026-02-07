@extends('adminlte::page')

@section('title', 'RTO Agents')

@section('content_header')
    <h1>RTO Agents</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create rto agent')
                        <a href="{{ route('admin.rto-agents.create') }}" class="btn btn-primary">Create RTO Agent</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.rto-agents.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route('admin.rto-agents.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.rto-agents.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Cities</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agents as $agent)
                        <tr>
                            <td>{{ $agent->name }}</td>
                            <td>{{ $agent->number }}</td>
                            <td>
                                @foreach ($agent->cities as $city)
                                    <span class="badge badge-info">{{ $city->city_name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($agent->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('view rto agent')
                                    <button type="button" class="btn btn-sm btn-info view-agent-btn" data-id="{{ $agent->id }}">View</button>
                                @endcan
                                @can('edit rto agent')
                                    <a href="{{ route('admin.rto-agents.edit', $agent->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.rto-agents.toggle-status', $agent->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $agent->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $agent->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $agents->links() }}
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
            $('.view-agent-btn').click(function() {
                let agentId = $(this).data('id');
                let url = '{{ route("admin.rto-agents.show", ":id") }}'.replace(':id', agentId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load agent details.</div>');
                    }
                });
            });
        });
    </script>
@stop
