@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <h1>Permissions</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @can('create permission')
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Create Permission</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td>
                                @can('edit permission')
                                    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
@stop
