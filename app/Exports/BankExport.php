<?php

namespace App\Exports;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BankExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Bank::with('branches');

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

        $columns = ['ID', 'Name', 'Branches', 'Status', 'Created At'];

        $callback = function() use ($banks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($banks as $bank) {
                $branches = $bank->branches->pluck('name')->implode(', ');
                $row = [
                    $bank->id,
                    $bank->name,
                    $branches,
                    $bank->is_active ? 'Active' : 'Blocked',
                    $bank->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
