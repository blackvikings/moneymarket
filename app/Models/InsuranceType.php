<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceType extends Model
{
    use HasFactory;

    protected $fillable = ['insurance_id', 'name'];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function subTypes()
    {
        return $this->hasMany(InsuranceSubType::class);
    }
}
