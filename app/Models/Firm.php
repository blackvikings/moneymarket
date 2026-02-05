<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address', // Keeping for backward compatibility if needed, but we'll use the new fields
        'address_line',
        'pincode',
        'state_id',
        'city_id',
        'is_active'
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
