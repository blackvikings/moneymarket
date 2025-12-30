@extends('adminlte::page')

@section('title', 'Leads')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Leads</h1>
        @can('create lead')
            <a href="{{ route('leads.create') }}" class="btn btn-success">Add Lead</a>
        @endcan
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
    </style>

    <div class="card">
        <div class="card-body p-2">
            <div class="leads-tabs-wrapper mb-0">
                {{-- All Tab --}}
                <div class="leads-tab-container">
                    <a href="{{ route('leads.index') }}" class="leads-tab-content clip-chevron {{ request('status') == '' ? 'active' : '' }}">
                        <span class="leads-tab-title">All</span>
                        <span class="leads-tab-subtitle">{{ $counts['All'] ?? 0 }}</span>
                    </a>
                </div>

                {{-- Statuses Tabs --}}
                @foreach($statuses as $status)
                    <div class="leads-tab-container">
                        <a href="{{ route('leads.index', ['status' => $status]) }}" class="leads-tab-content clip-chevron {{ request('status') == $status ? 'active' : '' }}">
                            <span class="leads-tab-title">{{ str_replace('_', ' ', $status) }}</span>
                            <span class="leads-tab-subtitle">{{ $counts[$status] ?? 0 }}</span>
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
            <table class="table table-bordered table-hover">
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
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info btn-sm">View</a>
                                @can('edit lead')
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endcan
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actionModal{{ $lead->id }}">
                                    Action
                                </button>
                                @can('delete lead')
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
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
                                                <h5 class="modal-title">Update Status for {{ $lead->customer_name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('leads.update-status', $lead->id) }}" method="POST">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label>Change Status</label>
                                                        <select name="status" class="form-control status-select" data-lead-id="{{ $lead->id }}" required>
                                                            <option value="">Select Status</option>
                                                            <option value="New" {{ $lead->status == 'New' ? 'selected' : '' }}>New</option>
                                                            <option value="Prospect" {{ $lead->status == 'Prospect' ? 'selected' : '' }}>Prospect</option>
                                                            <option value="Lead" {{ $lead->status == 'Lead' ? 'selected' : '' }}>Lead (Qualified)</option>
                                                            <option value="Rejected" {{ $lead->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                            <option value="Not_Active" {{ $lead->status == 'Not_Active' ? 'selected' : '' }}>Not Active</option>
                                                        </select>
                                                    </div>

                                                    <div class="follow-up-section" style="display: none;">
                                                        <div class="form-group">
                                                            <label>Follow Up Date</label>
                                                            <input type="date" name="follow_up_date" class="form-control" value="{{ $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Follow Up Time</label>
                                                            <input type="time" name="follow_up_time" class="form-control" value="{{ $lead->follow_up_time }}">
                                                        </div>
                                                    </div>

                                                    <div class="bank-section" style="display: none;">
                                                        <div class="form-group">
                                                            <label>Assign Bank</label>
                                                            <select name="bank_id" class="form-control bank-select" data-lead-id="{{ $lead->id }}">
                                                                <option value="">Select Bank</option>
                                                                @foreach(\App\Models\Bank::where('is_active', true)->get() as $bank)
                                                                    <option value="{{ $bank->id }}" {{ $lead->bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Assign Branch</label>
                                                            <select name="bank_branch_id" class="form-control branch-select">
                                                                <option value="">Select Branch</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Remark</label>
                                                        <textarea name="remark" class="form-control" rows="3">{{ $lead->remark }}</textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-success">Update Status</button>
                                                </form>
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
@stop

@section('js')
    <script>
        $(document).ready(function() {
            function toggleFields(selectElement) {
                let status = $(selectElement).val();
                let modal = $(selectElement).closest('.modal');
                modal.find('.follow-up-section').toggle(status === 'New' || status === 'Prospect');
                modal.find('.bank-section').toggle(status === 'Lead');
            }

            function fetchBranches(bankSelect, selectedBranchId = null) {
                let bankId = $(bankSelect).val();
                let modal = $(bankSelect).closest('.modal');
                let branchSelect = modal.find('.branch-select');
                branchSelect.empty().append('<option value="">Loading...</option>');

                if (bankId) {
                    $.ajax({
                        url: '{{ url("get-branches") }}/' + bankId,
                        type: 'GET',
                        success: function(data) {
                            branchSelect.empty().append('<option value="">Select Branch</option>');
                            $.each(data, function(key, branch) {
                                let option = $('<option></option>').attr('value', branch.id).text(branch.name);
                                if (branch.id == selectedBranchId) {
                                    option.prop('selected', true);
                                }
                                branchSelect.append(option);
                            });
                        }
                    });
                } else {
                    branchSelect.empty().append('<option value="">Select Bank First</option>');
                }
            }

            $('.status-select').each(function() {
                toggleFields(this);
            });

            $('.status-select').change(function() {
                toggleFields(this);
            });

            $('.bank-select').each(function() {
                let leadId = $(this).data('lead-id');
                let lead = @json($leads->keyBy('id'));
                if (lead[leadId] && lead[leadId].bank_id) {
                    fetchBranches(this, lead[leadId].bank_branch_id);
                }
            });

            $('.bank-select').change(function() {
                fetchBranches(this);
            });
        });
    </script>
@stop
