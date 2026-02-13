@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <h1>Permissions</h1>
@stop

@section('content')
    <style>
        .module-card {
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .module-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            font-weight: bold;
            color: #4e73df;
            text-transform: uppercase;
        }
        .permission-row:hover {
            background-color: #f8f9fa;
        }
    </style>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-6">
                    @can('create permission')
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i> Create Permission
                        </a>
                    @endcan
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.permissions.index') }}" method="GET" class="form-inline justify-content-end">
                        <div class="form-group mr-2">
                            <select name="module" class="form-control">
                                <option value="">All Modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <input type="text" name="name" class="form-control" placeholder="Search Name" value="{{ request('name') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary ml-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $groupedPermissions = $permissions->groupBy('module');
    @endphp

    <div class="row">
        @forelse ($groupedPermissions as $module => $perms)
            <div class="col-md-6">
                <div class="card module-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $module ?? 'Other' }}</span>
                        <span class="badge badge-secondary">{{ $perms->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach ($perms as $permission)
                                    <tr class="permission-row">
                                        <td class="pl-3 align-middle">{{ $permission->name }}</td>
                                        <td class="text-right pr-3">
                                            @can('edit permission')
                                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-xs btn-info" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No permissions found.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $permissions->links() }}
    </div>
@stop
