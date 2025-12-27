<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtoCloserDeposit extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'amount', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
