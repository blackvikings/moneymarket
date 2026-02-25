<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\Firm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find firm by name or ID
        $firmId = null;
        if (isset($row['firm_name'])) {
            $firm = Firm::where('name', $row['firm_name'])->first();
            if ($firm) {
                $firmId = $firm->id;
            }
        } elseif (isset($row['firm_id'])) {
            $firmId = $row['firm_id'];
        }

        // Find user to assign to by unique_id
        $assignedTo = null;
        if (isset($row['employee_id'])) {
            $user = User::where('unique_id', $row['employee_id'])->first();
            if ($user) {
                $assignedTo = $user->id;
            }
        }

        return new Lead([
            'customer_name' => $row['customer_name'],
            'contact_number' => $row['contact_number'],
            'father_name' => $row['father_name'] ?? null,
            'email' => $row['email'] ?? null,
            'firm_id' => $firmId,
            'residence_type' => $row['residence_type'] ?? 'Rented',
            'current_address_line' => $row['current_address_line'] ?? null,
            'permanent_address_line' => $row['permanent_address_line'] ?? null,
            'assigned_to' => $assignedTo,
            'status' => 'New',
            'created_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required',
            'contact_number' => 'required',
            'employee_id' => 'nullable|exists:users,unique_id',
        ];
    }
}
