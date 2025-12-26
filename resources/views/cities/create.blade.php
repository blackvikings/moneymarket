@extends('adminlte::page')

@section('title', 'Create City')

@section('content_header')
    <h1>Create City</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cities.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">City Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@stop
