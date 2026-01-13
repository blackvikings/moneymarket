@extends('adminlte::page')

@section('title', 'Insurance Leads')

@section('content_header')
    <h1>Insurance Leads</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 text-right">
            <a href="{{ route(get_route_prefix() . 'insurance-leads.export', request()->all()) }}" class="btn btn-success mr-1">Export</a>
            <a href="{{ route(get_route_prefix() . 'insurance-leads.create') }}" class="btn btn-primary">Add New Insurance Lead</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Expiry Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>
                                @if($lead->insurance_end_date)
                                    {{ $lead->insurance_end_date->format('d-m-Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route(get_route_prefix() . 'insurance-leads.show', $lead->id) }}" class="btn btn-info btn-sm">View & Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No insurance leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop
