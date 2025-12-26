<?php

namespace App\Exports;

use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InsuranceExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Insurance::with('types.subTypes');

        if ($this->request->has('company_name') && $this->request->company_name != '') {
            $query->where('company_name', 'like', '%' . $this->request->company_name . '%');
        }

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('is_active', $this->request->status);
        }

        $insurances = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=insurances.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Company Name', 'Agent Name', 'Address', 'Types', 'Status', 'Created At'];

        $callback = function() use ($insurances, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($insurances as $insurance) {
                $types = $insurance->types->map(function($type) {
                    $subTypes = $type->subTypes->pluck('name')->implode(', ');
                    return $type->name . ($subTypes ? " ($subTypes)" : "");
                })->implode('; ');

                $row = [
                    $insurance->id,
                    $insurance->company_name,
                    $insurance->agent_name,
                    $insurance->address,
                    $types,
                    $insurance->is_active ? 'Active' : 'Blocked',
                    $insurance->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
