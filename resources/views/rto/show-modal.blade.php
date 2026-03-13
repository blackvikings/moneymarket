@php
    $stages = [
        'New',
        'Document checking',
        'Agent Assigned',
        'Challan Done',
        'Document Upload',
        'Verification',
        'RTO Approval',
        'RTO Done',
        'Loan Return'
    ];
    $currentStage = $lead->rto_stage ?? 'New';
    $currentStageIndex = array_search($currentStage, $stages);
@endphp

<style>
    .rto-stage-text {
        display: block;
        max-width: 60px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0 auto;
    }
</style>

<div class="modal-header">
    <h5 class="modal-title">RTO Lead Flow: {{ $lead->customer_name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between mb-3 px-2">
                @foreach($stages as $index => $stage)
                    @if($stage != 'Loan Return')
                        <div class="text-center" style="flex: 1;">
                            <div class="badge {{ $index <= $currentStageIndex ? 'badge-success' : 'badge-secondary' }}"
                                 style="width: 25px; height: 25px; border-radius: 50%; line-height: 18px;"
                                 data-toggle="tooltip" title="{{ $stage }}">
                                {{ $index + 1 }}
                            </div>
                            <br>
                            <small class="rto-stage-text text-muted" data-toggle="tooltip" title="{{ $stage }}">
                                {{ $stage }}
                            </small>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            @if($currentStage == 'New')
                <div class="alert alert-info">New RTO Lead Created. Please proceed to Document Checking.</div>
                <form action="{{ route(get_route_prefix() . 'rto.update-stage', $lead->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="current_stage" value="New">
                    <input type="hidden" name="action" value="Yes">
                    <button type="submit" class="btn btn-primary btn-block">Start Document Checking</button>
                </form>

            @elseif($currentStage == 'Document checking')
                @include('rto.partials.document_checking_form')

            @elseif($currentStage == 'Agent Assigned')
                @include('rto.partials.agent_assign_form')

            @elseif($currentStage == 'Challan Done')
                @include('rto.partials.challan_form')

            @elseif($currentStage == 'Document Upload')
                @include('rto.partials.doc_upload_form')

            @elseif($currentStage == 'Verification')
                @include('rto.partials.verification_form')

            @elseif($currentStage == 'RTO Approval')
                @include('rto.partials.approval_form')

            @elseif($currentStage == 'RTO Done')
                @include('rto.partials.final_done_form')

            @elseif($currentStage == 'Loan Return')
                <div class="alert alert-warning">
                    <h6><strong>Loan Return Details</strong></h6>
                    <p><strong>Date:</strong> {{ $lead->loan_return_date ? \Carbon\Carbon::parse($lead->loan_return_date)->format('d-m-Y') : '-' }}</p>
                    <p><strong>Reason:</strong> {{ $lead->loan_return_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($currentStage != 'Loan Return' && $currentStage != 'RTO Done')
    <hr>
    <div class="mt-3">
        <button class="btn btn-danger btn-sm" onclick="$('#rejection_div').toggle()">Reject Lead (Loan Return)</button>
        <div id="rejection_div" style="display:none;" class="mt-2">
            <form action="{{ route(get_route_prefix() . 'rto.reject', $lead->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="rejection_reason" class="form-control" placeholder="Enter reason for rejection" required></textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-danger">Confirm Reject</button>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
