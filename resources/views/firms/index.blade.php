@extends('adminlte::page')

@section('title', 'Firms')

@section('content_header')
    <h1>Firms</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create firm')
                        <a href="{{ route('admin.firms.create') }}" class="btn btn-primary">Create Firm</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->can('export firm'))
                        <a href="{{ route('admin.firms.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route('admin.firms.index') }}" method="GET">
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
                        <a href="{{ route('admin.firms.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($firms as $firm)
                        <tr>
                            <td>{{ $firm->name }}</td>
                            <td>
                                @if($firm->address_line)
                                    {{ $firm->address_line }},<br>
                                    {{ $firm->city->name ?? '' }}, {{ $firm->state->name ?? '' }} - {{ $firm->pincode }}
                                @else
                                    {{ $firm->address }}
                                @endif
                            </td>
                            <td>
                                @if ($firm->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('view firm')
                                        <button type="button" class="btn btn-sm btn-info view-firm-btn mr-1" data-id="{{ $firm->id }}">View</button>
                                    @endcan
                                    @can('edit firm')
                                        <a href="{{ route('admin.firms.edit', $firm->id) }}" class="btn btn-sm btn-primary mr-1">Edit</a>
                                        <form action="{{ route('admin.firms.toggle-status', $firm->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $firm->is_active ? 'btn-warning' : 'btn-success' }}">
                                                {{ $firm->is_active ? 'Block' : 'Unblock' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $firms->links() }}
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
            $('.view-firm-btn').click(function() {
                let firmId = $(this).data('id');
                let url = '{{ route("admin.firms.show", ":id") }}'.replace(':id', firmId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load firm details.</div>');
                    }
                });
            });
        });
    </script>
@stop
