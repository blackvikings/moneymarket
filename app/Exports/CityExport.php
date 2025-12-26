<?php

namespace App\Exports;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CityExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = City::query();

        if ($this->request->has('name') && $this->request->name != '') {
            $query->where('name', 'like', '%' . $this->request->name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $cities = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=cities.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Status', 'Created At'];

        $callback = function() use ($cities, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($cities as $city) {
                $row = [
                    $city->id,
                    $city->name,
                    $city->is_active ? 'Active' : 'Blocked',
                    $city->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
