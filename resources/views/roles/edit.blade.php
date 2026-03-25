@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <h1>Edit Role</h1>
@stop

@section('content')
    <style>
        .permission-card {
            height: 100%;
            margin-bottom: 20px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .permission-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
        }
        .permission-card .card-body {
            padding: 1.25rem;
        }
        .permission-item {
            margin-bottom: 0.5rem;
        }
        .custom-switch .custom-control-label::before {
            height: 1.5rem;
            width: 2.75rem;
            border-radius: 1.5rem;
        }
        .custom-switch .custom-control-label::after {
            height: calc(1.5rem - 4px);
            width: calc(1.5rem - 4px);
            border-radius: 50%;
        }
        .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
            transform: translateX(1.25rem);
        }
        .parent-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4e73df;
        }
    </style>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="editRoleForm">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <label for="name" class="font-weight-bold">Role Name</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="position" class="font-weight-bold">Position</label>
                    <input type="number" name="position" id="position" class="form-control form-control-lg @error('position') is-invalid @enderror" value="{{ old('position', $role->position) }}" required>
                    @error('position') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <h4 class="mb-3 text-gray-800">Permissions</h4>

                @php
                    // Group permissions by their 'module' column
                    $groupedPermissions = $permissions->groupBy('module');

                    // Define parent permissions for each module
                    $moduleParents = [
                        'LMS' => 'show lms option',
                        'CRM' => 'show crm option',
                        'Employees' => 'show employee option',
                        'Masters' => 'show master option',
                    ];
                @endphp

                <div class="row">
                    @foreach($groupedPermissions as $moduleName => $perms)
                        @php
                            $moduleSlug = Str::slug($moduleName);
                            $parentPermName = $moduleParents[$moduleName] ?? null;
                            $parentPerm = $perms->firstWhere('name', $parentPermName);
                            $children = $perms->reject(function($p) use ($parentPermName) {
                                return $p->name === $parentPermName;
                            });
                        @endphp

                        <div class="col-lg-6 col-md-12">
                            <div class="card permission-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="m-0 font-weight-bold text-primary">{{ $moduleName }} Module</h5>
                                        <small class="text-muted">Enable to show in menu</small>
                                    </div>
                                    @if($parentPerm)
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input parent-checkbox"
                                                   id="parent_{{ $parentPerm->id }}"
                                                   name="permissions[]"
                                                   value="{{ $parentPerm->id }}"
                                                   data-module="{{ $moduleSlug }}"
                                                   {{ in_array($parentPerm->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="custom-control-label parent-label" for="parent_{{ $parentPerm->id }}">
                                                {{-- Label is hidden/styled via CSS but required for functionality --}}
                                            </label>
                                        </div>
                                    @else
                                        <span class="badge badge-secondary">No Parent Permission</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($children as $permission)
                                            <div class="col-md-6 permission-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="permissions[]"
                                                           class="custom-control-input child-checkbox module-{{ $moduleSlug }}"
                                                           id="perm_{{ $permission->id }}"
                                                           value="{{ $permission->id }}"
                                                           data-module="{{ $moduleSlug }}"
                                                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                                        {{ ucwords($permission->name) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 mt-3">
                        <i class="fas fa-save mr-2"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Parent checkbox toggles all children
            $('.parent-checkbox').change(function() {
                var module = $(this).data('module');
                var isChecked = $(this).prop('checked');
                $('.module-' + module).prop('checked', isChecked);
            });

            // Child checkbox logic
            $('.child-checkbox').change(function() {
                var module = $(this).data('module');
                var parentCheckbox = $('#parent_' + $('.parent-checkbox[data-module="' + module + '"]').val());

                if ($(this).prop('checked')) {
                    // If any child is checked, check the parent
                    parentCheckbox.prop('checked', true);
                } else {
                    // If all children are unchecked, uncheck the parent
                    var anyChecked = $('.module-' + module + ':checked').length > 0;
                    if (!anyChecked) {
                        parentCheckbox.prop('checked', false);
                    }
                }
            });
        });
    </script>
@stop
