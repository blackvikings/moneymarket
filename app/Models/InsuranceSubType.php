<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceSubType extends Model
{
    use HasFactory;

    protected $fillable = ['insurance_type_id', 'name'];

    public function type()
    {
        return $this->belongsTo(InsuranceType::class);
    }
}
