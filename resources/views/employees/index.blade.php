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
                        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">Create Employee</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->can('export employee'))
                        <a href="{{ route('admin.employees.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route('admin.employees.index') }}" method="GET">
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
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Reset</a>
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
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Joined Firm</th>
                        <th>Joining Date</th>
                        <th>Contact Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->roles->first()->name ?? 'N/A' }}</td>
                            <td>{{ $employee->firm->name ?? 'N/A' }}</td>
                            <td>{{ $employee->created_at ? $employee->created_at->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $employee->contact_number ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : ($employee->status == 'Blocked' ? 'danger' : 'warning') }}">
                                    {{ $employee->status }}
                                </span>
                            </td>
                            <td>
                                @if(!$employee->hasRole('admin'))
                                    <div>
                                        @can('view employee')
                                            <button type="button" class="btn btn-sm btn-info view-employee-btn mr-1" data-id="{{ $employee->id }}">View</button>
                                        @endcan
                                        @can('edit employee')
                                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-primary mr-1">Edit</a>

                                            {{-- Block/Unblock Button --}}
                                            @if($employee->status == 'Active')
                                                <form action="{{ route('admin.employees.update-status', $employee->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="Blocked">
                                                    <button type="submit" class="btn btn-sm btn-warning mr-1" onclick="return confirm('Are you sure you want to block this employee?')">Block</button>
                                                </form>
                                            @elseif($employee->status == 'Blocked')
                                                <form action="{{ route('admin.employees.update-status', $employee->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="Active">
                                                    <button type="submit" class="btn btn-sm btn-success mr-1" onclick="return confirm('Are you sure you want to unblock this employee?')">Unblock</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
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
            $('.view-employee-btn').click(function() {
                let employeeId = $(this).data('id');
                let url = '{{ route("admin.employees.show", ":id") }}'.replace(':id', employeeId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load employee details.</div>');
                    }
                });
            });
        });
    </script>
@stop
