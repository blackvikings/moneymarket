@extends('adminlte::page')

@section('title', 'CRM Lead Details')

@section('content_header')
    <h1>CRM Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    <style>
        /* Custom Tab Design */
        .crm-tabs-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
            overflow-x: auto;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 1rem;
        }
        .crm-tab-container {
            position: relative;
            flex: 1 1 0%;
            min-width: 120px;
            margin-left: -1rem;
            z-index: 1;
        }
        .crm-tab-container:first-child {
            margin-left: 0;
            z-index: 0;
        }
        /* Stacking context */
        .crm-tab-container:nth-child(1) { z-index: 10; }
        .crm-tab-container:nth-child(2) { z-index: 9; }
        .crm-tab-container:nth-child(3) { z-index: 8; }
        .crm-tab-container:nth-child(4) { z-index: 7; }
        .crm-tab-container:nth-child(5) { z-index: 6; }
        .crm-tab-container:nth-child(6) { z-index: 5; }
        .crm-tab-container:nth-child(7) { z-index: 4; }
        .crm-tab-container:nth-child(8) { z-index: 3; }
        .crm-tab-container:nth-child(9) { z-index: 2; }
        .crm-tab-container:nth-child(10) { z-index: 1; }

        .crm-tab-content {
            height: 4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-right: 1rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            text-decoration: none !important;
            color: #4b5563;
            background-color: white;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }

        .crm-tab-content:hover {
            background-color: #eff6ff;
        }

        .crm-tab-content.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .crm-tab-content.active .crm-tab-subtitle,
        .crm-tab-content.active .crm-tab-title {
            color: white;
        }

        .crm-tab-content.active .crm-tab-subtitle {
            opacity: 0.8;
        }

        .crm-tab-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1e3a8a;
        }

        .crm-tab-subtitle {
            font-size: 1.25rem;
            font-weight: 800;
            color: #9ca3af;
        }

        /* Clip Path */
        .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 15px 50%);
        }
        .crm-tab-container:first-child .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%);
        }
    </style>

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lead Details</h4>
        @if($lead->crm_stage !== 'Closed')
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#actionModal">
                Action
            </button>
        @endif
    </div>

    {{-- Lead Details Card --}}
    <div class="card">
        <div class="card-body">
            @include('crm.partials.lead_details')
        </div>
    </div>

    {{-- Disbursal Details Card (if applicable) --}}
    @if($lead->crm_stage === 'Disbursal' || $lead->crm_stage === 'Closed')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Disbursal Details</h3>
                @if($lead->crm_stage === 'Disbursal')
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#disbursalModal">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @include('crm.partials.disbursal_details')
            </div>
        </div>
    @endif

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stage: {{ $lead->crm_stage }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you want to mark this stage as complete and move to the next one?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('crm.update-stage', $lead->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}">
                        <button type="submit" name="action" value="Yes" class="btn btn-success">Yes</button>
                    </form>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disbursal Modal -->
    <div class="modal fade" id="disbursalModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Disbursal Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('crm.partials.disbursal_form')
                </div>
            </div>
        </div>
    </div>
@stop
