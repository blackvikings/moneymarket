@extends('adminlte::page')

@section('title', 'Create Insurance')

@section('content_header')
    <h1>Create Insurance</h1>
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
        .select2-container--default .select2-selection--multiple.error {
            border: 1px solid red;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('insurances.store') }}" method="POST" id="createInsuranceForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required>
                            @error('company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_name">Agent Name</label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name') }}" required>
                            @error('agent_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                    @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Insurance Types & Sub Types</h4>
                <div id="types-container">
                    {{-- Dynamically added fields will be here --}}
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-type">Add Insurance Type</button>

                <hr>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createInsuranceForm').validate();

            let typeIndex = 0;

            function initSelect2(element) {
                element.select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                });
            }

            function addTypeRow() {
                let html = `
                    <div class="card type-card mt-3" data-index="${typeIndex}">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Insurance Type</label>
                                <input type="text" name="types[${typeIndex}][name]" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Sub Types</label>
                                <select name="types[${typeIndex}][sub_types][]" class="form-control sub-types-select" multiple="multiple">
                                </select>
                                <small class="form-text text-muted">Type sub-type name and press enter to add.</small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-type">Remove Type</button>
                        </div>
                    </div>
                `;
                $('#types-container').append(html);
                initSelect2($('#types-container .type-card:last .sub-types-select'));

                // Add validation for the new row
                $(`[name="types[${typeIndex}][name]"]`).rules('add', {
                    required: true,
                    messages: {
                        required: "Insurance type name is required"
                    }
                });

                typeIndex++;
            }

            addTypeRow(); // Add the first row on page load

            $('#add-type').click(addTypeRow);

            $(document).on('click', '.remove-type', function() {
                $(this).closest('.type-card').remove();
            });
        });
    </script>
@stop
