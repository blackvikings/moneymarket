@extends('adminlte::page')

@section('title', 'CRM Lead Details')

@section('content_header')
    <h1>CRM Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Stages</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($stages as $index => $stage)
                            <div class="time-label">
                                <span class="bg-{{ $index <= $currentStageIndex ? 'green' : 'gray' }}">{{ $stage }}</span>
                            </div>
                            @if($index <= $currentStageIndex)
                                <div>
                                    <i class="fas fa-check bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $lead->{'crm_'.strtolower(str_replace(' ', '_', $stage)).'_at'} ? $lead->{'crm_'.strtolower(str_replace(' ', '_', $stage)).'_at'}->format('d M Y, h:i A') : 'Pending' }}</span>
                                        <h3 class="timeline-header">Stage Completed</h3>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($lead->crm_stage !== 'Closed')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Update Current Stage: <span class="text-primary">{{ $lead->crm_stage }}</span></h3>
                </div>
                <div class="card-body">
                    @if($lead->crm_stage === 'Disbursal')
                        {{-- Disbursal Form --}}
                        @include('crm.partials.disbursal_form')
                    @else
                        {{-- Standard Yes/No Form --}}
                        <form action="{{ route('crm.update-stage', $lead->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}">
                            <p>Do you want to mark this stage as complete and move to the next one?</p>
                            <button type="submit" name="action" value="Yes" class="btn btn-success">Yes</button>
                            <button type="submit" name="action" value="No" class="btn btn-danger">No</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Include lead details partial --}}
    @include('crm.partials.lead_details')

@stop
