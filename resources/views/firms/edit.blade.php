@extends('adminlte::page')

@section('title', 'Edit Firm')

@section('content_header')
    <h1>Edit Firm</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('firms.update', $firm->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Firm Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $firm->name }}" required>
                </div>
                <div class="form-group">
                    <label for="address">Firm Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required>{{ $firm->address }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@stop
