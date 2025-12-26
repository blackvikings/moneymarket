@extends('adminlte::page')

@section('title', 'Edit City')

@section('content_header')
    <h1>Edit City</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('cities.update', $city->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">City Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $city->name }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@stop
