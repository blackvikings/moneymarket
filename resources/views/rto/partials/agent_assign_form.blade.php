<form action="{{ route(get_route_prefix() . 'rto.assign-agent', $lead->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Assign RTO Agent</label>
        <select name="rto_agent_id" class="form-control" required>
            <option value="">Select Agent</option>
            @foreach(\App\Models\RtoAgent::where('is_active', true)->get() as $agent)
                <option value="{{ $agent->id }}" {{ $lead->rto_agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Assign Agent & Start Aging</button>
</form>
