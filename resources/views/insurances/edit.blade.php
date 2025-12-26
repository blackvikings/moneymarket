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
                            <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $insurance->company_name) }}" required>
                            @error('company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="agent_name">Agent Name</label>
                            <input type="text" name="agent_name" id="agent_name" class="form-control @error('agent_name') is-invalid @enderror" value="{{ old('agent_name', $insurance->agent_name) }}" required>
                            @error('agent_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $insurance->address) }}</textarea>
                    @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <hr>
                <h4>Insurance Types & Sub Types</h4>
                <div id="types-container">
                    @php
                        $types = old('types', $insurance->types->map(function($type) {
                            return [
                                'name' => $type->name,
                                'sub_types' => $type->subTypes->pluck('name')->toArray()
                            ];
                        })->toArray());
                        if (empty($types)) {
                            $types = [['name' => '', 'sub_types' => []]];
                        }
                    @endphp
                    @foreach ($types as $index => $type)
                        <div class="card type-card mt-3" data-index="{{ $index }}">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Insurance Type</label>
                                    <input type="text" name="types[{{ $index }}][name]" class="form-control @error('types.'.$index.'.name') is-invalid @enderror" value="{{ $type['name'] }}" required>
                                    @error('types.'.$index.'.name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Sub Types</label>
                                    <select name="types[{{ $index }}][sub_types][]" class="form-control sub-types-select" multiple="multiple">
                                        @if(isset($type['sub_types']))
                                            @foreach($type['sub_types'] as $subType)
                                                <option value="{{ $subType }}" selected>{{ $subType }}</option>
                                            @endforeach
                                        @endif
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
            let typeIndex = {{ count(old('types', $insurance->types->toArray())) -1 }};

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
