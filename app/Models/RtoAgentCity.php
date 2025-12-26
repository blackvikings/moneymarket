<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtoAgentCity extends Model
{
    use HasFactory;

    protected $fillable = ['rto_agent_id', 'city_name'];

    public function agent()
    {
        return $this->belongsTo(RtoAgent::class);
    }
}
