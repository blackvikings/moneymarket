<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;

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
        'is_first_login',
        'employee_id',
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
        'proof_id_number',
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
            'is_first_login' => 'boolean',
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

    /**
     * Relationship for denied permissions.
     */
    public function deniedPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_denied_permissions', 'user_id', 'permission_id');
    }

    /**
     * Override the hasPermissionTo method to check for denied permissions.
     *
     * @param string|int|\Spatie\Permission\Contracts\Permission $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionClass = $this->getPermissionClass();

        // First, resolve the permission object/name/id
        if (is_string($permission)) {
            // Call findByName statically on the class string
            $permissionObj = $permissionClass::findByName(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        } elseif (is_int($permission)) {
            // Call findById statically on the class string
            $permissionObj = $permissionClass::findById(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        } else {
            $permissionObj = $permission;
        }

        if (!$permissionObj) {
            return false;
        }

        // Check if this permission is explicitly denied for the user
        // Use permissions.id to avoid ambiguity
        if ($this->deniedPermissions()->where('permissions.id', $permissionObj->id)->exists()) {
            return false;
        }

        // If not denied, proceed with standard Spatie permission check
        return $this->hasDirectPermission($permissionObj) || $this->hasPermissionViaRole($permissionObj);
    }
}
