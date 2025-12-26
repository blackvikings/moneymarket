<?php

namespace App\Exports;

use App\Models\VehicleManufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VehicleManufactureExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = VehicleManufacture::with('vehicleNames');

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $manufactures = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=vehicle_manufactures.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Manufacture Name', 'Vehicle Names', 'Status', 'Created At'];

        $callback = function() use ($manufactures, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($manufactures as $manufacture) {
                $vehicleNames = $manufacture->vehicleNames->pluck('name')->implode(', ');
                $row = [
                    $manufacture->id,
                    $manufacture->name,
                    $vehicleNames,
                    $manufacture->is_active ? 'Active' : 'Blocked',
                    $manufacture->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
