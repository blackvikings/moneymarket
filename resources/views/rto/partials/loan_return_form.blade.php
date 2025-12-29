<form action="{{ route('rto.update-loan-return', $lead->id) }}" method="POST" class="rto-loan-return-form">
    @csrf
    <div class="form-group">
        <label>Date</label>
        <input type="date" name="loan_return_date" class="form-control" value="{{ $lead->loan_return_date ? $lead->loan_return_date->format('Y-m-d') : '' }}" required>
    </div>
    <div class="form-group">
        <label>Reason for Cancel Loan</label>
        <textarea name="loan_return_reason" class="form-control" rows="3" required>{{ $lead->loan_return_reason }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save Loan Return Details</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next</button>
</form>
