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
                        <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.create') }}" class="btn btn-primary">Create Vehicle Manufacture</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.index') }}" method="GET">
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
                        <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Manufacture Name</th>
                        <th>Vehicle Name</th>
                        <th>Variants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manufactures as $manufacture)
                        @php
                            $groupedVehicles = $manufacture->vehicleNames->groupBy('name');
                        @endphp
                        @foreach ($groupedVehicles as $vehicleName => $items)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $groupedVehicles->count() }}">{{ $manufacture->name }}</td>
                                @endif
                                <td>{{ $vehicleName }}</td>
                                <td>
                                    @php
                                        $variants = $items->pluck('variant')->filter()->toArray();
                                    @endphp
                                    @foreach($variants as $variant)
                                        <span class="badge badge-info">{{ $variant }}</span>
                                    @endforeach
                                    @if(empty($variants))
                                        <span class="text-muted">No variants</span>
                                    @endif
                                </td>
                                @if ($loop->first)
                                    <td rowspan="{{ $groupedVehicles->count() }}">
                                        @if ($manufacture->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Blocked</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $groupedVehicles->count() }}">
                                        @can('edit vehicle manufacture')
                                            <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.edit', $manufacture->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.toggle-status', $manufacture->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $manufacture->is_active ? 'btn-warning' : 'btn-success' }}">
                                                    {{ $manufacture->is_active ? 'Block' : 'Unblock' }}
                                                </button>
                                            </form>
                                        @endcan
                                        @can('delete vehicle manufacture')
                                            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.destroy', $manufacture->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        @if($groupedVehicles->isEmpty())
                            <tr>
                                <td>{{ $manufacture->name }}</td>
                                <td colspan="2" class="text-center text-muted italic">No vehicles added</td>
                                <td>
                                    @if ($manufacture->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Blocked</span>
                                    @endif
                                </td>
                                <td>
                                    @can('edit vehicle manufacture')
                                        <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.edit', $manufacture->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.toggle-status', $manufacture->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $manufacture->is_active ? 'btn-warning' : 'btn-success' }}">
                                                {{ $manufacture->is_active ? 'Block' : 'Unblock' }}
                                            </button>
                                        </form>
                                    @endcan
                                    @can('delete vehicle manufacture')
                                        <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.destroy', $manufacture->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            {{ $manufactures->links() }}
        </div>
    </div>
@stop
