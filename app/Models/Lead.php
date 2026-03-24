<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lead extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    protected $fillable = [
        'customer_name',
        'contact_number',
        'email',
        'lead_type',
        'loan_type',
        'sub_loan_type',
        'source',
        'status',
        'assigned_to',
        'created_by',
        'follow_up_date',
        'remarks',
        'converted_at',
        'firm_id',
        'bank_id',
        'bank_branch_id',
        'manufacture_id',
        'vehicle_name_id',
        'variant',
        'color',
        'finance_amount',
        'processing_fee',
        'login_date',
        'sanction_date',
        'disbursal_date',
        'disbursal_amount',
        'pdd_amount',
        'file_charge',
        'insurance_amount',
        'rto_amount',
        'other_charges',
        'total_deduction',
        'net_disbursal_amount',
        'crm_stage',
        'crm_new_at',
        'crm_login_at',
        'crm_fi_document_at',
        'crm_vehicle_evaluation_at',
        'crm_credit_at',
        'crm_approved_at',
        'crm_agreement_at',
        'crm_disbursal_at',
        'rto_stage',
        'rto_new_at',
        'rto_done_at',
        'rto_agent_id',
        'rto_status',
        'rto_remarks',
        'rto_approved_doc_path',
        'insurance_lead_stage',
        'insurance_type',
        'insurance_sub_type',
        'insurance_company',
        'policy_number',
        'insurance_start_date',
        'insurance_end_date',
        'insurance_premium',
        'current_address_line',
        'current_address_pincode',
        'current_address_city',
        'current_address_state',
        'permanent_address_line',
        'permanent_address_pincode',
        'permanent_address_city',
        'permanent_address_state',
        'profile_picture_path',
        'aadhar_number',
        'proof_id_type',
        'proof_id_number',
        'loan_purchase_type',
        'property_type',
        'property_status',
        'property_pincode',
        'property_city',
        'property_state',
        'property_owner',
        'property_cost',
        'business_stability',
        'business_type',
        'business_address',
        'business_pincode',
        'business_city',
        'business_state',
        'job_profile',
        'agri_land_area',
        'agri_land_document_path',
        'monthly_income',
        'itr_income',
        'other_income',
        'total_income',
        'checklist_vehicle_cost',
        'checklist_hypothecation',
        'checklist_rto_tax',
        'checklist_insurance',
        'checklist_file_charges',
        'checklist_pdd',
        'checklist_other_charges',
        'checklist_total_charges',
        'checklist_net_disbursal',
        'checklist_customer_margin',
        'rto_bank_name',
        'rto_noc_status',
        'rto_noc_date',
        'rto_noc_doc_path',
        'rto_work_stage',
        'rto_work_status',
        'rto_work_date',
        'rto_work_remarks',
        'rto_work_doc_path',
        'rto_verification_stage',
        'rto_verification_status',
        'rto_verification_date',
        'rto_verification_remarks',
        'rto_verification_doc_path',
        'rto_approval_stage',
        'rto_approval_status',
        'rto_approval_date',
        'rto_approval_remarks',
        'rto_approval_doc_path',
        'rto_loan_return_stage',
        'rto_loan_return_status',
        'rto_loan_return_date',
        'rto_loan_return_remarks',
        'rto_loan_return_doc_path',
        'rto_vehicle_detail_stage',
        'rto_vehicle_detail_status',
        'rto_vehicle_detail_date',
        'rto_vehicle_detail_remarks',
        'rto_vehicle_detail_doc_path',
        'rto_final_stage',
        'rto_final_status',
        'rto_final_date',
        'rto_final_remarks',
        'rto_final_doc_path',
        'lead_source_type',
        'lead_source_person_id',
        'lead_source_person_name',
        'referral_commission',
        'referral_log_id',
        'close_amount',
        'guarantor_name',
        'guarantor_contact',
        'guarantor_address',
        'guarantor_pincode',
        'guarantor_city',
        'guarantor_state',
    ];

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
        // RTO Timestamps
        'rto_new_at' => 'datetime',
        'rto_done_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('insurance_address_proof');
        $this->addMediaCollection('insurance_pan_card')->singleFile();
        $this->addMediaCollection('insurance_rc')->singleFile();
        $this->addMediaCollection('aadhar_front')->singleFile();
        $this->addMediaCollection('aadhar_back')->singleFile();
        $this->addMediaCollection('final_document')->singleFile();

        // Future Collections
        $this->addMediaCollection('loan_documents');
        $this->addMediaCollection('rto_documents');
    }

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

    // Address Relationships
    public function currentCity()
    {
        return $this->belongsTo(City::class, 'current_address_city');
    }

    public function currentState()
    {
        return $this->belongsTo(State::class, 'current_address_state');
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, 'permanent_address_city');
    }

    public function permanentState()
    {
        return $this->belongsTo(State::class, 'permanent_address_state');
    }

    // Accessor for Aging
    public function getAgingAttribute()
    {
        if ($this->converted_at) {
            return $this->created_at->diffForHumans($this->converted_at, true);
        }
        return $this->created_at->diffForHumans(null, true);
    }

    // Accessor for RTO Aging
    public function getRtoAgingAttribute()
    {
        if ($this->rto_new_at) {
            $end = $this->rto_done_at ?? now();
            $days = (int) $this->rto_new_at->diffInDays($end);
            return $days . ' ' . Str::plural('day', $days);
        }
        return '-';
    }
}
