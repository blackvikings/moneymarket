@extends('adminlte::page')

@section('title', 'RTO Lead Details')

@section('content_header')
    <h1>RTO Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RTO Stages</h3>
                </div>
                <div class="card-body">
                    {{-- Simplified timeline for RTO --}}
                    <div class="d-flex justify-content-between">
                        @foreach($stages as $index => $stage)
                            <span class="badge badge-{{ $index <= $currentStageIndex ? 'success' : 'secondary' }} p-2">{{ $stage }}</span>
                            @if(!$loop->last)
                                <span class="flex-grow-1" style="height: 2px; background-color: #ccc; margin: auto 5px;"></span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Stage: <span class="text-primary">{{ $lead->rto_stage }}</span></h3>
                </div>
                <div class="card-body">
                    @if($lead->rto_stage === 'Checking')
                        @include('rto.partials.checking_form')
                    @elseif($lead->rto_stage === 'Loan Return')
                        @include('rto.partials.loan_return_form')
                    @elseif($lead->rto_stage === 'Vehicle Detail')
                         @include('rto.partials.vehicle_detail_form')
                    @elseif($lead->rto_stage === 'Insurance')
                        @include('rto.partials.insurance_form')
                    @elseif($lead->rto_stage === 'RTO Done')
                        <p class="text-success font-weight-bold">This lead's RTO process is complete.</p>
                    @else {{-- New Stage --}}
                        <p>This lead is new to the RTO process.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Actions</h3></div>
                <div class="card-body">
                    @if($lead->rto_stage !== 'RTO Done')
                        <form action="{{ route('rto.update-stage', $lead->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="current_stage" value="{{ $lead->rto_stage }}">
                            <p>Move to next stage?</p>
                            <button type="submit" name="action" value="Yes" class="btn btn-success">Yes, Move to Next</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Lead Info</h3></div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    @if($lead->rto_address_proof_path)
                        <p><a href="{{ asset('storage/' . $lead->rto_address_proof_path) }}" target="_blank">View Address Proof</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
