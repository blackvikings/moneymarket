<?php

namespace App\Exports;

use App\Models\Firm;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FirmExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Firm::with(['state', 'city']);

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $firms = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=firms.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Address', 'City', 'State', 'Pincode', 'Status', 'Created At'];

        $callback = function() use ($firms, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($firms as $firm) {
                $row = [
                    $firm->id,
                    $firm->name,
                    $firm->address_line,
                    $firm->city->name ?? '',
                    $firm->state->name ?? '',
                    $firm->pincode,
                    $firm->is_active ? 'Active' : 'Blocked',
                    $firm->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
