@extends('adminlte::page')

@section('title', 'Create Role')

@section('content_header')
    <h1>Create Role</h1>
@stop

@section('content')
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
        .permission-group {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .permission-group h5 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .permission-item {
            margin-bottom: 10px;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST" id="createRoleForm">
                @csrf
                <div class="form-group">
                    <label for="name">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="position">Position <span class="text-danger">*</span></label>
                    <input type="number" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $nextPosition ?? 1) }}" min="1" required>
                    <small class="text-muted">Use a unique position number. The next suggested value is prefilled.</small>
                    @error('position') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Permissions <span class="text-danger">*</span></label>
                    <div class="row">
                        @php
                            // Group permissions by module
                            $groupedPermissions = $permissions->groupBy(function($permission) {
                                $parts = explode(' ', $permission->name);
                                // Handle special cases or default grouping
                                if (count($parts) > 1) {
                                    // e.g., "view employee" -> "Employee"
                                    // "view rto agent" -> "Rto Agent"
                                    // "view insurance lead" -> "Insurance Lead"
                                    $module = array_slice($parts, 1);
                                    return ucwords(implode(' ', $module));
                                }
                                return 'Other';
                            });

                            // Sort groups alphabetically
                            $groupedPermissions = $groupedPermissions->sortKeys();
                        @endphp

                        @foreach($groupedPermissions as $module => $perms)
                            <div class="col-md-4">
                                <div class="permission-group">
                                    <h5>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input select-all-group" id="group_{{ Str::slug($module) }}">
                                            <label class="custom-control-label" for="group_{{ Str::slug($module) }}">{{ $module }}</label>
                                        </div>
                                    </h5>
                                    @foreach($perms as $permission)
                                        <div class="permission-item">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="permissions[]"
                                                       class="custom-control-input permission-checkbox group-{{ Str::slug($module) }}"
                                                       id="perm_{{ $permission->id }}"
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                                    {{ ucwords($permission->name) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions') <span class="text-danger d-block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Role</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle "Select All" for groups
            $('.select-all-group').change(function() {
                var groupId = $(this).attr('id').replace('group_', '');
                $('.group-' + groupId).prop('checked', $(this).prop('checked'));
            });

            // If all items in a group are checked, check the "Select All" box
            $('.permission-checkbox').change(function() {
                var groupClass = $(this).attr('class').match(/group-[\w-]+/)[0];
                var groupId = groupClass.replace('group-', '');
                var allChecked = $('.' + groupClass + ':checked').length === $('.' + groupClass).length;
                $('#group_' + groupId).prop('checked', allChecked);
            });

            $('#createRoleForm').validate({
                rules: {
                    name: { required: true },
                    position: { required: true, number: true },
                    'permissions[]': { required: true, minlength: 1 }
                },
                messages: {
                    name: "Please enter a role name",
                    position: {
                        required: "Please enter a position",
                        number: "Please enter a valid number"
                    },
                    'permissions[]': "Please select at least one permission"
                },
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "permissions[]") {
                        error.insertAfter(element.closest('.form-group').find('label').first());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@stop
