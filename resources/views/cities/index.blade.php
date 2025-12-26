@extends('adminlte::page')

@section('title', 'Cities')

@section('content_header')
    <h1>Cities</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create city')
                        <a href="{{ route('cities.create') }}" class="btn btn-primary">Create City</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('cities.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                </div>
            </div>
            <br>
            <form action="{{ route('cities.index') }}" method="GET">
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
                        <a href="{{ route('cities.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cities as $city)
                        <tr>
                            <td>{{ $city->name }}</td>
                            <td>
                                @if ($city->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('edit city')
                                    <a href="{{ route('cities.edit', $city->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('cities.toggle-status', $city->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $city->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $city->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete city')
                                    <form action="{{ route('cities.destroy', $city->id) }}" method="POST" class="d-inline">
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
            {{ $cities->links() }}
        </div>
    </div>
@stop
