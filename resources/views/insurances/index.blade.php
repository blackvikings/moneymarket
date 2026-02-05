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
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route(get_route_prefix(). 'insurances.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route(get_route_prefix(). 'insurances.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="company_name" class="form-control" placeholder="Search by Company Name" value="{{ request('company_name') }}">
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
                                @can('edit insurance')
                                    <a href="{{ route(get_route_prefix(). 'insurances.edit', $insurance->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route(get_route_prefix(). 'insurances.toggle-status', $insurance->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $insurance->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $insurance->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete insurance')
                                    <form action="{{ route(get_route_prefix(). 'insurances.destroy', $insurance->id) }}" method="POST" class="d-inline">
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
            {{ $insurances->links() }}
        </div>
    </div>
@stop
