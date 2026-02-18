<?php

namespace App\Exports;

use App\Models\Bank;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BankExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Bank::with(['branches.state', 'branches.city']);

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $banks = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=banks.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Bank Name', 'Branch Name', 'Address', 'City', 'State', 'Pincode', 'Status', 'Created At'];

        $callback = function() use ($banks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($banks as $bank) {
                if ($bank->branches->count() > 0) {
                    foreach ($bank->branches as $branch) {
                        $row = [
                            $bank->id,
                            $bank->name,
                            $branch->name,
                            $branch->address_line,
                            $branch->city->name ?? '',
                            $branch->state->name ?? '',
                            $branch->pincode,
                            $bank->is_active ? 'Active' : 'Blocked',
                            $bank->created_at
                        ];
                        fputcsv($file, $row);
                    }
                } else {
                    // If no branches, still export the bank info
                    $row = [
                        $bank->id,
                        $bank->name,
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        $bank->is_active ? 'Active' : 'Blocked',
                        $bank->created_at
                    ];
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
