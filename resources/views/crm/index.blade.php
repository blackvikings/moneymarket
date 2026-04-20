@extends('adminlte::page')

@section('title', 'CRM - Bank Assigned Leads')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>CRM - Bank Assigned Leads</h1>
        @if(auth()->user()->hasRole('admin') || auth()->user()->can('export crm'))
            <a href="{{ route(get_route_prefix() . 'crm.export', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export
            </a>
        @endif
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
        .icon-action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .select2-container {
            width: 100% !important;
        }
        .move-to-rto-modal .modal-dialog {
            max-width: 560px;
        }
        .move-to-rto-modal .modal-content {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18);
        }
        .move-to-rto-modal .modal-header {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #fff;
            border-bottom: 0;
            padding: 1rem 1.25rem;
        }
        .move-to-rto-modal .modal-header .close {
            color: #fff;
            opacity: 1;
            text-shadow: none;
        }
        .move-to-rto-modal .modal-body {
            padding: 1.25rem;
            background: #fff;
        }
        .move-to-rto-modal .modal-footer {
            border-top: 1px solid #e5e7eb;
            padding: 1rem 1.25rem;
            background: #fff;
            gap: 0.5rem;
        }
        .move-to-rto-modal .form-group {
            margin-bottom: 0;
        }
        .move-to-rto-modal .form-control {
            min-height: 44px;
        }
        .move-to-rto-modal .select2-container--default .select2-selection--single {
            min-height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 6px 12px;
        }
        .move-to-rto-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            color: #111827;
            padding-left: 0;
        }
        .move-to-rto-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 8px;
        }
        .move-to-rto-modal .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
        }
        .move-to-rto-modal .select2-container--open {
            z-index: 99999;
        }
        .move-to-insurance-modal .modal-dialog {
            max-width: 560px;
        }
        .move-to-insurance-modal .modal-content {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18);
        }
        .move-to-insurance-modal .modal-header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border-bottom: 0;
            padding: 1rem 1.25rem;
        }
        .move-to-insurance-modal .modal-header .close {
            color: #fff;
            opacity: 1;
            text-shadow: none;
        }
        .move-to-insurance-modal .modal-body {
            padding: 1.25rem;
            background: #fff;
        }
        .move-to-insurance-modal .modal-footer {
            border-top: 1px solid #e5e7eb;
            padding: 1rem 1.25rem;
            background: #fff;
            gap: 0.5rem;
        }
        .move-to-insurance-modal .form-group {
            margin-bottom: 0;
        }
        .move-to-insurance-modal .form-control {
            min-height: 44px;
        }
        .move-to-insurance-modal .select2-container--default .select2-selection--single {
            min-height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 6px 12px;
        }
        .move-to-insurance-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            color: #111827;
            padding-left: 0;
        }
        .move-to-insurance-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 8px;
        }
        .move-to-insurance-modal .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
        }
        .move-to-insurance-modal .select2-container--open {
            z-index: 99999;
        }
        .move-to-rto-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
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
                    @if($stage === 'Payment Release Detail')
                        @continue
                    @endif
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
                        <th>Ageing</th>
                        <th>City</th>
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
                            <td>{{ $lead->converted_at ? $lead->converted_at->format('d-m-Y') : $lead->created_at->format('d-m-Y') }}</td>
                            <td>{{ $lead->ageing }}</td>
                            <td>{{ $lead->currentCity->name ?? '-' }}</td>
                             <td>
                                 <div class="move-to-rto-actions">
                                     <button type="button" class="btn btn-info btn-sm icon-action-btn view-crm-btn" data-id="{{ $lead->id }}" title="View Lead" aria-label="View Lead"><i class="fas fa-eye"></i></button>
                                     <a href="{{ route(get_route_prefix() . 'leads.full-details', $lead->id) }}" class="btn btn-secondary btn-sm icon-action-btn" title="Full Details" aria-label="Full Details"><i class="fas fa-file-alt"></i></a>
                                     @if($lead->status === 'Bank Assign')
                                         <button type="button" class="btn btn-warning btn-sm icon-action-btn" data-toggle="modal" data-target="#moveToRtoModal{{ $lead->id }}" title="Move to RTO" aria-label="Move to RTO">
                                             <i class="fas fa-share"></i>
                                         </button>
                                         <button type="button" class="btn btn-success btn-sm icon-action-btn" data-toggle="modal" data-target="#moveToInsuranceModal{{ $lead->id }}" title="Move to Insurance" aria-label="Move to Insurance">
                                             <i class="fas fa-shield-alt"></i>
                                         </button>
                                     @endif
                                     @if($lead->crm_stage !== 'Disbursal')
                                         <button type="button" class="btn btn-primary btn-sm icon-action-btn" data-toggle="modal" data-target="#actionModal{{ $lead->id }}" title="Update Stage" aria-label="Update Stage">
                                             <i class="fas fa-ellipsis-h"></i>
                                         </button>
                                     @else
                                     @if($lead->rto_stage && (auth()->user()->hasRole('admin') || auth()->user()->can('view rto')))
                                         <button type="button"
                                                 class="btn btn-warning btn-sm icon-action-btn view-rto-btn"
                                                 data-url="{{ route(get_route_prefix() . 'rto.show', $lead->id) }}">
                                             <i class="fas fa-car-side"></i>
                                         </button>
                                     @endif
                                     @if($lead->rto_stage === 'New') <span class="text-success small">Moved to RTO</span> @endif
                                     @if($lead->insurance_lead_stage === 'New') <span class="text-success small">Moved to Insurance</span> @endif
                                     @endif
                                 </div>

                                 @if($lead->status === 'Bank Assign')
                                 <div class="modal fade move-to-rto-modal" id="moveToRtoModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Move to RTO: {{ $lead->customer_name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" class="move-to-rto-form" data-confirm-message="Move this lead to RTO?">
                                                @csrf
                                                <input type="hidden" name="action" value="move_to_rto">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <div class="text-muted small">Bank Assign lead ko RTO me move karne ke liye ek team member select karein.</div>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold mb-2">Assign RTO Team Member <span class="text-danger">*</span></label>
                                                        <select name="rto_assigned_to" class="form-control rto-team-select" data-placeholder="Search employee..."></select>
                                                        <small class="text-muted d-block mt-2">Type employee name or ID to search.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning text-white">Move to RTO</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                 </div>
                                 @endif

                                 @if($lead->status === 'Bank Assign')
                                 <div class="modal fade move-to-insurance-modal" id="moveToInsuranceModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Move to Insurance: {{ $lead->customer_name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" class="move-to-insurance-form" data-confirm-message="Move this lead to Insurance?">
                                                @csrf
                                                <input type="hidden" name="action" value="move_to_insurance">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <div class="text-muted small">Bank Assign lead ko Insurance module me move karne ke liye ek team member select karein.</div>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold mb-2">Assign Insurance Team Member <span class="text-danger">*</span></label>
                                                        <select name="insurance_assigned_to" class="form-control insurance-team-select" data-placeholder="Search employee..."></select>
                                                        <small class="text-muted d-block mt-2">Type employee name or ID to search.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Move to Insurance</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                 </div>
                                 @endif

                                 <!-- Action Modal -->
                                 <div class="modal fade" id="actionModal{{ $lead->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Stage: {{ $lead->crm_stage }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <form action="{{ route(get_route_prefix() . 'crm.update-stage', $lead->id) }}" method="POST" id="updateStageForm{{ $lead->id }}" data-confirm-message="Update the CRM stage for this lead?">
                                                @csrf
                                                {{-- Removed: <input type="hidden" name="current_stage" value="{{ $lead->crm_stage }}"> --}}

                                                @php
                                                    $allStages = ['New', 'Login', 'FI Document', 'Vehicle Evaluation', 'Credit', 'Approved', 'Agreement', 'Disbursal'];
                                                    $currentIndex = array_search($lead->crm_stage, $allStages);
                                                    if ($currentIndex === false) $currentIndex = -1;
                                                @endphp

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Stage Progress</label>
                                                        @foreach($allStages as $index => $stage)
                                                            @if($stage === 'Agreement' && $lead->crm_stage !== 'Approved') @continue @endif
                                                            @if($stage === 'Disbursal' && $lead->crm_stage !== 'Approved' && $lead->crm_stage !== 'Agreement') @continue @endif

                                                            <div class="form-check">
                                                                <input class="form-check-input stage-checkbox" type="checkbox"
                                                                    name="completed_stages[]"
                                                                    value="{{ $stage }}"
                                                                    {{ $index <= $currentIndex ? 'checked disabled' : '' }}
                                                                    data-lead-id="{{ $lead->id }}"
                                                                    @if($stage === 'Disbursal') data-total-loan-amount="{{ $lead->total_loan_amount }}" @endif>
                                                                <label class="form-check-label">{{ $stage }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div id="disbursal_fields_{{ $lead->id }}" style="display: none;">
                                                        <hr>
                                                        <h5>Disbursal Information</h5>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Disbursal Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="disbursal_date" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Finance Amount <span class="text-danger">*</span></label>
                                                                    <input type="number" step="0.01" name="finance_amount" class="form-control" value="{{ $lead->finance_amount ?? $lead->total_loan_amount }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Disbursal Amount <span class="text-danger">*</span></label>
                                                                    <input type="number" step="0.01" name="disbursal_amount" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" name="move_to_rto" value="yes">
                                                                    <label class="form-check-label">Move to RTO</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input move-to-insurance-check" name="move_to_insurance" value="yes" data-target="#insurance_expiry_container_{{ $lead->id }}">
                                                                    <label class="form-check-label">Move to Insurance</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2 d-none" id="insurance_expiry_container_{{ $lead->id }}">
                                                            <div class="col-md-6 offset-md-6">
                                                                <div class="form-group">
                                                                    <label>Insurance Expiry Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="insurance_expiry_date" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <p class="text-muted small">Select stages to complete. You can select multiple stages at once.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Update</button> {{-- Removed name="action" value="Yes" --}}
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No leads found in this CRM stage.</td>
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
    <script>
        $(document).ready(function() {
            console.log("CRM index.blade.php script loaded.");

             $('.view-crm-btn').click(function() {
                 let leadId = $(this).data('id');
                 let url = '{{ route(get_route_prefix() . "crm.show", ":id") }}'.replace(':id', leadId);

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

            $('.view-rto-btn').click(function() {
                let url = $(this).data('url');
                if (!url) return;

                $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading RTO flow...</p></div>');
                $('#globalViewModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#globalViewModalContent').html(response);
                    },
                    error: function() {
                        $('#globalViewModalContent').html('<div class="alert alert-danger m-3">Failed to load RTO lead flow.</div>');
                    }
                });
            });

            function initRtoTeamSelects(context) {
                $(context).find('.rto-team-select').each(function() {
                    const select = $(this);

                    if (select.data('select2')) {
                        return;
                    }

                    const modal = select.closest('.modal');

                    select.select2({
                        width: '100%',
                        placeholder: select.data('placeholder') || 'Search employee...',
                        minimumInputLength: 1,
                        dropdownParent: modal.length ? modal : $(document.body),
                        ajax: {
                            url: "{{ route(get_route_prefix() . 'crm.search-employees') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return { q: params.term || '' };
                            },
                            processResults: function(data) {
                                return { results: data };
                            },
                            cache: true
                        }
                    });

                    select.on('select2:open', function() {
                        const search = $('.select2-container--open .select2-search__field');
                        if (search.length) {
                            search[0].focus();
                        }
                    });
                });
            }

            function initInsuranceTeamSelects(context) {
                $(context).find('.insurance-team-select').each(function() {
                    const select = $(this);

                    if (select.data('select2')) {
                        return;
                    }

                    const modal = select.closest('.modal');

                    select.select2({
                        width: '100%',
                        placeholder: select.data('placeholder') || 'Search employee...',
                        minimumInputLength: 1,
                        dropdownParent: modal.length ? modal : $(document.body),
                        ajax: {
                            url: "{{ route(get_route_prefix() . 'crm.search-employees') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return { q: params.term || '' };
                            },
                            processResults: function(data) {
                                return { results: data };
                            },
                            cache: true
                        }
                    });

                    select.on('select2:open', function() {
                        const search = $('.select2-container--open .select2-search__field');
                        if (search.length) {
                            search[0].focus();
                        }
                    });
                });
            }

            $(document).on('shown.bs.modal', '[id^="moveToRtoModal"]', function() {
                initRtoTeamSelects(this);
            });

            $(document).on('shown.bs.modal', '[id^="moveToInsuranceModal"]', function() {
                initInsuranceTeamSelects(this);
            });

             // Attach submit handler to each form dynamically
             $('[id^="actionModal"]').on('shown.bs.modal', function () {
                 let leadId = $(this).attr('id').replace('actionModal', '');
                 let form = $('#updateStageForm' + leadId);

                form.off('submit').on('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission

                    // This form submits via AJAX, so native HTML5 required validation won't block submission by default.
                    // Enforce it here so fields like Finance Amount* are actually required before the request is sent.
                    let formEl = form.get(0);
                    if (formEl && formEl.checkValidity && !formEl.checkValidity()) {
                        if (formEl.reportValidity) formEl.reportValidity();
                        return;
                    }

                    console.log("Form submission intercepted for lead ID:", leadId);
                    console.log("Form action URL:", form.attr('action'));
                    console.log("Form method:", form.attr('method'));
                    console.log("Form data:", form.serialize());

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Ensure CSRF token is sent
                            'Accept': 'application/json' // Request JSON response
                        },
                        success: function(response) {
                            console.log("AJAX Success:", response);
                            // Close the modal after successful submission
                            $('#actionModal' + leadId).modal('hide');

                            location.reload()
                            // Optionally, display a success message or update the UI without full reload
                            // For now, we'll just log and close the modal.
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            // Display error message to the user
                            alert('Error updating stage: ' + xhr.responseText);
                        }
                    });
                });

                // Ensure Disbursal section visibility is correct when the modal opens.
                syncDisbursalFields(form, leadId);
            });


            function syncDisbursalFields(form, leadId) {
                if (!form || !form.length || !leadId) return;

                let disbursalFields = $('#disbursal_fields_' + leadId);
                if (!disbursalFields.length) return;

                let disbursalCheckbox = form.find('input.stage-checkbox[value="Disbursal"]');
                let isDisbursalChecked = disbursalCheckbox.length && disbursalCheckbox.is(':checked');

                // Always set the form action to update-stage, as the backend handles the logic.
                form.attr('action', '{{ route(get_route_prefix() . "crm.update-stage", ["lead" => ":id"]) }}'.replace(':id', leadId));

                if (isDisbursalChecked) {
                    disbursalFields.show();
                    disbursalFields.find('input[name="disbursal_date"], input[name="finance_amount"], input[name="disbursal_amount"]').prop('required', true);

                    let totalLoanAmount = disbursalCheckbox.data('total-loan-amount');
                    let financeAmountInput = disbursalFields.find('input[name="finance_amount"]');
                    let hasFinanceAmount = financeAmountInput.val() !== undefined && financeAmountInput.val() !== null && financeAmountInput.val() !== '';
                    let hasTotalLoanAmount = totalLoanAmount !== undefined && totalLoanAmount !== null && totalLoanAmount !== '';
                    if (!hasFinanceAmount && hasTotalLoanAmount) {
                        financeAmountInput.val(totalLoanAmount);
                    }
                } else {
                    disbursalFields.hide();
                    disbursalFields.find('input').prop('required', false);
                }
            }

            // Delegated handlers so it works reliably inside Bootstrap modals.
            $(document).on('change', '.stage-checkbox', function() {
                let leadId = $(this).data('lead-id');
                let form = $(this).closest('form');
                syncDisbursalFields(form, leadId);
            });

            $(document).on('change', '.move-to-insurance-check', function() {
                let target = $(this).data('target');
                if ($(this).is(':checked')) {
                    $(target).removeClass('d-none');
                    $(target).find('input').prop('required', true);
                } else {
                    $(target).addClass('d-none');
                    $(target).find('input').prop('required', false);
                }
            });

            $(document).on('submit', '.move-to-rto-form', function(event) {
                event.preventDefault();

                const form = $(this);
                const modal = form.closest('.modal');
                const leadId = modal.attr('id').replace('moveToRtoModal', '');
                const formEl = form.get(0);

                if (formEl && formEl.checkValidity && !formEl.checkValidity()) {
                    if (formEl.reportValidity) formEl.reportValidity();
                    return;
                }

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $('#moveToRtoModal' + leadId).modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error moving lead to RTO: ' + (xhr.responseJSON?.message || xhr.responseText));
                    }
                });
            });

            $(document).on('submit', '.move-to-insurance-form', function(event) {
                event.preventDefault();

                const form = $(this);
                const modal = form.closest('.modal');
                const leadId = modal.attr('id').replace('moveToInsuranceModal', '');
                const formEl = form.get(0);

                if (formEl && formEl.checkValidity && !formEl.checkValidity()) {
                    if (formEl.reportValidity) formEl.reportValidity();
                    return;
                }

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $('#moveToInsuranceModal' + leadId).modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error moving lead to Insurance: ' + (xhr.responseJSON?.message || xhr.responseText));
                    }
                });
            });

            $(document).on('submit', 'form[data-confirm-message]', function(event) {
                const message = $(this).data('confirm-message');
                if (!message) {
                    return;
                }

                if (!window.confirm(message)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                }
            });
        });
    </script>
@stop
