@extends('adminlte::page')

@section('title', 'Banks')

@section('content_header')
    <h1>Banks</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    @can('create bank')
                        <a href="{{ route('admin.banks.create') }}" class="btn btn-primary">Create Bank</a>
                    @endcan
                </div>
                <div class="col-md-6 text-right">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->can('export bank'))
                        <a href="{{ route('admin.banks.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
                    @endif
                </div>
            </div>
            <br>
            <form action="{{ route('admin.banks.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Reset</a>
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
                        <th>Name</th>
                        <th>Branches</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($banks as $bank)
                        <tr>
                            <td>{{ $bank->name }}</td>
                            <td>
                                @foreach ($bank->branches as $branch)
                                    <span class="badge badge-info" title="{{ $branch->address_line }}, {{ $branch->city->name ?? '' }}, {{ $branch->state->name ?? '' }} - {{ $branch->pincode }}">
                                        {{ $branch->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if ($bank->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @can('view bank')
                                    <button type="button" class="btn btn-sm btn-info view-bank-btn mr-1" data-id="{{ $bank->id }}">View</button>
                                @endcan
                                @can('edit bank')
                                    <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-sm btn-primary mr-1">Edit</a>
                                    <form action="{{ route('admin.banks.toggle-status', $bank->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $bank->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $bank->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $banks->links() }}
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
            $('.view-bank-btn').click(function() {
                let bankId = $(this).data('id');
                let url = '{{ route("admin.banks.show", ":id") }}'.replace(':id', bankId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load bank details.</div>');
                    }
                });
            });
        });
    </script>
@stop
