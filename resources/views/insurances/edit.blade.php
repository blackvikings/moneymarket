@extends('adminlte::page')

@section('title', 'Edit Insurance')

@section('content_header')
    <h1>Edit Insurance</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('insurances.update', $insurance->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $insurance->company_name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_name">Agent Name</label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control" value="{{ $insurance->agent_name }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required>{{ $insurance->address }}</textarea>
                </div>

                <hr>
                <h4>Insurance Types & Sub Types</h4>
                <div id="types-container">
                    @foreach ($insurance->types as $index => $type)
                        <div class="card type-card" data-index="{{ $index }}">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Insurance Type</label>
                                    <input type="text" name="types[{{ $index }}][name]" class="form-control" value="{{ $type->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Sub Types</label>
                                    <select name="types[{{ $index }}][sub_types][]" class="form-control sub-types-select" multiple="multiple">
                                        @foreach ($type->subTypes as $subType)
                                            <option value="{{ $subType->name }}" selected>{{ $subType->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Type sub-type name and press enter to add.</small>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-type">Remove Type</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-type">Add Insurance Type</button>

                <hr>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let typeIndex = {{ $insurance->types->count() }};

            function initSelect2(element) {
                element.select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                });
            }

            initSelect2($('.sub-types-select'));

            $('#add-type').click(function() {
                typeIndex++;
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
            });

            $(document).on('click', '.remove-type', function() {
                $(this).closest('.type-card').remove();
            });
        });
    </script>
@stop
