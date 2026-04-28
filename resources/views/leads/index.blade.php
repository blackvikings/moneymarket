@extends('adminlte::page')

@section('title', 'Leads')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Leads</h1>
        <div>
            @can('create lead')
                @if(auth()->user()->hasRole('admin') || auth()->user()->can('import lead'))
                    <button type="button" class="btn btn-info mr-1" data-toggle="modal" data-target="#importModal">Import Leads</button>
                @endif
                <a href="{{ route(get_route_prefix() . 'leads.create') }}" class="btn btn-success">Add Lead</a>
            @endcan
        </div>
    </div>
@stop

@section('content')
    @php
        $canCreateBank = auth()->user()->hasRole('admin') || auth()->user()->can('create bank');
    @endphp
    <style>
        /* Custom Tab Design */
        .leads-tabs-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
            overflow-x: auto;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }
        .leads-tab-container {
            position: relative;
            flex: 1 1 0%;
            min-width: 120px;
            margin-left: -1rem;
            z-index: 1;
        }
        .leads-tab-container:first-child {
            margin-left: 0;
            z-index: 0;
        }
        /* Stacking context */
        .leads-tab-container:nth-child(1) { z-index: 6; }
        .leads-tab-container:nth-child(2) { z-index: 5; }
        .leads-tab-container:nth-child(3) { z-index: 4; }
        .leads-tab-container:nth-child(4) { z-index: 3; }
        .leads-tab-container:nth-child(5) { z-index: 2; }
        .leads-tab-container:nth-child(6) { z-index: 1; }

        .leads-tab-content {
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
            border: 1px solid #d1d5db;
        }

        .leads-tab-content:hover {
            background-color: #eff6ff;
        }

        .leads-tab-content.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .leads-tab-content.active .leads-tab-subtitle,
        .leads-tab-content.active .leads-tab-title {
            color: white;
        }

        .leads-tab-content.active .leads-tab-subtitle {
            opacity: 0.8;
        }

        .leads-tab-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1e3a8a;
        }

        .leads-tab-subtitle {
            font-size: 1.25rem;
            font-weight: 800;
            color: #9ca3af;
        }

        /* Clip Path */
        .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 15px 50%);
        }
        .leads-tab-container:first-child .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%);
        }

        .upper-line {
            display: block;
            height: 1px;
            width: 90%;
            background-color: #2563eb;
            padding-bottom: 2px;
            margin: 0 auto;
        }

        .icon-action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .leads-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            align-items: end;
        }

        .leads-filter-form .form-group {
            margin-bottom: 0;
        }

        .leads-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .leads-table-wrap table {
            width: 100%;
            min-width: 1100px;
        }

        @media (max-width: 768px) {
            .leads-filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .leads-filter-form .form-group,
            .leads-filter-form .btn {
                width: 100%;
            }

            .leads-filter-form .mr-2,
            .leads-filter-form .ml-2 {
                margin-right: 0 !important;
                margin-left: 0 !important;
            }

            .leads-filter-form .form-group {
                margin-bottom: 0.25rem;
            }

            .leads-tab-container {
                min-width: 96px;
            }

            .leads-tab-content {
                height: 3.5rem;
                padding-right: 0.5rem;
            }

            .leads-tab-title {
                font-size: 0.72rem;
            }

            .leads-tab-subtitle {
                font-size: 1rem;
            }
        }

    </style>

    <div class="card mb-3">
        <div class="card-body p-2">
            <form action="{{ route(get_route_prefix() . 'leads.index') }}" method="GET" class="leads-filter-form">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Start Date:</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">End Date:</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="form-group mr-2">
                    <label for="customer_name" class="mr-2">Customer Name:</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Search by name">
                </div>
                <div class="form-group mr-2">
                    <label for="contact_number" class="mr-2">Contact Number:</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ request('contact_number') }}" placeholder="Search by number">
                </div>
                <div class="form-group mr-2">
                    <label for="assignment_status" class="mr-2">Assignment:</label>
                    <select name="assignment_status" class="form-control">
                        <option value="">All</option>
                        <option value="Assigned" {{ request('assignment_status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="Unassigned" {{ request('assignment_status') == 'Unassigned' ? 'selected' : '' }}>Unassigned</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route(get_route_prefix() . 'leads.index', ['status' => request('status')]) }}" class="btn btn-secondary ml-2">Reset</a>
                @if(auth()->user()->hasRole('admin') || auth()->user()->can('export lead'))
                    <a href="{{ route(get_route_prefix() . 'leads.export', request()->all()) }}" class="btn btn-success ml-2">Export</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-2">
            <div class="leads-tabs-wrapper mb-0">
                {{-- All Tab --}}
                <div class="leads-tab-container">
                    <a href="{{ route(get_route_prefix() . 'leads.index', array_merge(request()->except(['status', 'page']), ['status' => ''])) }}" class="leads-tab-content clip-chevron {{ request('status') == '' ? 'active' : '' }}">
                        <span class="leads-tab-title">All</span>
                        <span class="leads-tab-subtitle">{{ $counts['All'] ?? 0 }}</span>
                        <span class="upper-line"></span>
                    </a>
                </div>

                {{-- Statuses Tabs --}}
                @foreach($statuses as $status)
                    <div class="leads-tab-container">
                        <a href="{{ route(get_route_prefix() . 'leads.index', array_merge(request()->except(['status', 'page']), ['status' => $status])) }}" class="leads-tab-content clip-chevron {{ request('status') == $status ? 'active' : '' }}">
                            <span class="leads-tab-title">{{ $status == 'New' ? 'New Task' : $status }}</span>
                            <span class="leads-tab-subtitle">{{ $counts[$status] ?? 0 }}</span>
                            <span class="upper-line"></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(auth()->user()->hasRole('admin'))
                <form action="{{ route(get_route_prefix() . 'leads.bulk-assign') }}" method="POST" id="bulkAssignForm">
                    @csrf
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-3">
                            <label>Assign Selected To:</label>
                            <select name="assigned_to" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to assign selected leads?')">Bulk Assign</button>
                        </div>
                    </div>
                </form>
            @endif

            @php
                $showBankExecutiveColumn = request('status') === 'Bank Assign' || $leads->contains(fn ($lead) => $lead->status === 'Bank Assign');
            @endphp

            <div class="leads-table-wrap">
            <table class="table table-bordered table-hover table-responsive-sm mb-0">
                <thead>
                    <tr>
                        @if(auth()->user()->hasRole('admin'))
                            <th><input type="checkbox" id="selectAll" form="bulkAssignForm"></th>
                        @endif
                        @php
                            $hideFollowUpColumn = in_array(request('status'), ['Lead', 'Bank Assign', 'Rejected', 'Not_Active'], true);
                        @endphp
                        <th>ID</th>
                        <th>Created At</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Lead Type</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        @unless($hideFollowUpColumn)
                            <th>Follow Up</th>
                        @endunless
                        @if($showBankExecutiveColumn)
                            <th>Bank Executive</th>
                        @endif
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            @if(auth()->user()->hasRole('admin'))
                                <td>
                                    @if(!$lead->assigned_to)
                                        <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="lead-checkbox" form="bulkAssignForm">
                                    @endif
                                </td>
                            @endif
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->created_at->format('d-m-Y') }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>{{ $lead->lead_type ?? '-' }}</td>
                            <td>
                                @php
                                    $statusLabel = $lead->status == 'Prospect'
                                        ? 'Follow up'
                                        : str_replace('_', ' ', $lead->status);

                                    if ($lead->status !== 'New') {
                                        if (
                                            $lead->lead_type === 'Loan' &&
                                            !empty($lead->crm_stage) &&
                                            in_array($lead->status, ['Lead', 'Bank Assign'], true)
                                        ) {
                                            $statusLabel = $lead->crm_stage === 'New'
                                                ? 'Bank Assign'
                                                : $lead->crm_stage;
                                        }

                                        if (!empty($lead->rto_stage)) {
                                            $statusLabel = $lead->rto_stage === 'New'
                                                ? 'RTO'
                                                : $lead->rto_stage;
                                        }

                                        if (!empty($lead->insurance_lead_stage)) {
                                            $statusLabel = $lead->insurance_lead_stage === 'New'
                                                ? 'Insurance'
                                                : $lead->insurance_lead_stage;
                                        }
                                    }
                                @endphp
                                <span class="badge badge-{{ $lead->status == 'Lead' ? 'success' : ($lead->status == 'Rejected' || $lead->status == 'Not_Active' ? 'danger' : 'info') }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>{{ $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned' }}</td>
                            @unless($hideFollowUpColumn)
                                <td>
                                    @if($lead->follow_up_date)
                                        {{ \Carbon\Carbon::parse($lead->follow_up_date)->format('d-m-Y') }} {{ $lead->follow_up_time }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endunless
                            @if($showBankExecutiveColumn)
                                <td>
                                    @if($lead->status === 'Bank Assign')
                                        {{ $lead->bank_executive_name ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td>{{ $lead->currentCity->name ?? '-' }}</td>
                            <td>
                                @php
                                    $hideActionButton = !empty($lead->insurance_lead_stage)
                                        && (($lead->crm_stage ?? 'New') === 'New' || $lead->crm_stage === null);
                                @endphp
                                @can('view lead')
                                    <button type="button" class="btn btn-info btn-sm icon-action-btn view-lead-btn" data-id="{{ $lead->id }}" title="View Lead" aria-label="View Lead">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route(get_route_prefix() . 'leads.full-details', $lead->id) }}" class="btn btn-secondary btn-sm icon-action-btn" title="Full Details" aria-label="Full Details">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                @endcan
                                @can('edit lead')
                                    <a href="{{ route(get_route_prefix() . 'leads.edit', $lead->id) }}" class="btn btn-warning btn-sm icon-action-btn" title="Edit Lead" aria-label="Edit Lead">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                @endcan
                                @if($lead->status !== 'Rejected' && $lead->status !== 'Bank Assign' && !$hideActionButton)
                                    <button type="button" class="btn btn-primary btn-sm icon-action-btn" data-toggle="modal" data-target="#actionModal{{ $lead->id }}" title="Lead Actions" aria-label="Lead Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                @endif

                                <!-- Action Modal -->
                                <div class="modal fade" id="actionModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Action for {{ $lead->customer_name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @if($lead->status == 'Bank Assign')
                                                    <form action="{{ route(get_route_prefix() . 'leads.assign-bank', $lead->id) }}" method="POST">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>Assign Bank <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <select name="bank_id" class="form-control bank-select" required>
                                                                    <option value="">Select Bank</option>
                                                                    @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                                                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if($canCreateBank)
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addBankModal">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Assign Branch <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <select name="bank_branch_id" class="form-control branch-select" required>
                                                                    <option value="">Select Bank First</option>
                                                                </select>
                                                                @if($canCreateBank)
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-outline-secondary add-branch-trigger" type="button" data-toggle="modal" data-target="#addBranchModal">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Executive Name</label>
                                                            <input type="text" name="bank_executive_name" class="form-control" placeholder="Enter executive name">
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-block mt-3">Assign Bank & Move to CRM</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route(get_route_prefix() . 'leads.update-status', $lead->id) }}" method="POST" class="action-form">
                                                        @csrf
                                                        <input type="hidden" name="action" class="action-input">

                                                        @if($lead->status == 'New')
                                                            <div class="form-group">
                                                                <label>Select Action:</label><br>
                                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                    <label class="btn btn-outline-success active">
                                                                        <input type="radio" name="action_radio" value="interested" data-lead-id="{{ $lead->id }}" checked> Interested
                                                                    </label>
                                                                    <label class="btn btn-outline-danger">
                                                                        <input type="radio" name="action_radio" value="not_interested" data-lead-id="{{ $lead->id }}"> Not Interested
                                                                    </label>
                                                                    <label class="btn btn-outline-info">
                                                                        <input type="radio" name="action_radio" value="follow_up" data-lead-id="{{ $lead->id }}"> Follow Up
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="action-fields" id="not_interested-fields-{{ $lead->id }}" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="action-fields" id="follow_up-fields-{{ $lead->id }}" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="follow_up_date" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Time <span class="text-danger">*</span></label>
                                                                    <input type="time" name="follow_up_time" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input no-require" name="not_active" value="1" id="not_active_new_{{ $lead->id }}">
                                                                    <label class="form-check-label" for="not_active_new_{{ $lead->id }}">Mark as Not Active</label>
                                                                </div>
                                                            </div>

                                                        @elseif($lead->status == 'Prospect')
                                                            <div class="form-group">
                                                                <label>Select Action:</label><br>
                                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                    <label class="btn btn-outline-info active">
                                                                        <input type="radio" name="action_radio" value="follow_up" data-lead-id="{{ $lead->id }}" checked> Follow-up
                                                                    </label>
                                                                    <label class="btn btn-outline-success">
                                                                        <input type="radio" name="action_radio" value="qualify" data-lead-id="{{ $lead->id }}"> Qualify to Lead
                                                                    </label>
                                                                    <label class="btn btn-outline-danger">
                                                                        <input type="radio" name="action_radio" value="reject" data-lead-id="{{ $lead->id }}"> Reject
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="action-fields" id="follow_up-fields-{{ $lead->id }}">
                                                                <div class="form-group">
                                                                    <label>Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="follow_up_date" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Time <span class="text-danger">*</span></label>
                                                                    <input type="time" name="follow_up_time" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input no-require" name="not_active" value="1" id="not_active_prospect_{{ $lead->id }}">
                                                                    <label class="form-check-label" for="not_active_prospect_{{ $lead->id }}">Mark as Not Active</label>
                                                                </div>
                                                            </div>

                                                            <div class="action-fields" id="reject-fields-{{ $lead->id }}" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>

                                                        @elseif($lead->status == 'Lead')
                                                            <input type="hidden" name="action_radio" value="proceed_to_bank_assign" data-lead-id="{{ $lead->id }}">

                                                            @if($lead->lead_type == 'Loan')
                                                                <div class="action-fields" id="proceed_to_bank_assign-fields-{{ $lead->id }}">
                                                                    <div class="form-group">
                                                                        <label>Assign Bank <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <select name="bank_id" class="form-control bank-select" required>
                                                                                <option value="">Select Bank</option>
                                                                                @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                                                                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @if($canCreateBank)
                                                                                <div class="input-group-append">
                                                                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addBankModal">
                                                                                        <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Assign Branch <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <select name="bank_branch_id" class="form-control branch-select" required>
                                                                                <option value="">Select Bank First</option>
                                                                            </select>
                                                                            @if($canCreateBank)
                                                                                <div class="input-group-append">
                                                                                    <button class="btn btn-outline-secondary add-branch-trigger" type="button" data-toggle="modal" data-target="#addBranchModal">
                                                                                        <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Executive Name</label>
                                                                        <input type="text" name="bank_executive_name" class="form-control" placeholder="Enter executive name">
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <p>Are you sure you want to proceed?</p>
                                                            @endif
                                                        @elseif($lead->status == 'Not_Active')
                                                            <div class="form-group">
                                                                <label>Select Action:</label><br>
                                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                    <label class="btn btn-outline-success active">
                                                                        <input type="radio" name="action_radio" value="mark_active" data-lead-id="{{ $lead->id }}" checked> Mark as Active
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="action-fields" id="mark_active-fields-{{ $lead->id }}">
                                                                <div class="form-group">
                                                                    <label>New Status <span class="text-danger">*</span></label>
                                                                    <select name="new_status" class="form-control" required>
                                                                        <option value="New">New</option>
                                                                        <option value="Prospect">Follow up</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <button type="submit" class="btn btn-primary btn-block mt-3">Submit</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Global View Modal -->
    <div class="modal fade" id="globalViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="globalViewModalContent">
                {{-- Content will be loaded via AJAX --}}
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    @if(auth()->user()->hasRole('admin') || auth()->user()->can('import lead'))
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Leads</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route(get_route_prefix() . 'leads.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Upload File (CSV, Excel)</label>
                            <input type="file" name="file" class="form-control-file" required>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route(get_route_prefix() . 'leads.download-sample') }}" class="text-primary">Download Sample File</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" role="dialog" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankModalLabel">Add New Bank</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addBankForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Branch Name <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="branch_name" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addBranchModal" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBranchModalLabel">Add New Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addBranchForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="bank_id" id="branch_modal_bank_id">
                        <div class="form-group">
                            <label>Bank</label>
                            <input type="text" id="branch_modal_bank_name" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address_line" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
            let activeBranchContext = null;

            // --- AJAX View Modal ---
            $('.view-lead-btn').click(function() {
                let leadId = $(this).data('id');
                let url = '{{ route(get_route_prefix() . "leads.show", ":id") }}'.replace(':id', leadId);

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading details...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load lead details.</div>');
                    }
                });
            });

            // Function to handle radio change within a specific modal
            function handleRadioChange(modal) {
                let checkedRadio = modal.find('input[name="action_radio"]:checked');
                // If no radio is checked, check for hidden input
                let action = checkedRadio.length ? checkedRadio.val() : modal.find('input[name="action_radio"]').val();
                let leadId = checkedRadio.length ? checkedRadio.data('lead-id') : modal.find('input[name="action_radio"]').data('lead-id');

                // Hide all conditional sections first
                modal.find('.action-fields').hide();
                // Disable inputs in hidden sections so they are not submitted
                modal.find('.action-fields input, .action-fields select, .action-fields textarea').prop('required', false).prop('disabled', true);

                // Show the relevant section based on action
                if (action === 'not_interested' || action === 'follow_up' || action === 'qualify' || action === 'reject' || action === 'mark_active') {
                    let sectionId = '#' + action + '-fields-' + leadId;
                    let sectionToShow = modal.find(sectionId);

                    if (sectionToShow.length > 0) {
                        sectionToShow.show();
                        // Enable inputs in the shown section, excluding elements with 'no-require' class
                        sectionToShow.find('input:not(.no-require), select:not(.no-require), textarea:not(.no-require)').prop('required', true).prop('disabled', false);
                        // Also enable elements with 'no-require' class but don't set required to true
                        sectionToShow.find('.no-require').prop('disabled', false);
                    }
                } else if (action === 'proceed_to_bank_assign') {
                    // Special handling for proceed_to_bank_assign
                    let sectionId = '#' + action + '-fields-' + leadId;
                    let sectionToShow = modal.find(sectionId);

                    if (sectionToShow.length > 0) {
                        sectionToShow.show();
                        sectionToShow.find('select').prop('required', true).prop('disabled', false);
                        sectionToShow.find('input, textarea').prop('disabled', false);
                    }
                }
            }

            // Bind change event to radio buttons
            $(document).on('change', 'input[name="action_radio"]', function() {
                let modal = $(this).closest('.modal');
                handleRadioChange(modal);
            });

            // Trigger change when modal is shown to set initial state
            $('.modal').on('shown.bs.modal', function () {
                if($(this).attr('id') !== 'globalViewModal' && $(this).attr('id') !== 'addBankModal') {
                    handleRadioChange($(this));
                }
            });

            $('.action-form').submit(function(e) {
                let modal = $(this).closest('.modal');
                let checkedRadio = modal.find('input[name="action_radio"]:checked');
                let selectedAction = checkedRadio.length ? checkedRadio.val() : modal.find('input[name="action_radio"]').val();
                modal.find('.action-input').val(selectedAction);
            });

            $('.bank-select').change(function() {
                let bankId = $(this).val();
                let modal = $(this).closest('.modal');
                let branchSelect = modal.find('.branch-select');
                branchSelect.empty().append('<option value="">Loading...</option>');

                if (bankId) {
                    $.ajax({
                        url: '{{ route(get_route_prefix() . "get-branches", ["bank" => ":bankId"]) }}'.replace(':bankId', bankId),
                        type: 'GET',
                        success: function(data) {
                            branchSelect.empty().append('<option value="">Select Branch</option>');
                            $.each(data, function(key, branch) {
                                branchSelect.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                            });
                        }
                    });
                } else {
                    branchSelect.empty().append('<option value="">Select Bank First</option>');
                }
            });

            $(document).on('click', '.add-branch-trigger', function() {
                const modal = $(this).closest('.modal');
                const bankSelect = modal.find('.bank-select');
                const bankId = bankSelect.val();
                const bankName = bankSelect.find('option:selected').text();

                if (!bankId) {
                    alert('Please select bank first.');
                    return false;
                }

                activeBranchContext = {
                    modal: modal,
                    branchSelect: modal.find('.branch-select'),
                    bankSelect: bankSelect,
                };

                $('#branch_modal_bank_id').val(bankId);
                $('#branch_modal_bank_name').val(bankName);
            });

            // Select All Checkbox
            $('#selectAll').click(function() {
                $('.lead-checkbox').prop('checked', this.checked);
            });

            // If any checkbox is unchecked, uncheck "Select All"
            $('.lead-checkbox').click(function() {
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                }
            });

            // --- Add Bank AJAX ---
            $('#addBankForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route(get_route_prefix() . 'banks.store-ajax') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Add to all bank selects on the page
                            let newOption = `<option value="${response.bank.id}">${response.bank.name}</option>`;
                            $('.bank-select').append(newOption);

                            $('#addBankModal').modal('hide');
                            $('#addBankForm')[0].reset();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding bank. Please check inputs.');
                    }
                });
            });

            $('#addBranchForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route(get_route_prefix() . 'banks.store-branch-ajax') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success && activeBranchContext) {
                            const branchSelect = activeBranchContext.branchSelect;
                            const branch = response.branch;

                            if (!branchSelect.find(`option[value="${branch.id}"]`).length) {
                                branchSelect.append(`<option value="${branch.id}">${branch.name}</option>`);
                            }

                            branchSelect.val(String(branch.id)).trigger('change');
                            $('#addBranchModal').modal('hide');
                            $('#addBranchForm')[0].reset();
                            $('#branch_modal_bank_id').val('');
                            $('#branch_modal_bank_name').val('');
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error adding branch. Please check inputs.');
                    }
                });
            });
        });
    </script>
@stop
