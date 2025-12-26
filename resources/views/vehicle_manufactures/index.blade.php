@extends('adminlte::page')

@section('title', 'Vehicle Manufactures')

@section('content_header')
    <h1>Vehicle Manufactures</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create vehicle manufacture')
                        <a href="{{ route('vehicle-manufactures.create') }}" class="btn btn-primary">Create Vehicle Manufacture</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('vehicle-manufactures.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                </div>
            </div>
            <br>
            <form action="{{ route('vehicle-manufactures.index') }}" method="GET">
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
                        <a href="{{ route('vehicle-manufactures.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Manufacture Name</th>
                        <th>Vehicle Names</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manufactures as $manufacture)
                        <tr>
                            <td>{{ $manufacture->name }}</td>
                            <td>
                                @foreach ($manufacture->vehicleNames as $vehicleName)
                                    <span class="badge badge-info">{{ $vehicleName->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($manufacture->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('edit vehicle manufacture')
                                    <a href="{{ route('vehicle-manufactures.edit', $manufacture->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('vehicle-manufactures.toggle-status', $manufacture->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $manufacture->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $manufacture->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete vehicle manufacture')
                                    <form action="{{ route('vehicle-manufactures.destroy', $manufacture->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $manufactures->links() }}
        </div>
    </div>
@stop
