<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\Firm;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find firm by name or ID, or use a default if needed.
        // For simplicity, assuming 'firm_id' is provided or we look up by name.
        // If firm name is provided in CSV:
        $firmId = null;
        if (isset($row['firm_name'])) {
            $firm = Firm::where('name', $row['firm_name'])->first();
            if ($firm) {
                $firmId = $firm->id;
            }
        } elseif (isset($row['firm_id'])) {
            $firmId = $row['firm_id'];
        }

        // If no firm found, you might want to skip or assign a default.
        // For now, let's assume validation handles required fields.

        return new Lead([
            'customer_name' => $row['customer_name'],
            'contact_number' => $row['contact_number'],
            'father_name' => $row['father_name'] ?? null,
            'email' => $row['email'] ?? null,
            'firm_id' => $firmId,
            'residence_type' => $row['residence_type'] ?? 'Rented',
            'current_address_line' => $row['current_address_line'] ?? null,
            'permanent_address_line' => $row['permanent_address_line'] ?? null,
            'status' => 'New',
            'created_by' => Auth::id(),
            'assigned_to' => 'Unassigned', // Assign to uploader by default, or handle logic
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required',
            'contact_number' => 'required',
            // Add other rules as needed
        ];
    }
}
