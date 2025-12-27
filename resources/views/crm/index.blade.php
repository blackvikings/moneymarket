@extends('adminlte::page')

@section('title', 'CRM - Bank Assigned Leads')

@section('content_header')
    <h1>CRM - Bank Assigned Leads</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('crm.index') }}" method="GET" class="form-inline">
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
                <a href="{{ route('crm.index') }}" class="btn btn-secondary ml-2">Reset</a>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Bank</th>
                        <th>Branch</th>
                        <th>CRM Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>{{ $lead->bank ? $lead->bank->name : '-' }}</td>
                            <td>{{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</td>
                            <td><span class="badge badge-primary">{{ $lead->crm_stage ?? 'New' }}</span></td>
                            <td>
                                <a href="{{ route('crm.show', $lead->id) }}" class="btn btn-info btn-sm">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No leads found in this CRM stage.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop
