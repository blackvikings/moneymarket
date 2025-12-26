@extends('adminlte::page')

@section('title', 'Bank Branches')

@section('content_header')
    <h1>Bank Branches</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create branch')
                        <a href="{{ route('bank-branches.create') }}" class="btn btn-primary">Create Branch</a>
                    @endcan
                </div>
            </div>
            <br>
            <form action="{{ route('bank-branches.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="bank_id" class="form-control">
                            <option value="">All Banks</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('bank-branches.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Branch Name</th>
                        <th>Bank Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->bank->name }}</td>
                            <td>
                                @can('edit branch')
                                    <a href="{{ route('bank-branches.edit', $branch->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                @endcan
                                @can('delete branch')
                                    <form action="{{ route('bank-branches.destroy', $branch->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $branches->links() }}
        </div>
    </div>
@stop
