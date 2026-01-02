<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'follow_up_date' => 'date',
        'insurance_start_date' => 'date',
        'insurance_end_date' => 'date',
        'converted_at' => 'datetime',
        'loan_return_date' => 'date',
        'disbursal_date' => 'date',
        // CRM Stage Timestamps
        'crm_new_at' => 'datetime',
        'crm_login_at' => 'datetime',
        'crm_fi_document_at' => 'datetime',
        'crm_vehicle_evaluation_at' => 'datetime',
        'crm_credit_at' => 'datetime',
        'crm_approved_at' => 'datetime',
        'crm_agreement_at' => 'datetime',
        'crm_disbursal_at' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bankBranch()
    {
        return $this->belongsTo(BankBranch::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function manufacture()
    {
        return $this->belongsTo(VehicleManufacture::class, 'manufacture_id');
    }

    public function vehicleName()
    {
        return $this->belongsTo(VehicleName::class, 'vehicle_name_id');
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function rtoAgent()
    {
        return $this->belongsTo(RtoAgent::class);
    }

    public function rtoCloserDeposits()
    {
        return $this->hasMany(RtoCloserDeposit::class);
    }

    public function rtoChallans()
    {
        return $this->hasMany(RtoChallan::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(LeadStatusLog::class);
    }

    // Accessor for Aging
    public function getAgingAttribute()
    {
        if ($this->converted_at) {
            return $this->created_at->diffForHumans($this->converted_at, true);
        }
        return $this->created_at->diffForHumans(null, true);
    }
}
