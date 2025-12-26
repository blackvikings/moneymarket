@extends('adminlte::page')

@section('title', 'Create Firm')

@section('content_header')
    <h1>Create Firm</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('firms.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Firm Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Firm Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop
