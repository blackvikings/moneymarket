<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'file_path',
        'document_type',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
