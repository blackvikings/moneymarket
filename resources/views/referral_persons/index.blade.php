@extends('adminlte::page')

@section('title', 'Referral Persons')

@section('content_header')
    <h1>Referral Persons</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Referral Persons</h3>
            <div class="card-tools">
                <a href="{{ route(get_route_prefix() . 'referral-persons.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Referral Person
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referralPersons as $person)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $person->name }}</td>
                            <td>{{ $person->contact_number ?? '-' }}</td>
                            <td>{{ $person->email ?? '-' }}</td>
                            <td>{{ $person->address ?? '-' }}</td>
                            <td>
                                <a href="{{ route(get_route_prefix() . 'referral-persons.edit', $person->id) }}" class="btn btn-info btn-sm">Edit</a>
                                <form action="{{ route(get_route_prefix() . 'referral-persons.destroy', $person->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this person?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No referral persons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $referralPersons->links() }}
        </div>
    </div>
@stop
