<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'name'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
