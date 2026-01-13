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
                        <a href="{{ route('admin.banks.create') }}" class="btn btn-primary">Create Bank</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.banks.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route('admin.banks.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
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
                                @foreach($bank->branches as $branch)
                                    <span class="badge badge-secondary">{{ $branch->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge badge-{{ $bank->is_active ? 'success' : 'danger' }}">
                                    {{ $bank->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @can('edit bank')
                                    <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                @endcan
                                @can('delete bank')
                                    <form action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endcan
                                <form action="{{ route('admin.banks.toggle-status', $bank->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $bank->is_active ? 'warning' : 'success' }}">
                                        {{ $bank->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $banks->links() }}
        </div>
    </div>
@stop
