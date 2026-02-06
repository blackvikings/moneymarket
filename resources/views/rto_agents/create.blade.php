@extends('adminlte::page')

@section('title', 'Create RTO Agent')

@section('content_header')
    <h1>Create RTO Agent</h1>
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
            <form action="{{ route('admin.rto-agents.store') }}" method="POST" id="createRtoAgentForm">
                @csrf
                <div class="form-group">
                    <label for="name">Agent Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="number">Agent Number</label>
                    <input type="text" name="number" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}">
                    @error('number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="cities">Cities</label>
                    <select name="cities[]" id="cities" class="form-control @error('cities') is-invalid @enderror" multiple="multiple">
                        @foreach ($cities as $city)
                            <option value="{{ $city->name }}" {{ in_array($city->name, old('cities', [])) ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('cities') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Select cities from the list.</small>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cities').select2({
                tags: false,
                tokenSeparators: [',', ' ']
            });

            $('#createRtoAgentForm').validate({
                ignore: [], // To validate hidden select2 fields
                rules: {
                    name: { required: true }
                },
                messages: {
                    name: "Please enter the agent's name"
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@stop
