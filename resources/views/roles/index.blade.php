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
                    <div class="card-body p-0">
                        <div class="p-3">
                            <p class="text-muted mb-2 font-weight-bold">Permissions:</p>
                            @if($role->permissions->count() > 0)
                                @php
                                    // Group permissions by module
                                    $groupedPermissions = $role->permissions->groupBy('module');
                                @endphp

                                <div class="accordion" id="accordionExample{{ $role->id }}">
                                    @foreach($groupedPermissions as $module => $permissions)
                                        <div class="card mb-1 border-0">
                                            <div class="card-header p-0" id="heading{{ $role->id }}{{ Str::slug($module) }}">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse{{ $role->id }}{{ Str::slug($module) }}" aria-expanded="false" aria-controls="collapse{{ $role->id }}{{ Str::slug($module) }}">
                                                        {{ $module ?? 'Other' }} <span class="badge badge-secondary float-right">{{ $permissions->count() }}</span>
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapse{{ $role->id }}{{ Str::slug($module) }}" class="collapse" aria-labelledby="heading{{ $role->id }}{{ Str::slug($module) }}" data-parent="#accordionExample{{ $role->id }}">
                                                <div class="card-body p-2 bg-light">
                                                    <div class="d-flex flex-wrap">
                                                        @foreach ($permissions as $permission)
                                                            <span class="badge badge-info m-1">{{ $permission->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="font-italic text-muted">No permissions assigned.</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-right bg-white border-top">
                        @can('edit role')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Role">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('delete role')
                            @if($role->name !== 'admin')
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        @endcan
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
