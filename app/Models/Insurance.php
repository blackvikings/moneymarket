<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'agent_name',
        'address', // Keeping for backward compatibility
        'address_line',
        'pincode',
        'state_id',
        'city_id',
        'is_active'
    ];

    public function types()
    {
        return $this->hasMany(InsuranceType::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
