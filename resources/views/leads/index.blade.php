@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
    <h1>Leads</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('leads.index') }}" method="GET" class="form-inline">
                        <select name="status" class="form-control mr-2">
                            <option value="">All Statuses</option>
                            <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                            <option value="Prospect" {{ request('status') == 'Prospect' ? 'selected' : '' }}>Prospect</option>
                            <option value="Lead" {{ request('status') == 'Lead' ? 'selected' : '' }}>Lead</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Not_Active" {{ request('status') == 'Not_Active' ? 'selected' : '' }}>Not Active</option>
                        </select>
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Team Lead'))
                            <select name="assigned_to" class="form-control mr-2">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary ml-2">Reset</a>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('leads.export', request()->all()) }}" class="btn btn-success mr-2">Export</a>
                    @can('create lead')
                        <a href="{{ route('leads.create') }}" class="btn btn-primary">Add Lead</a>
                    @endcan
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
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Follow Up</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>
                                <span class="badge badge-{{ $lead->status == 'Lead' ? 'success' : ($lead->status == 'Rejected' || $lead->status == 'Not_Active' ? 'danger' : 'info') }}">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td>{{ $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned' }}</td>
                            <td>
                                @if($lead->follow_up_date)
                                    {{ $lead->follow_up_date->format('d-m-Y') }} {{ $lead->follow_up_time }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info btn-sm">View</a>
                                @can('edit lead')
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endcan
                                @can('delete lead')
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop
