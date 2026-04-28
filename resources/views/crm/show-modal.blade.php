<style>
    .crm-timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }
    .crm-timeline:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e9ecef;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
    .crm-timeline > li {
        position: relative;
        margin-right: 10px;
        margin-bottom: 15px;
    }
    .crm-timeline > li > .fa,
    .crm-timeline > li > .fas,
    .crm-timeline > li > .far {
        width: 30px;
        height: 30px;
        font-size: 12px;
        line-height: 30px;
        position: absolute;
        color: #fff;
        background: #adb5bd;
        border-radius: 50%;
        text-align: center;
        left: 18px;
        top: 0;
    }
    .crm-timeline > li > .timeline-item {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        color: #495057;
        margin-left: 60px;
        margin-right: 15px;
        padding: 0;
        position: relative;
    }
    .crm-timeline > li > .timeline-item > .time {
        color: #999;
        float: right;
        padding: 10px;
        font-size: 12px;
    }
    .crm-timeline > li > .timeline-item > .timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 10px;
        font-size: 14px;
        line-height: 1.1;
    }
    .bg-blue { background-color: #007bff !important; }
    .bg-green { background-color: #28a745 !important; }
</style>

<div class="modal-header">
    <h5 class="modal-title">CRM Lead Details: {{ $lead->customer_name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Customer Information</strong></h6>
            <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
            <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
            <p><strong>Firm:</strong> {{ $lead->firm ? $lead->firm->name : '-' }}</p>
            <p><strong>CRM Stage:</strong> <span class="badge badge-primary">{{ $lead->crm_stage ?? 'New' }}</span></p>
        </div>
        <div class="col-md-6">
            <h6><strong>Bank Details</strong></h6>
            <p><strong>Bank:</strong> {{ $lead->bank ? $lead->bank->name : '-' }}</p>
            <p><strong>Branch:</strong> {{ $lead->bankBranch ? $lead->bankBranch->name : '-' }}</p>
            <p><strong>Assigned Date:</strong> {{ $lead->converted_at ? $lead->converted_at->format('d-m-Y') : '-' }}</p>
        </div>
    </div>

    <hr>
    <h6><strong>Journey Timeline</strong></h6>
    <div class="row">
        <div class="col-md-12">
            <ul class="crm-timeline">
                @php
                    $crmStages = [
                        'New' => 'crm_new_at',
                        'Login' => 'crm_login_at',
                        'FI Document' => 'crm_fi_document_at',
                        'Vehicle Evaluation' => 'crm_vehicle_evaluation_at',
                        'Credit' => 'crm_credit_at',
                        'Approved' => 'crm_approved_at',
                        'Agreement' => 'crm_agreement_at',
                        'Disbursal' => 'crm_disbursal_at'
                    ];
                @endphp
                @foreach($crmStages as $stage => $column)
                    @if($lead->$column)
                        <li>
                            <i class="fas fa-check bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> {{ $lead->$column->format('d-m-Y H:i') }}</span>
                                <h3 class="timeline-header"><strong>{{ $stage }}</strong> completed</h3>
                            </div>
                        </li>
                    @endif
                @endforeach

                {{-- Only show the "In Progress" clock if Disbursal is not yet completed --}}
                @if(!$lead->crm_disbursal_at)
                    <li>
                        <i class="far fa-clock bg-gray"></i>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <hr>
    <h6><strong>Vehicle Details</strong></h6>
    <div class="row">
        <div class="col-md-12">
            @if($lead->vehicle_purchase_type == 'New Purchase')
                <p><strong>Manufacturer:</strong> {{ $lead->manufacture ? $lead->manufacture->name : '-' }}</p>
                <p><strong>Vehicle Name:</strong> {{ $lead->vehicleName ? $lead->vehicleName->name : '-' }}</p>
                <p><strong>Variant:</strong> {{ $lead->vehicle_variant }}</p>
            @else
                <p><strong>RC Number:</strong> {{ $lead->rc_number }}</p>
                <p><strong>Model:</strong> {{ $lead->model_name }} ({{ $lead->variant }})</p>
            @endif
        </div>
    </div>

    @if($lead->crm_stage == 'Disbursal')
        <hr>
        <h6><strong>Disbursal Details</strong></h6>
        @php
            $financeAmount = $lead->finance_amount ?? $lead->total_loan_amount;
            $disbursalAmount = $lead->disbursal_amount;
        @endphp
        <div class="row">
            <div class="col-md-12">
                <p><strong>Disbursal Date:</strong> {{ $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-' }}</p>
                <p><strong>Finance Amount:</strong> {{ $financeAmount !== null ? number_format($financeAmount, 2) : '-' }}</p>
                <p><strong>Disbursal Amount:</strong> {{ $disbursalAmount !== null ? number_format($disbursalAmount, 2) : '-' }}</p>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-primary" id="printLeadDetailsBtn">Print</button>
    @if($lead->crm_stage == 'Disbursal')
        @if(!$lead->rto_stage)
            <form action="{{ route(get_route_prefix() . 'crm.store-disbursal', $lead->id) }}" method="POST" class="d-inline" data-confirm-message="Move this lead to RTO?">
                @csrf
                <input type="hidden" name="disbursal_date" value="{{ $lead->disbursal_date ? $lead->disbursal_date->format('Y-m-d') : '' }}">
                <input type="hidden" name="finance_amount" value="{{ $lead->finance_amount }}">
                <input type="hidden" name="disbursal_amount" value="{{ $lead->disbursal_amount }}">
                <input type="hidden" name="move_to_rto" value="1">
                <button type="submit" class="btn btn-success">Move to RTO</button>
            </form>
        @endif
        @if($lead->rto_stage && (auth()->user()->hasRole('admin') || auth()->user()->can('view rto')))
            <button type="button"
                    class="btn btn-warning btn-sm crm-view-rto-btn"
                    data-url="{{ route(get_route_prefix() . 'rto.show', $lead->id) }}">
                Open RTO Flow
            </button>
        @endif
        @if($lead->canBeMovedToInsurance())
            <button type="button" class="btn btn-info" onclick="$('#insuranceExpiryModal').modal('show');">Move to Insurance</button>
        @endif
    @endif
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

@if($lead->canBeMovedToInsurance())
    <!-- Insurance Expiry Modal -->
    <div class="modal fade" id="insuranceExpiryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Move to Insurance</h5>
                    <button type="button" class="close" onclick="$('#insuranceExpiryModal').modal('hide');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route(get_route_prefix() . 'crm.store-disbursal', $lead->id) }}" method="POST" data-confirm-message="Move this lead to Insurance?">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="disbursal_date" value="{{ $lead->disbursal_date ? $lead->disbursal_date->format('Y-m-d') : '' }}">
                        <input type="hidden" name="finance_amount" value="{{ $lead->finance_amount }}">
                        <input type="hidden" name="disbursal_amount" value="{{ $lead->disbursal_amount }}">
                        <input type="hidden" name="move_to_insurance" value="1">

                        <div class="form-group">
                            <label>Insurance Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" name="insurance_expiry_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm & Move</button>
                        <button type="button" class="btn btn-secondary" onclick="$('#insuranceExpiryModal').modal('hide');">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
    $(document).off('click.printLeadDetails').on('click.printLeadDetails', '#printLeadDetailsBtn', function() {
        const source = document.getElementById('globalViewModalContent');
        if (!source) return;

        const printWindow = window.open('', '_blank', 'width=1100,height=800');
        if (!printWindow) return;

        const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
            .map(node => node.outerHTML)
            .join('\n');

        const content = source.cloneNode(true);
        const footer = content.querySelector('.modal-footer');
        if (footer) {
            footer.remove();
        }
        const closeBtn = content.querySelector('.close');
        if (closeBtn) {
            closeBtn.remove();
        }

        printWindow.document.open();
        printWindow.document.write(`<!doctype html>
<html>
<head>
    <title>Lead Details - {{ $lead->customer_name }}</title>
    ${styles}
    <style>
        body { padding: 20px; background: #fff; }
        .modal-content { border: 0 !important; box-shadow: none !important; }
        .modal-header, .modal-body { display: block; }
        .btn, .modal-footer { display: none !important; }
    </style>
</head>
<body>
    ${content.outerHTML}
</body>
</html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    });

    // From within CRM modal, allow opening the RTO flow modal (same global modal container).
    // Note: the global modal container exists on CRM index page where this partial is used.
    $('.crm-view-rto-btn').click(function() {
        let url = $(this).data('url');
        if (!url) return;

        $('#globalViewModalContent').html('<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading RTO flow...</p></div>');
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

    // Show/Hide Disbursal fields based on checkbox selection
    $('input[name="completed_stages[]"]').change(function() {
        let isDisbursalChecked = false;
        $('input[name="completed_stages[]"]:checked').each(function() {
            if ($(this).val() === 'Disbursal') {
                isDisbursalChecked = true;
            }
        });

        if (isDisbursalChecked) {
            $('#disbursal_fields').show();
            $('#disbursal_fields input').prop('required', true);
            // Optional fields shouldn't be required
            $('#move_to_rto, #move_to_insurance, #insurance_expiry_div input').prop('required', false);

            // Change form action to store-disbursal if Disbursal is selected
            $(this).closest('form').attr('action', '{{ route(get_route_prefix() . "crm.store-disbursal", $lead->id) }}');
        } else {
            $('#disbursal_fields').hide();
            $('#disbursal_fields input').prop('required', false);
            // Revert form action
            $(this).closest('form').attr('action', '{{ route(get_route_prefix() . "crm.update-stage", $lead->id) }}');
        }
    });

    $('#move_to_insurance').change(function() {
        if($(this).is(':checked')) {
            $('#insurance_expiry_div').show();
            $('input[name="insurance_expiry_date"]').prop('required', true);
        } else {
            $('#insurance_expiry_div').hide();
            $('input[name="insurance_expiry_date"]').prop('required', false);
        }
    });
</script>
