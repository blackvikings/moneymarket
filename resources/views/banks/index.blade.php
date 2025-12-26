@extends('adminlte::page')

@section('title', 'Banks')

@section('content_header')
    <h1>Banks</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create bank')
                        <a href="{{ route('banks.create') }}" class="btn btn-primary">Create Bank</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('banks.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                </div>
            </div>
            <br>
            <form action="{{ route('banks.index') }}" method="GET">
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
                        <a href="{{ route('banks.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Branches</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($banks as $bank)
                        <tr>
                            <td>{{ $bank->name }}</td>
                            <td>
                                @foreach ($bank->branches as $branch)
                                    <span class="badge badge-info">{{ $branch->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($bank->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('edit bank')
                                    <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('banks.toggle-status', $bank->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $bank->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $bank->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete bank')
                                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" class="d-inline">
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
            {{ $banks->links() }}
        </div>
    </div>
@stop
