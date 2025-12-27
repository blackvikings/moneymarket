@extends('adminlte::page')

@section('title', 'Add New Insurance Lead')

@section('content_header')
    <h1>Add New Insurance Lead</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rto.store-insurance-lead') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5>Customer Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Enquiry Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Father Name</label>
                            <input type="text" name="father_name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Id</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type</label>
                            <select name="residence_type" class="form-control">
                                <option value="Rented">Rented</option>
                                <option value="Own">Own</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Address Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Address</h6>
                        <div class="form-group"><label>Address Line</label><input type="text" name="current_address_line" class="form-control"></div>
                        <div class="form-group"><label>Pin Code</label><input type="text" name="current_address_pincode" class="form-control"></div>
                        <div class="form-group"><label>City</label><input type="text" name="current_address_city" class="form-control"></div>
                        <div class="form-group"><label>State</label><input type="text" name="current_address_state" class="form-control"></div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address (As per Aadhar)</h6>
                        <div class="form-group"><label>Address Line</label><input type="text" name="permanent_address_line" class="form-control"></div>
                        <div class="form-group"><label>Pin Code</label><input type="text" name="permanent_address_pincode" class="form-control"></div>
                        <div class="form-group"><label>City</label><input type="text" name="permanent_address_city" class="form-control"></div>
                        <div class="form-group"><label>State</label><input type="text" name="permanent_address_state" class="form-control"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof Upload</label>
                            <input type="file" name="insurance_address_proof" class="form-control-file">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address Proof Type</label>
                            <input type="text" name="insurance_address_proof_type" class="form-control" placeholder="e.g., Aadhar, Voter ID">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Add Insurance Lead</button>
            </form>
        </div>
    </div>
@stop
