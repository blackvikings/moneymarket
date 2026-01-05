<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_type',
        'contact_number',
        'dob',
        'blood_group',
        'firm_id',
        'current_address_line',
        'current_address_pincode',
        'current_address_city',
        'current_address_state',
        'permanent_address_line',
        'permanent_address_pincode',
        'permanent_address_city',
        'permanent_address_state',
        'address_proof_path',
        'proof_id_type',
        'reporting_manager_id',
        'bank_id',
        'bank_branch_id',
        'account_holder_name',
        'ifsc_code',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
        ];
    }

    public function education()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function reportingManager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bankBranch()
    {
        return $this->belongsTo(BankBranch::class);
    }
}
