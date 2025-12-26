<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_education';

    protected $fillable = [
        'user_id',
        'degree',
        'institution',
        'passing_year',
        'percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
