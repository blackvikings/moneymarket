@extends('adminlte::page')

@section('title', 'Create Vehicle Manufacture')

@section('content_header')
    <h1>Create Vehicle Manufacture</h1>
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
            <form action="{{ route(get_route_prefix(). 'vehicle-manufactures.store') }}" method="POST" id="createVehicleManufactureForm">
                @csrf
                <div class="form-group">
                    <label for="name">Manufacture Name <span class="text-danger">*</span></label>
                    <select name="name" id="name" class="form-control select2 @error('name') is-invalid @enderror" required>
                        <option value=""></option>
                        @foreach($manufactures as $mfg)
                            <option value="{{ $mfg->name }}" {{ old('name') == $mfg->name ? 'selected' : '' }}>{{ $mfg->name }}</option>
                        @endforeach
                    </select>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h5>Vehicle Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_name">Vehicle Name <span class="text-danger">*</span></label>
                            <input type="text" name="vehicle_name" id="vehicle_name" class="form-control @error('vehicle_name') is-invalid @enderror" value="{{ old('vehicle_name') }}" required>
                            @error('vehicle_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="variants">Variants</label>
                            <select name="variants[]" id="variants" class="form-control select2 @error('variants') is-invalid @enderror" multiple="multiple">
                                @if(old('variants'))
                                    @foreach(old('variants') as $variant)
                                        <option value="{{ $variant }}" selected>{{ $variant }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('variants') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="form-text text-muted">Type variant name and press enter to add. Spaces are allowed.</small>
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">Save Vehicle</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 with tags for Manufacturer Name
            $('#name').select2({
                tags: true,
                placeholder: "Select or Type New Manufacturer",
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Variants
            $('#variants').select2({
                tags: true,
                tokenSeparators: [], // Removed space from separators to allow spaces in strings
                placeholder: "Type variants and press enter",
                width: '100%'
            });

            $('#createVehicleManufactureForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        element.closest('.form-group').append(error);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    }
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                    }
                },
                rules: {
                    name: { required: true },
                    vehicle_name: { required: true }
                },
                messages: {
                    name: "Please select or enter a manufacturer name",
                    vehicle_name: "Please enter the vehicle name"
                }
            });

            // Trigger validation on select2 change
            $('.select2').on('change', function() {
                $(this).valid();
            });
        });
    </script>
@stop
