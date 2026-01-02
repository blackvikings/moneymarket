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
                    <a href="{{ route('admin.firms.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
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
                            <td>{{ $firm->address }}</td>
                            <td>
                                @if ($firm->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('edit firm')
                                    <a href="{{ route('admin.firms.edit', $firm->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.firms.toggle-status', $firm->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $firm->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $firm->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete firm')
                                    <form action="{{ route('admin.firms.destroy', $firm->id) }}" method="POST" class="d-inline">
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
            {{ $firms->links() }}
        </div>
    </div>
@stop
