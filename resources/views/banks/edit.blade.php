@extends('adminlte::page')

@section('title', 'Edit Bank')

@section('content_header')
    <h1>Edit Bank</h1>
@stop

@section('content')
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST" id="editBankForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Bank Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bank->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Branches</h5>
                <div id="branches-container">
                    @foreach ($bank->branches as $index => $branch)
                        <div class="card branch-row mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-danger btn-sm remove-branch">X</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Branch Name <span class="text-danger">*</span></label>
                                            <input type="text" name="branches[{{ $index }}][name]" class="form-control" value="{{ $branch->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address Line</label>
                                            <input type="text" name="branches[{{ $index }}][address_line]" class="form-control" value="{{ $branch->address_line }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pin Code</label>
                                            <input type="text" name="branches[{{ $index }}][pincode]" class="form-control pincode-input" value="{{ $branch->pincode }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>State</label>
                                            <select name="branches[{{ $index }}][state_id]" class="form-control state-select" data-selected="{{ $branch->state_id }}">
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="branches[{{ $index }}][city_id]" class="form-control city-select" data-selected="{{ $branch->city_id }}">
                                                <option value="">Select State First</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-branch">Add Branch</button>

                <br><br>
                <button type="submit" class="btn btn-primary">Update Bank</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            let branchIndex = {{ $bank->branches->count() }};

            function addBranchRow() {
                let html = `
                    <div class="card branch-row mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-danger btn-sm remove-branch">X</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Name <span class="text-danger">*</span></label>
                                        <input type="text" name="branches[${branchIndex}][name]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address Line</label>
                                        <input type="text" name="branches[${branchIndex}][address_line]" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pin Code</label>
                                        <input type="text" name="branches[${branchIndex}][pincode]" class="form-control pincode-input">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <select name="branches[${branchIndex}][state_id]" class="form-control state-select">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <select name="branches[${branchIndex}][city_id]" class="form-control city-select">
                                            <option value="">Select State First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#branches-container').append(html);

                // Populate states for the new row
                let newStateSelect = $('#branches-container .branch-row:last .state-select');
                populateStates(newStateSelect);

                branchIndex++;
            }

            $('#add-branch').click(function() {
                addBranchRow();
            });

            $(document).on('click', '.remove-branch', function() {
                if ($('.branch-row').length > 1) {
                    $(this).closest('.branch-row').remove();
                } else {
                    alert('At least one branch is required.');
                }
            });

            // --- Location Scripts ---
            function populateStates(element, selectedStateId = null) {
                $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        element.empty().append('<option value="">Select State</option>');
                        $.each(data, function(key, state) {
                            let selected = (selectedStateId == state.id) ? 'selected' : '';
                            element.append('<option value="' + state.id + '" ' + selected + '>' + state.name + '</option>');
                        });
                    }
                });
            }

            function populateCities(stateElement, cityElement, pincodeElement, selectedCity = null) {
                let stateId = $(stateElement).val();
                $(cityElement).empty().append('<option value="">Loading...</option>');
                if(pincodeElement) $(pincodeElement).val('');

                if (stateId) {
                    $.ajax({
                        url: '{{ url("get-cities") }}/' + stateId,
                        type: 'GET',
                        success: function(data) {
                            $(cityElement).empty().append('<option value="">Select City</option>');
                            $.each(data, function(key, city) {
                                let option = $('<option></option>').attr('value', city.id).text(city.name).data('pincode', city.pincode);
                                if (selectedCity && city.id == selectedCity) {
                                    option.prop('selected', true);
                                    if(pincodeElement) $(pincodeElement).val(city.pincode);
                                }
                                $(cityElement).append(option);
                            });
                        }
                    });
                } else {
                    $(cityElement).empty().append('<option value="">Select State First</option>');
                }
            }

            // Initialize existing rows
            $('.branch-row').each(function() {
                let stateSelect = $(this).find('.state-select');
                let citySelect = $(this).find('.city-select');
                let selectedStateId = stateSelect.data('selected');
                let selectedCityId = citySelect.data('selected');

                // Re-implementing initialization logic specifically:
                 $.ajax({
                    url: '{{ route("get-states") }}',
                    type: 'GET',
                    success: function(data) {
                        stateSelect.empty().append('<option value="">Select State</option>');
                        $.each(data, function(key, state) {
                            let selected = (selectedStateId == state.id) ? 'selected' : '';
                            stateSelect.append('<option value="' + state.id + '" ' + selected + '>' + state.name + '</option>');
                        });

                        if (selectedStateId) {
                             populateCities(stateSelect, citySelect, null, selectedCityId);
                        }
                    }
                });
            });


            // Event Handlers using delegation for dynamic elements
            $(document).on('change', '.state-select', function() {
                let citySelect = $(this).closest('.branch-row').find('.city-select');
                let pincodeInput = $(this).closest('.branch-row').find('.pincode-input');
                // When manually changing state, we don't have a selected city
                populateCities(this, citySelect, pincodeInput);
            });

            $(document).on('change', '.city-select', function() {
                let pincode = $(this).find(':selected').data('pincode');
                let pincodeInput = $(this).closest('.branch-row').find('.pincode-input');
                if(pincode) pincodeInput.val(pincode);
            });

            $(document).on('input', '.pincode-input', function() {
                let pincode = $(this).val();
                if (pincode.length === 6) {
                    let stateSelect = $(this).closest('.branch-row').find('.state-select');
                    let citySelect = $(this).closest('.branch-row').find('.city-select');

                    $.ajax({
                        url: '{{ url("get-location-by-pincode") }}/' + pincode,
                        type: 'GET',
                        success: function(data) {
                            if (data) {
                                stateSelect.val(data.state.id);
                                populateCities(stateSelect, citySelect, null, data.city.id);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            if(jqXHR.status == 404) {
                                // alert('Invalid Pincode');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
