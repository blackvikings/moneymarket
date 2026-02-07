@extends('adminlte::page')

@section('title', 'RTO Leads')

@section('content_header')
    <h1>RTO Leads</h1>
@stop

@section('content')
    <style>
        /* Custom Tab Design */
        .rto-tabs-wrapper {
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
        .rto-tab-container {
            position: relative;
            flex: 1 1 0%;
            min-width: 120px;
            margin-left: -1rem;
            z-index: 1;
        }
        .rto-tab-container:first-child {
            margin-left: 0;
            z-index: 0;
        }
        /* Ensure stacking context for overlap */
        .rto-tab-container:nth-child(1) { z-index: 1; }
        .rto-tab-container:nth-child(2) { z-index: 2; }
        .rto-tab-container:nth-child(3) { z-index: 3; }
        .rto-tab-container:nth-child(4) { z-index: 4; }
        .rto-tab-container:nth-child(5) { z-index: 5; }
        .rto-tab-container:nth-child(6) { z-index: 6; }
        .rto-tab-container:nth-child(7) { z-index: 7; }
        .rto-tab-container:nth-child(8) { z-index: 8; }

        .rto-tab-content {
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
            border: 1px solid #d1d5db; /* Added border */
            border-right: none; /* Remove right border to avoid double borders with overlap */
        }

        .rto-tab-container:last-child .rto-tab-content {
            border-right: 1px solid #d1d5db;
        }

        .rto-tab-content:hover {
            background-color: #eff6ff;
        }

        .rto-tab-content.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .rto-tab-content.active .rto-tab-subtitle,
        .rto-tab-content.active .rto-tab-title {
            color: white;
        }

        .rto-tab-content.active .rto-tab-subtitle {
            opacity: 0.8;
        }

        .rto-tab-subtitle {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
        }

        .rto-tab-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e3a8a;
        }

        /* Clip Path */
        .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%, 15px 50%);
        }
        .rto-tab-container:first-child .clip-chevron {
            clip-path: polygon(0% 0%, calc(100% - 15px) 0%, 100% 50%, calc(100% - 15px) 100%, 0% 100%);
        }

        .error {
            color: red;
            font-size: 0.875em;
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

    <div class="row mb-3">
        <div class="col-md-12 text-right">
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route(get_route_prefix() . 'rto.export', request()->all()) }}" class="btn btn-success btn-sm mr-1">Export</a>
            @endif
            <a href="{{ route(get_route_prefix() . 'rto.create') }}" class="btn btn-success btn-sm mr-1">Add New RTO Lead</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-2">
            <div class="rto-tabs-wrapper mb-0">
                {{-- All Tab --}}
                <div class="rto-tab-container">
                    <a href="{{ route(get_route_prefix() . 'rto.index') }}" class="rto-tab-content clip-chevron {{ request('stage') == '' ? 'active' : '' }}">
                        <span class="rto-tab-subtitle">View</span>
                        <span class="rto-tab-title">{{ $counts['All'] }}</span>
                        <span class="upper-line"></span>
                    </a>
                </div>

                {{-- Stages Tabs --}}
                @foreach($stages as $stage)
                    <div class="rto-tab-container">
                        <a href="{{ route(get_route_prefix() . 'rto.index', ['stage' => $stage]) }}" class="rto-tab-content clip-chevron {{ request('stage') == $stage ? 'active' : '' }}">
                            <span class="rto-tab-subtitle">{{ $stage }}</span>
                            <span class="rto-tab-title">{{ $counts[$stage] ?? 0 }}</span>
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
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>RTO Stage</th>
                        <th>Aging</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>{{ $lead->contact_number }}</td>
                            <td><span class="badge badge-info">{{ $lead->rto_stage ?? 'New' }}</span></td>
                            <td>{{ $lead->rto_aging }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-rto-btn" data-id="{{ $lead->id }}">View</button>
                                @if($lead->rto_stage !== 'Loan Return')
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actionModal{{ $lead->id }}">
                                        Action
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="actionModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel{{ $lead->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="actionModalLabel{{ $lead->id }}">Action for {{ $lead->customer_name }} ({{ $lead->rto_stage }})</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($lead->rto_stage == 'New')
                                                        <form action="{{ route(get_route_prefix() . 'rto.update-stage', $lead->id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="current_stage" value="{{ $lead->rto_stage }}">
                                                            <p>Move to next stage?</p>
                                                            <button type="submit" name="action" value="Yes" class="btn btn-success">Yes, Move to Checking</button>
                                                        </form>
                                                    @elseif($lead->rto_stage == 'Checking')
                                                        @include('rto.partials.checking_form', ['lead' => $lead, 'banks' => \App\Models\Bank::where('is_active', true)->get()])
                                                    @elseif($lead->rto_stage == 'RTO Done')
                                                        @include('rto.partials.rto_done_form', ['lead' => $lead])
                                                    @else
                                                        <p class="text-success">This lead has completed the RTO process.</p>
                                                    @endif

                                                    @if($lead->rto_stage !== 'Loan Return' && $lead->rto_stage !== 'RTO Done')
                                                        <hr>
                                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="collapse" data-target="#rejectCollapse{{ $lead->id }}">Reject Loan</button>
                                                        <div id="rejectCollapse{{ $lead->id }}" class="collapse mt-2">
                                                            <form action="{{ route(get_route_prefix() . 'rto.reject-lead', $lead->id) }}" method="POST">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <label>Reason for Rejection</label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="2" required></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-danger btn-sm">Confirm Reject</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- AJAX View Modal ---
            $('.view-rto-btn').click(function() {
                let leadId = $(this).data('id');
                let url = '{{ route(get_route_prefix() . "rto.show", ":id") }}'.replace(':id', leadId);

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

            // Initialize validation for all forms
            $('.rto-checking-form').each(function() {
                $(this).validate();
            });
            $('.rto-loan-return-form').each(function() {
                $(this).validate();
            });
            $('.rto-vehicle-detail-form').each(function() {
                $(this).validate();
            });
            $('.rto-insurance-form').each(function() {
                $(this).validate();
            });

            // Toggle logic for Hypothecation
            $(document).on('change', 'input[name="rto_hypothecation_status"]', function() {
                var bankDiv = $(this).closest('form').find('select[name="rto_hypothecation_bank_id"]').closest('.form-group');
                if($(this).is(':checked')) {
                    bankDiv.show();
                } else {
                    bankDiv.hide();
                }
            });

            // Toggle logic for Vehicle Type
            $(document).on('change', '.rto-vehicle-type-select', function() {
                var form = $(this).closest('form');
                var container = form.find('.old-vehicle-details-section');

                if ($(this).val() === 'Old Vehicle') {
                    container.show();
                } else {
                    container.hide();
                }
            });

            // Add Closer Deposit Row
            $(document).on('click', '.add-closer-deposit', function() {
                var container = $(this).closest('form').find('.closer-deposits-container');
                var index = container.find('.closer-deposit-row').length;
                var html = `
                    <div class="row closer-deposit-row">
                        <div class="col-md-5"><div class="form-group"><input type="number" step="0.01" name="closer_deposits[${index}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-5"><div class="form-group"><input type="date" name="closer_deposits[${index}][date]" class="form-control"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                container.append(html);
            });

            // Add Challan Row
            $(document).on('click', '.add-challan', function() {
                var container = $(this).closest('form').find('.challans-container');
                var index = container.find('.challan-row').length;
                var html = `
                    <div class="row challan-row">
                        <div class="col-md-4"><div class="form-group"><input type="text" name="challans[${index}][type]" class="form-control" placeholder="Challan Type"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="date" name="challans[${index}][date]" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="number" step="0.01" name="challans[${index}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                container.append(html);
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.row').remove();
            });

            // Toggle RTO remark fields based on checkbox state
            $(document).on('change', '.rto-status-checkbox', function() {
                var remarkTargetId = $(this).data('remark-target');
                if ($(this).is(':checked')) {
                    $(remarkTargetId).show();
                } else {
                    $(remarkTargetId).hide();
                }
            });

            // Initial state for RTO remark fields when modal opens
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.rto-status-checkbox').each(function() {
                    var remarkTargetId = $(this).data('remark-target');
                    if ($(this).is(':checked')) {
                        $(remarkTargetId).show();
                    } else {
                        $(remarkTargetId).hide();
                    }
                });
            });
        });
    </script>
@stop
