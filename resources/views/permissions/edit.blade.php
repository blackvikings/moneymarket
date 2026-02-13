@extends('adminlte::page')

@section('title', 'Edit Permission')

@section('content_header')
    <h1>Edit Permission</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="module">Module <span class="text-danger">*</span></label>
                    <select name="module" id="module" class="form-control @error('module') is-invalid @enderror" required>
                        <option value="">Select Module</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ old('module', $permission->module) == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                        <option value="Other" {{ old('module', $permission->module) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('module') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save Permission</button>
            </form>
        </div>
    </div>
@stop
