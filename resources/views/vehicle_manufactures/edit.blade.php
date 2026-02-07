@extends('adminlte::page')

@section('title', 'Edit Vehicle Manufacture')

@section('content_header')
    <h1>Edit Vehicle Manufacture</h1>
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
        .select2-container--default .select2-selection--single.error,
        .select2-container--default .select2-selection--multiple.error {
            border: 1px solid red;
        }
        .invalid-feedback {
            display: block;
        }

        /* --- Select2 Layout Fixes --- */

        /* 1. Spacing below the select boxes */
        .form-group .select2-container {
            margin-bottom: 15px !important;
            width: 100% !important;
        }

        /* 2. Fix single select height and text alignment */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            padding: 6px 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 0 !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* 3. Fix multiple select (Variants) wrapping and scroll */
        .select2-container--default .select2-selection--multiple {
            min-height: 38px !important;
            height: auto !important;
            border: 1px solid #ced4da !important;
            overflow: hidden !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            padding: 0 8px 5px 8px !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border: 1px solid #006fe6 !important;
            color: #fff !important;
            padding: 0 10px !important;
            margin-top: 5px !important;
            margin-right: 5px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255,255,255,.7) !important;
            margin-right: 5px !important;
        }

        /* 4. Fix search field in multiple select */
        .select2-search--inline .select2-search__field {
            height: 24px !important;
            margin-top: 7px !important;
            font-family: inherit !important;
        }

        /* 5. Hide all scrollbars */
        .select2-container *::-webkit-scrollbar {
            display: none !important;
        }
        .select2-container * {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.update', $vehicleManufacture->id) }}" method="POST" id="editVehicleManufactureForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Manufacture Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $vehicleManufacture->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Vehicle Details</h5>
                <div id="vehicles-container">
                    @php
                        $groupedVehicles = $vehicleManufacture->vehicleNames->groupBy('name');
                        $index = 0;
                    @endphp
                    @foreach($groupedVehicles as $vehicleName => $items)
                        <div class="card vehicle-card mt-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vehicle Name</label>
                                            <input type="text" name="vehicles[{{ $index }}][name]" class="form-control" value="{{ $vehicleName }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Variants</label>
                                            <select name="vehicles[{{ $index }}][variants][]" class="form-control select2 variant-select" multiple="multiple">
                                                @foreach($items as $item)
                                                    @if($item->variant)
                                                        <option value="{{ $item->variant }}" selected>{{ $item->variant }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-vehicle">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $index++; @endphp
                    @endforeach
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-vehicle">Add Vehicle Name</button>

                <hr>
                <button type="submit" class="btn btn-primary">Save Manufacture</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            function initVariantSelect2(element) {
                element.select2({
                    tags: true,
                    tokenSeparators: [','],
                    placeholder: "Type variants and press enter",
                    width: '100%'
                });
            }

            initVariantSelect2($('.variant-select'));

            $('#editVehicleManufactureForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            let vehicleIndex = {{ $index }};

            $('#add-vehicle').click(function() {
                let html = `
                    <div class="card vehicle-card mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vehicle Name</label>
                                        <input type="text" name="vehicles[${vehicleIndex}][name]" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Variants</label>
                                        <select name="vehicles[${vehicleIndex}][variants][]" class="form-control select2 variant-select" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-block remove-vehicle">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#vehicles-container').append(html);
                initVariantSelect2($('#vehicles-container .vehicle-card:last .variant-select'));

                $(`[name="vehicles[${vehicleIndex}][name]"]`).rules('add', {
                    required: true,
                    messages: { required: "Vehicle name is required" }
                });

                vehicleIndex++;
            });

            $(document).on('click', '.remove-vehicle', function() {
                $(this).closest('.vehicle-card').remove();
            });
        });
    </script>
@stop
