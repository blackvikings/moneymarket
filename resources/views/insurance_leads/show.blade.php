@extends('adminlte::page')

@section('title', 'Insurance Lead Details')

@section('content_header')
    <h1>Insurance Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    @if($lead->insurance_address_proof_path)
                        <p><a href="{{ asset('storage/' . $lead->insurance_address_proof_path) }}" target="_blank">View Address Proof</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Model:</strong> {{ $lead->vehicleName->name ?? 'N/A' }}</p>
                    <p><strong>RC Number:</strong> {{ $lead->rc_number ?? 'N/A' }}</p>
                    <p><strong>Manufacturer:</strong> {{ $lead->manufacture->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Insurance Policy Details</h3>
        </div>
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'insurance-leads.update-details', $lead->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Insurance Company</label>
                            <select name="insurance_id" class="form-control" required>
                                <option value="">Select Company</option>
                                @foreach($insurances as $insurance)
                                    <option value="{{ $insurance->id }}" {{ $lead->insurance_id == $insurance->id ? 'selected' : '' }}>{{ $insurance->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Insurance Start Date</label>
                            <input type="date" name="insurance_start_date" class="form-control" value="{{ $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Insurance End Date</label>
                            <input type="date" name="insurance_end_date" class="form-control" value="{{ $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Type Of Insurance</label>
                    @php
                        $insuranceTypes = ['Third Party Insurance', 'Commercial Vehicle Insurance', 'Comprehensive Car Insurance', 'Private Car Insurance', 'Personal Accident Cover', 'Zero Depreciation Cover', 'Roadside Assistance'];
                        $selectedTypes = $lead->insurance_type_of_insurance ? explode(', ', $lead->insurance_type_of_insurance) : [];
                    @endphp
                    <div class="row">
                        @foreach($insuranceTypes as $type)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="insurance_type_of_insurance[]" value="{{ $type }}" {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $type }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Premium Amount</label>
                            <input type="number" step="0.01" name="insurance_premium_amount" class="form-control" value="{{ $lead->insurance_premium_amount }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>IDV (Insured Declared Value)</label>
                            <input type="number" step="0.01" name="insurance_idv_amount" class="form-control" value="{{ $lead->insurance_idv_amount }}" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Details</button>
            </form>
        </div>
    </div>
@stop
