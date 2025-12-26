<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleManufacture extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function vehicleNames()
    {
        return $this->hasMany(VehicleName::class);
    }
}
