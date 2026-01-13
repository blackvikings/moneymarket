@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Leads</h1>
        <div>
            @can('create lead')
                @if(auth()->user()->hasRole('admin'))
                    <button type="button" class="btn btn-info mr-1" data-toggle="modal" data-target="#importModal">Import Leads</button>
                @endif
                <a href="{{ route(get_route_prefix() . 'leads.create') }}" class="btn btn-success">Add Lead</a>
            @endcan
        </div>
    </div>
@stop

@section('content')
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

    </style>

    <div class="card mb-3">
        <div class="card-body p-2">
            <form action="{{ route(get_route_prefix() . 'leads.index') }}" method="GET" class="form-inline">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Start Date:</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">End Date:</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route(get_route_prefix() . 'leads.index', ['status' => request('status')]) }}" class="btn btn-secondary ml-2">Reset</a>
                @if(auth()->user()->hasRole('admin'))
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
                    <a href="{{ route(get_route_prefix() . 'leads.index', array_merge(request()->except('status'), ['status' => ''])) }}" class="leads-tab-content clip-chevron {{ request('status') == '' ? 'active' : '' }}">
                        <span class="leads-tab-title">All</span>
                        <span class="leads-tab-subtitle">{{ $counts['All'] ?? 0 }}</span>
                        <span class="upper-line"></span>
                    </a>
                </div>

                {{-- Statuses Tabs --}}
                @foreach($statuses as $status)
                    <div class="leads-tab-container">
                        <a href="{{ route(get_route_prefix() . 'leads.index', array_merge(request()->except('status'), ['status' => $status])) }}" class="leads-tab-content clip-chevron {{ request('status') == $status ? 'active' : '' }}">
                            <span class="leads-tab-title">{{ $status == 'New' ? 'New Task' : str_replace('_', ' ', $status) }}</span>
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
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Created At</th>
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
                            <td>{{ $lead->created_at->format('d-m-Y') }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td>
                                <span class="badge badge-{{ $lead->status == 'Lead' ? 'success' : ($lead->status == 'Rejected' || $lead->status == 'Not_Active' ? 'danger' : 'info') }}">
                                    {{ str_replace('_', ' ', $lead->status) }}
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
                                <a href="{{ route(get_route_prefix() . 'leads.show', $lead->id) }}" class="btn btn-info btn-sm">View</a>
                                @can('edit lead')
                                    <a href="{{ route(get_route_prefix() . 'leads.edit', $lead->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endcan
                                @if($lead->status !== 'Rejected' && $lead->status !== 'Not_Active')
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actionModal{{ $lead->id }}">
                                        Action
                                    </button>
                                @endif
                                @can('delete lead')
                                    <form action="{{ route(get_route_prefix() . 'leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endcan

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
                                                            <select name="bank_id" class="form-control bank-select" required>
                                                                <option value="">Select Bank</option>
                                                                @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                                                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Assign Branch <span class="text-danger">*</span></label>
                                                            <select name="bank_branch_id" class="form-control branch-select" required>
                                                                <option value="">Select Bank First</option>
                                                            </select>
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
                                                            </div>

                                                            <div class="action-fields" id="reject-fields-{{ $lead->id }}" style="display: none;">
                                                                <div class="form-group">
                                                                    <label>Remark <span class="text-danger">*</span></label>
                                                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>

                                                        @elseif($lead->status == 'Lead')
                                                            <div class="form-group">
                                                                <label>Select Action:</label><br>
                                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                    <label class="btn btn-outline-info active">
                                                                        <input type="radio" name="action_radio" value="follow_up" data-lead-id="{{ $lead->id }}" checked> Follow-up
                                                                    </label>
                                                                    <label class="btn btn-outline-primary">
                                                                        <input type="radio" name="action_radio" value="proceed_to_bank_assign" data-lead-id="{{ $lead->id }}"> Proceed to Bank Assign
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
                                                            </div>

                                                            <div class="action-fields" id="reject-fields-{{ $lead->id }}" style="display: none;">
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
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    @if(auth()->user()->hasRole('admin'))
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
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Function to handle radio change within a specific modal
            function handleRadioChange(modal) {
                let checkedRadio = modal.find('input[name="action_radio"]:checked');
                let action = checkedRadio.val();
                let leadId = checkedRadio.data('lead-id');

                // Hide all conditional sections first
                modal.find('.action-fields').hide();
                modal.find('.action-fields input, .action-fields select, .action-fields textarea').prop('required', false);

                // Show the relevant section based on action
                if (action === 'not_interested' || action === 'follow_up' || action === 'qualify' || action === 'reject') {
                    let sectionId = '#' + action + '-fields-' + leadId;
                    let sectionToShow = modal.find(sectionId);

                    if (sectionToShow.length > 0) {
                        sectionToShow.show();
                        sectionToShow.find('input, select, textarea').prop('required', true);
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
                handleRadioChange($(this));
            });

            $('.action-form').submit(function(e) {
                let modal = $(this).closest('.modal');
                let selectedAction = modal.find('input[name="action_radio"]:checked').val();
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
        });
    </script>
@stop
