@extends('adminlte::page')

@section('title', 'Add New RTO Lead')

@section('content_header')
    <h1>Add New RTO Lead</h1>
@stop

@section('content')
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
        input.error, select.error, textarea.error {
            border: 1px solid red;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rto.store') }}" method="POST" enctype="multipart/form-data" id="createRtoLeadForm">
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
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" required>
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
                            <label>Email Id <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Residence Type <span class="text-danger">*</span></label>
                            <select name="residence_type" class="form-control" required>
                                <option value="">Select Type</option>
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
                        <h6>Current Address <span class="text-danger">*</span></h6>
                        <div class="form-group"><label>Address Line</label><input type="text" name="current_address_line" class="form-control" required></div>
                        <div class="form-group"><label>Pin Code</label><input type="text" name="current_address_pincode" class="form-control"></div>
                        <div class="form-group"><label>City</label><input type="text" name="current_address_city" class="form-control"></div>
                        <div class="form-group"><label>State</label><input type="text" name="current_address_state" class="form-control"></div>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address (As per Aadhar) <span class="text-danger">*</span></h6>
                        <div class="form-group"><label>Address Line</label><input type="text" name="permanent_address_line" class="form-control" required></div>
                        <div class="form-group"><label>Pin Code</label><input type="text" name="permanent_address_pincode" class="form-control"></div>
                        <div class="form-group"><label>City</label><input type="text" name="permanent_address_city" class="form-control"></div>
                        <div class="form-group"><label>State</label><input type="text" name="permanent_address_state" class="form-control"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Address Proof <span class="text-danger">*</span></label>
                    <input type="file" name="rto_address_proof" class="form-control-file" required>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Add RTO Lead</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#createRtoLeadForm').validate({
                rules: {
                    customer_name: { required: true },
                    father_name: { required: true },
                    contact_number: { required: true, number: true, minlength: 10, maxlength: 10 },
                    email: { required: true, email: true },
                    residence_type: { required: true },
                    current_address_line: { required: true },
                    permanent_address_line: { required: true },
                    rto_address_proof: { required: true }
                },
                messages: {
                    customer_name: "Please enter the customer's name",
                    father_name: "Please enter the father's name",
                    contact_number: {
                        required: "Please enter a mobile number",
                        number: "Please enter a valid number",
                        minlength: "Mobile number must be 10 digits",
                        maxlength: "Mobile number must be 10 digits"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    residence_type: "Please select a residence type",
                    current_address_line: "Please enter the current address",
                    permanent_address_line: "Please enter the permanent address",
                    rto_address_proof: "Please upload an address proof document"
                }
            });
        });
    </script>
@stop
