@extends('adminlte::page')

@section('title', 'RTO Leads')

@section('content_header')
    <h1>RTO Leads</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('rto.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="stage" class="mr-2">Stage:</label>
                            <select name="stage" id="stage" class="form-control">
                                <option value="">All Stages</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>{{ $stage }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('rto.index') }}" class="btn btn-secondary ml-2">Reset</a>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('rto.create') }}" class="btn btn-success mr-2">Add New RTO Lead</a>
                    <a href="{{ route('rto.create-insurance-lead') }}" class="btn btn-info">Add New Insurance Lead</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>RTO Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td><span class="badge badge-info">{{ $lead->rto_stage ?? 'New' }}</span></td>
                            <td>
                                <a href="{{ route('rto.show', $lead->id) }}" class="btn btn-info btn-sm">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop
