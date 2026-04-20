<form action="{{ route(get_route_prefix() . 'rto.update-checklist', $lead->id) }}" method="POST" class="rto-checking-form" data-confirm-message="Save this document checklist?">
    @csrf
    @php
        $isSelected = function ($current, $option) {
            if ($option === 'Pending') {
                return $current === 'Pending' || $current === null || $current === '' || $current === 0 || $current === '0' || $current === false;
            }
            if ($option === 'Done') {
                return $current === 'Done' || $current === 1 || $current === '1' || $current === true;
            }
            if ($option === 'Previously Done') {
                return $current === 'Previously Done' || $current === 2 || $current === '2';
            }
            return false;
        };
    @endphp
    <div class="row">
        <div class="col-md-6">
            {{-- HSRP --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">HSRP</label>
                <div class="col-sm-8">
                    <select name="rto_hsrp_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_hsrp_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_hsrp_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_hsrp_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_hsrp_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_hsrp_remark }}">
                </div>
            </div>

            {{-- Insurance --}}
            @php
                $isInsuranceProper = ($lead->insurance_available == 1 && $lead->insurance_end_date && \Carbon\Carbon::parse($lead->insurance_end_date)->isFuture());
                if ($isInsuranceProper) {
                    $insuranceStatus = 'Previously Done';
                } else {
                    $raw = $lead->rto_insurance_status;
                    $insuranceStatus = $raw === 'Previously Done' || $raw === 2 || $raw === '2'
                        ? 'Previously Done'
                        : (($raw === 'Done' || $raw === 1 || $raw === '1' || $raw === true) ? 'Done' : 'Pending');
                }
            @endphp
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Insurance</label>
                <div class="col-sm-8">
                    <select name="rto_insurance_status" class="form-control form-control-sm" {{ $isInsuranceProper ? 'readonly' : '' }}>
                        <option value="Pending" {{ $insuranceStatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $insuranceStatus == 'Previously Done' ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $insuranceStatus == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                    @if($isInsuranceProper)
                        <small class="text-success">Verified from Lead Data (Expires: {{ \Carbon\Carbon::parse($lead->insurance_end_date)->format('d-m-Y') }})</small>
                    @endif
                    <input type="text" name="rto_insurance_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_insurance_remark }}">
                </div>
            </div>

            {{-- Fitness --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Fitness</label>
                <div class="col-sm-8">
                    <select name="rto_fitness_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_fitness_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_fitness_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_fitness_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_fitness_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_fitness_remark }}">
                </div>
            </div>

            {{-- Tax --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Tax</label>
                <div class="col-sm-8">
                    <select name="rto_tax_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_tax_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_tax_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_tax_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_tax_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_tax_remark }}">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            {{-- PUC --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">PUC</label>
                <div class="col-sm-8">
                    <select name="rto_puc_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_puc_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_puc_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_puc_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_puc_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_puc_remark }}">
                </div>
            </div>

            {{-- E-challan --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">E-challan</label>
                <div class="col-sm-8">
                    <select name="rto_echallan_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_echallan_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_echallan_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_echallan_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_echallan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_echallan_remark }}">
                </div>
            </div>

            {{-- NCR --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">NCR</label>
                <div class="col-sm-8">
                    <select name="rto_ncr_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_ncr_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_ncr_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_ncr_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_ncr_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_ncr_remark }}">
                </div>
            </div>

            {{-- RTO Challan --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">RTO Challan</label>
                <div class="col-sm-8">
                    <select name="rto_challan_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_challan_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_challan_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_challan_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_challan_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_challan_remark }}">
                </div>
            </div>

            {{-- Permit --}}
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Permit</label>
                <div class="col-sm-8">
                    <select name="rto_permit_status" class="form-control form-control-sm">
                        <option value="Pending" {{ $isSelected($lead->rto_permit_status, 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Previously Done" {{ $isSelected($lead->rto_permit_status, 'Previously Done') ? 'selected' : '' }}>Previously Done</option>
                        <option value="Done" {{ $isSelected($lead->rto_permit_status, 'Done') ? 'selected' : '' }}>Done</option>
                    </select>
                    <input type="text" name="rto_permit_remark" class="form-control form-control-sm mt-1" placeholder="Remark" value="{{ $lead->rto_permit_remark }}">
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Document Checklist</button>
    <button type="submit" name="save_and_next" value="1" class="btn btn-success">Save & Next (Work Checking)</button>
</form>
