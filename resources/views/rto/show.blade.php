@extends('adminlte::page')

@section('title', 'RTO Lead Details')

@section('content_header')
    <h1>RTO Lead: {{ $lead->customer_name }}</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @php
        $formStages = ['Checking', 'Loan Return', 'Vehicle Detail', 'Insurance'];
        $activeTab = in_array($lead->rto_stage, $formStages) ? $lead->rto_stage : 'Checking';
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="rto-tabs" role="tablist">
                        @foreach($formStages as $stage)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === $stage ? 'active' : '' }}" id="tab-{{ str_replace(' ', '-', strtolower($stage)) }}" data-toggle="pill" href="#content-{{ str_replace(' ', '-', strtolower($stage)) }}" role="tab" aria-controls="content-{{ str_replace(' ', '-', strtolower($stage)) }}" aria-selected="{{ $activeTab === $stage ? 'true' : 'false' }}">{{ $stage }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="rto-tabs-content">
                        {{-- Checking Tab --}}
                        <div class="tab-pane fade {{ $activeTab === 'Checking' ? 'show active' : '' }}" id="content-checking" role="tabpanel" aria-labelledby="tab-checking">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>HSRP:</strong> {{ $lead->rto_hsrp_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>Hypothecation:</strong> {{ $lead->rto_hypothecation_status ? 'Yes' : 'No' }}</p>
                                    @if($lead->rto_hypothecation_status)
                                        <p><strong>Bank:</strong> {{ $lead->bank ? $lead->bank->name : '-' }}</p>
                                    @endif
                                    <p><strong>Insurance:</strong> {{ $lead->rto_insurance_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>Fitness:</strong> {{ $lead->rto_fitness_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>Tax:</strong> {{ $lead->rto_tax_status ? 'Yes' : 'No' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>PUC:</strong> {{ $lead->rto_puc_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>E-challan:</strong> {{ $lead->rto_echallan_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>NCR:</strong> {{ $lead->rto_ncr_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>RTO Challan:</strong> {{ $lead->rto_challan_status ? 'Yes' : 'No' }}</p>
                                    <p><strong>Permit:</strong> {{ $lead->rto_permit_status ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-checking">Edit Checking Details</button>
                        </div>

                        {{-- Loan Return Tab --}}
                        <div class="tab-pane fade {{ $activeTab === 'Loan Return' ? 'show active' : '' }}" id="content-loan-return" role="tabpanel" aria-labelledby="tab-loan-return">
                            <p><strong>Date:</strong> {{ $lead->loan_return_date ? $lead->loan_return_date->format('d-m-Y') : '-' }}</p>
                            <p><strong>Reason:</strong> {{ $lead->loan_return_reason }}</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-loan-return">Edit Loan Return Details</button>
                        </div>

                        {{-- Vehicle Detail Tab --}}
                        <div class="tab-pane fade {{ $activeTab === 'Vehicle Detail' ? 'show active' : '' }}" id="content-vehicle-detail" role="tabpanel" aria-labelledby="tab-vehicle-detail">
                            <p><strong>Vehicle Type:</strong> {{ $lead->rto_vehicle_type }}</p>
                            @if($lead->rto_vehicle_type == 'Old Vehicle')
                                <p><strong>Old Financer:</strong> {{ $lead->old_financer }}</p>
                                <p><strong>Loan Closed:</strong> {{ $lead->rto_loan_closed_status ? 'Yes' : 'No' }}</p>
                                <p><strong>HP Termination:</strong> {{ $lead->rto_hp_termination_status ? 'Yes' : 'No' }}</p>
                                <h6>Closer Deposits:</h6>
                                <ul>
                                    @foreach($lead->rtoCloserDeposits as $deposit)
                                        <li>{{ $deposit->amount }} on {{ $deposit->date->format('d-m-Y') }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <p><strong>RTO Agent:</strong> {{ $lead->rtoAgent ? $lead->rtoAgent->name : '-' }}</p>
                            <h6>Challans:</h6>
                            <ul>
                                @foreach($lead->rtoChallans as $challan)
                                    <li>{{ $challan->challan_type }} - {{ $challan->amount }} on {{ $challan->date->format('d-m-Y') }}</li>
                                @endforeach
                            </ul>
                            <p><strong>Challan Verified:</strong> {{ $lead->rto_challan_verified ? 'Yes' : 'No' }}</p>
                            <p><strong>Verification:</strong> {{ $lead->rto_verification_status ? 'Yes' : 'No' }}</p>
                            <p><strong>Approval:</strong> {{ $lead->rto_approval_status ? 'Yes' : 'No' }}</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-vehicle-detail">Edit Vehicle Details</button>
                        </div>

                        {{-- Insurance Tab --}}
                        <div class="tab-pane fade {{ $activeTab === 'Insurance' ? 'show active' : '' }}" id="content-insurance" role="tabpanel" aria-labelledby="tab-insurance">
                            <p><strong>Company:</strong> {{ $lead->insurance ? $lead->insurance->company_name : '-' }}</p>
                            <p><strong>Start Date:</strong> {{ $lead->insurance_start_date ? $lead->insurance_start_date->format('d-m-Y') : '-' }}</p>
                            <p><strong>End Date:</strong> {{ $lead->insurance_end_date ? $lead->insurance_end_date->format('d-m-Y') : '-' }}</p>
                            <p><strong>Type:</strong> {{ $lead->insurance_type_of_insurance }}</p>
                            <p><strong>Premium:</strong> {{ $lead->insurance_premium_amount }}</p>
                            <p><strong>IDV:</strong> {{ $lead->insurance_idv_amount }}</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-insurance">Edit Insurance Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Lead Info</h3></div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $lead->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $lead->contact_number }}</p>
                    @if($lead->rto_address_proof_path)
                        <p><a href="{{ asset('storage/' . $lead->rto_address_proof_path) }}" target="_blank">View Address Proof</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <div class="modal fade" id="modal-checking" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Checking Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @include('rto.partials.checking_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-loan-return" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Loan Return Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @include('rto.partials.loan_return_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-vehicle-detail" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vehicle Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @include('rto.partials.vehicle_detail_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-insurance" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Insurance Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @include('rto.partials.insurance_form')
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Checking Form JS
            function toggleHypothecation() {
                if($('#hypothecation').is(':checked')) {
                    $('#hypothecation_bank_div').show();
                } else {
                    $('#hypothecation_bank_div').hide();
                }
            }
            $('#hypothecation').change(toggleHypothecation);

            // Vehicle Detail Form JS
            function toggleVehicleType() {
                if ($('#rto_vehicle_type').val() === 'Old Vehicle') {
                    $('#old_vehicle_details').show();
                } else {
                    $('#old_vehicle_details').hide();
                }
            }
            $('#rto_vehicle_type').change(toggleVehicleType);

            let depositIndex = {{ $lead->rtoCloserDeposits->count() }};
            $('#add-closer-deposit').click(function() {
                let html = `
                    <div class="row closer-deposit-row">
                        <div class="col-md-5"><div class="form-group"><input type="number" step="0.01" name="closer_deposits[${depositIndex}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-5"><div class="form-group"><input type="date" name="closer_deposits[${depositIndex}][date]" class="form-control"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                $('#closer-deposits-container').append(html);
                depositIndex++;
            });

            let challanIndex = {{ $lead->rtoChallans->count() }};
            $('#add-challan').click(function() {
                let html = `
                    <div class="row challan-row">
                        <div class="col-md-4"><div class="form-group"><input type="text" name="challans[${challanIndex}][type]" class="form-control" placeholder="Challan Type"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="date" name="challans[${challanIndex}][date]" class="form-control"></div></div>
                        <div class="col-md-3"><div class="form-group"><input type="number" step="0.01" name="challans[${challanIndex}][amount]" class="form-control" placeholder="Amount"></div></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">X</button></div>
                    </div>`;
                $('#challans-container').append(html);
                challanIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('.row').remove();
            });
        });
    </script>
@stop
