@extends('adminlte::page')

@section('title', 'CRM - Bank Assigned Leads')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>CRM - Bank Assigned Leads</h1>
        <a href="{{ route(get_route_prefix() . 'crm.export', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export
        </a>
    </div>
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
            min-width: 173px;
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
            height: 5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            text-decoration: none !important;
            color: #4b5563;
            background-color: white;
            border: 1px solid #d1d5db; /* Added border */
            border-right: none; /* Remove right border to avoid double borders with overlap */
        }

        .crm-tab-container:last-child .crm-tab-content {
            border-right: 1px solid #d1d5db;
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
            word-wrap: break-word;
        }

        .crm-tab-content.active .crm-tab-subtitle {
            opacity: 0.8;
            word-wrap: break-word;
        }

        .crm-tab-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1e3a8a;
            word-break: break-word;
            text-align: center;
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
        .upper-line {
            display: block;
            height: 1px;
            width: 70%;
            background-color: #2563eb;
            padding-bottom: 2px;
            margin-top: 12px;
        }
        @media(max-width: 768px)
        {
            .crm-tabs-wrapper {
               flex-wrap: wrap;
            }
        }
    </style>

    <div class="card">
        <div class="card-body p-2">
            <div class="crm-tabs-wrapper mb-0">
                {{-- All Tab --}}
                <div class="crm-tab-container">
                    <a href="{{ route(get_route_prefix() . 'crm.index') }}" class="crm-tab-content clip-chevron {{ request('stage') == '' ? 'active' : '' }}">
                        <span class="crm-tab-title">All</span>
                        <span class="crm-tab-subtitle">{{ $counts['All'] ?? 0 }}</span>
                        <span class="upper-line"></span>
                    </a>
                </div>

                {{-- Stages Tabs --}}
                @foreach($stages as $stage)
                    <div class="crm-tab-container">
                        <a href="{{ route(get_route_prefix() . 'crm.index', ['stage' => $stage]) }}" class="crm-tab-content clip-chevron {{ request('stage') == $stage ? 'active' : '' }}">
                            <span class="crm-tab-title">{{ $stage }}</span>
                            <span class="crm-tab-subtitle">{{ $counts[$stage] ?? 0 }}</span>
                            <span class="upper-line"></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Bank</th>
                        <th>Branch</th>
                        <th>CRM Stage</th>
                        <th>Start Date</th>
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
                            <td>{{ $lead->converted_at ? $lead->converted_at->format('d-m-Y') : '-' }}</td>
                            <td>
                                <a href="{{ route(get_route_prefix() . 'crm.show', $lead->id) }}" class="btn btn-info btn-sm">View</a>
                                @if($lead->crm_stage !== 'Closed')
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actionModal{{ $lead->id }}">
                                        Action
                                    </button>
                                @endif

                                <!-- Action Modal -->
                                <div class="modal fade" id="actionModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}">
                                                    <button type="submit" name="action" value="Yes" class="btn btn-success">Yes</button>
                                                </form>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No leads found in this CRM stage.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leads->links() }}
        </div>
    </div>
@stop
