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
    <style>
        .role-grid {
            min-height: 200px;
        }

        .role-card-wrap {
            cursor: grab;
        }

        .role-card-wrap.dragging {
            opacity: 0.55;
        }

        .role-card-wrap.drag-over .card {
            border: 2px dashed #17a2b8;
            transform: translateY(-2px);
        }

        .role-drag-handle {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
        }

        .role-position-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
        }
    </style>

    <div class="alert alert-info">
        Drag and drop role cards to change position. Changes save automatically.
    </div>

    <div class="row">
        @forelse ($roles as $role)
            <div class="col-md-4 mb-3 role-card-wrap" draggable="true" data-role-id="{{ $role->id }}">
                <div class="card card-widget widget-user-2 shadow-sm h-100 position-relative">
                    <span class="badge badge-light role-position-badge">#{{ $role->position }}</span>
                    <span class="role-drag-handle" title="Drag to reorder" aria-label="Drag to reorder">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <div class="widget-user-header bg-info">
                        <div class="widget-user-image">
                            <i class="fas fa-user-tag fa-3x text-white-50"></i>
                        </div>
                        <h3 class="widget-user-username">{{ $role->name }}</h3>
                        <h5 class="widget-user-desc">Position: {{ $role->position }}</h5>
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
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Role" aria-label="Edit Role">
                                <i class="fas fa-edit"></i>
                            </a>
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

@section('js')
    <script>
        $(function () {
            let dragged = null;

            function getRoleCards() {
                return $('.role-card-wrap');
            }

            function syncPositionBadges() {
                getRoleCards().each(function (index) {
                    $(this).find('.role-position-badge').text('#' + (index + 1));
                });
            }

            function saveOrder() {
                const roles = getRoleCards().map(function () {
                    return $(this).data('role-id');
                }).get();

                return $.ajax({
                    url: "{{ route('admin.roles.reorder') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        roles: roles
                    }
                }).done(function () {
                    window.location.reload();
                });
            }

            getRoleCards().attr('draggable', true);

            getRoleCards().on('dragstart', function (e) {
                dragged = this;
                $(this).addClass('dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
            });

            getRoleCards().on('dragend', function () {
                $(this).removeClass('dragging');
                getRoleCards().removeClass('drag-over');
                dragged = null;
            });

            getRoleCards().on('dragover', function (e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                if (dragged && dragged !== this) {
                    $(this).addClass('drag-over');
                }
            });

            getRoleCards().on('dragleave', function () {
                $(this).removeClass('drag-over');
            });

            getRoleCards().on('drop', function (e) {
                e.preventDefault();
                if (!dragged || dragged === this) {
                    return;
                }

                const $dragged = $(dragged);
                const $target = $(this);

                if ($dragged.index() < $target.index()) {
                    $target.after($dragged);
                } else {
                    $target.before($dragged);
                }

                getRoleCards().removeClass('drag-over');
                syncPositionBadges();

                saveOrder().fail(function () {
                    window.location.reload();
                });
            });
        });
    </script>
@stop
