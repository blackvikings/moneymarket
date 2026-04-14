<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'amount',
        'status',
        'payment_mode',
        'cheque_number',
        'cheque_image_path',
        'upi_id',
        'online_payment_image_path',
        'account_holder_name',
        'account_number',
        'bank_name',
        'ifsc_code',
        'bank_transfer_image_path',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
