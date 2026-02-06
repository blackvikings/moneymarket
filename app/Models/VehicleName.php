<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleName extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_manufacture_id', 'name', 'variant'];

    public function manufacture()
    {
        return $this->belongsTo(VehicleManufacture::class);
    }
}
