@extends('adminlte::page')

@section('title', 'Edit Referral Person')

@section('content_header')
    <h1>Edit Referral Person</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route(get_route_prefix() . 'referral-persons.update', $referralPerson->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $referralPerson->name }}" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ $referralPerson->contact_number }}">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $referralPerson->email }}">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ $referralPerson->address }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Person</button>
            </form>
        </div>
    </div>
@stop
