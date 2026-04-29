<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\BrevoPasswordResetNotification;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasRoles {
        hasPermissionTo as hasSpatiePermissionTo;
    }

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
        'bank_name',
        'bank_branch_name',
        'account_holder_name',
        'account_number',
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

    public function sendPasswordResetNotification($token): void
    {
        (new BrevoPasswordResetNotification($token))->send($this);
    }

    // public function bank()
    // {
    //     return $this->belongsTo(Bank::class);
    // }

    // public function bankBranch()
    // {
    //     return $this->belongsTo(BankBranch::class);
    // }

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
        if ($this->hasRole('admin')) {
            return true;
        }

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
        $isDenied = $this->deniedPermissions()->where('permissions.id', $permissionObj->id)->exists();

        if ($isDenied) {
             Log::info("Permission '{$permissionObj->name}' is DENIED for user {$this->id}");
            return false;
        }

        // If not denied, proceed with standard Spatie permission check
        $hasPermission = $this->hasSpatiePermissionTo($permissionObj, $guardName);

         Log::info("Permission '{$permissionObj->name}' check for user {$this->id}: " . ($hasPermission ? 'GRANTED' : 'NOT GRANTED'));

        return $hasPermission;
    }
}
