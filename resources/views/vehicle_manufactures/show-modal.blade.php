<div class="modal-header">
    <h5 class="modal-title">Manufacture Details: {{ $vehicleManufacture->name }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Manufacture Name:</strong> {{ $vehicleManufacture->name }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Status:</strong> <span class="badge badge-{{ $vehicleManufacture->is_active ? 'success' : 'danger' }}">{{ $vehicleManufacture->is_active ? 'Active' : 'Blocked' }}</span></p>
        </div>
    </div>

    <hr>
    <h6><strong>Vehicles & Variants</strong></h6>
    @php
        $groupedVehicles = $vehicleManufacture->vehicleNames->groupBy('name');
    @endphp
    @if($groupedVehicles->count() > 0)
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Vehicle Name</th>
                    <th>Variants</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedVehicles as $vehicleName => $items)
                    <tr>
                        <td><strong>{{ $vehicleName }}</strong></td>
                        <td>
                            @foreach($items as $item)
                                @if($item->variant)
                                    <span class="badge badge-info">{{ $item->variant }}</span>
                                @endif
                            @endforeach
                            @if($items->whereNotNull('variant')->isEmpty())
                                <span class="text-muted">No variants</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No vehicles added.</p>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route(get_route_prefix(). 'vehicle-manufactures.edit', $vehicleManufacture->id) }}" class="btn btn-primary">Edit Manufacture</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
