@extends('adminlte::page')

@section('title', 'Employees')

@section('content_header')
    <h1>Employees</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create employee')
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">Create Employee</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('employees.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                </div>
            </div>
            <br>
            <form action="{{ route('employees.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Blocked" {{ request('status') == 'Blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="Exited" {{ request('status') == 'Exited' ? 'selected' : '' }}>Exited</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->employee_type }}</td>
                            <td>
                                <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : ($employee->status == 'Blocked' ? 'danger' : 'warning') }}">
                                    {{ $employee->status }}
                                </span>
                            </td>
                            <td>
                                @if(!$employee->hasRole('admin'))
                                    @can('view employee')
                                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info">View</a>
                                    @endcan
                                    @can('edit employee')
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                        {{-- Block/Unblock Button --}}
                                        <form action="{{ route('employees.update-status', $employee->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if($employee->status == 'Active')
                                                <input type="hidden" name="status" value="Blocked">
                                                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure you want to block this employee?')">Block</button>
                                            @elseif($employee->status == 'Blocked')
                                                <input type="hidden" name="status" value="Active">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to unblock this employee?')">Unblock</button>
                                            @endif
                                        </form>
                                    @endcan
                                    @can('delete employee')
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $employees->links() }}
        </div>
    </div>
@stop
