<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = User::with(['firm', 'reportingManager'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'admin');
            });

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('status', $this->request->status);
        }

        $employees = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employees.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Employee Type', 'Name', 'Contact Number', 'Email', 'DOB', 'Blood Group', 'Firm',
            'Current Address', 'Permanent Address', 'Reporting Manager', 'Bank', 'Branch', 'Account Name', 'Account Number', 'IFSC', 'Status'
        ];

        $callback = function() use ($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($employees as $employee) {
                $currentAddress = "{$employee->current_address_line}, {$employee->current_address_city}, {$employee->current_address_state} - {$employee->current_address_pincode}";
                $permanentAddress = "{$employee->permanent_address_line}, {$employee->permanent_address_city}, {$employee->permanent_address_state} - {$employee->permanent_address_pincode}";

                $row = [
                    $employee->employee_id,
                    $employee->employee_type,
                    $employee->name,
                    $employee->contact_number ? "\t" . $employee->contact_number : '',
                    $employee->email,
                    $employee->dob ? $employee->dob->format('d-m-Y') : '',
                    $employee->blood_group,
                    $employee->firm ? $employee->firm->name : '',
                    $currentAddress,
                    $permanentAddress,
                    $employee->reportingManager ? $employee->reportingManager->name : '',
                    $employee->bank_name,
                    $employee->bank_branch_name,
                    $employee->account_holder_name,
                    $employee->account_number ? "\t" . $employee->account_number : '',
                    $employee->ifsc_code,
                    $employee->status,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
