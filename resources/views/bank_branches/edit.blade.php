@extends('adminlte::page')

@section('title', 'Edit Bank Branch')

@section('content_header')
    <h1>Edit Bank Branch</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bank-branches.update', $bankBranch->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="bank_id">Bank</label>
                    <select name="bank_id" id="bank_id" class="form-control @error('bank_id') is-invalid @enderror" required>
                        <option value="">Select a Bank</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id', $bankBranch->bank_id) == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                    @error('bank_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="name">Branch Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bankBranch->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@stop
