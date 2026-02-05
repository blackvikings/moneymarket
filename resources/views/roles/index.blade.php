@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Roles</h1>
        @can('create role')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Create New Role
            </a>
        @endcan
    </div>
@stop

@section('content')
    <div class="row">
        @forelse ($roles as $role)
            <div class="col-md-4">
                <div class="card card-widget widget-user-2 shadow-sm">
                    <div class="widget-user-header bg-info">
                        <div class="widget-user-image">
                            <i class="fas fa-user-tag fa-3x text-white-50"></i>
                        </div>
                        <h3 class="widget-user-username">{{ $role->name }}</h3>
                        <h5 class="widget-user-desc">{{ $role->permissions->count() }} Permissions</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Permissions:</p>
                        <div class="d-flex flex-wrap">
                            @if($role->permissions->count() > 0)
                                @foreach ($role->permissions as $permission)
                                    <span class="badge badge-light m-1">{{ $permission->name }}</span>
                                @endforeach
                            @else
                                <span class="font-italic">No permissions assigned.</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        @can('edit role')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-default" title="Edit Role">
                                <i class="fas fa-edit text-primary"></i> Edit
                            </a>
                        @endcan
{{--                        @can('delete role')--}}
{{--                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">--}}
{{--                                @csrf--}}
{{--                                @method('DELETE')--}}
{{--                                <button type="submit" class="btn btn-sm btn-default" title="Delete Role">--}}
{{--                                    <i class="fas fa-trash text-danger"></i> Delete--}}
{{--                                </button>--}}
{{--                            </form>--}}
{{--                        @endcan--}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> No roles found.
                </div>
            </div>
        @endforelse
    </div>
@stop
