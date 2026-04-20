@extends('adminlte::page')

@section('title', 'Create Referral')

@section('content_header')
    <h1>Create Referral</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'referral-persons.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Referral</button>
            </form>
        </div>
    </div>
@stop
