<?php

namespace App\Exports;

use App\Models\Insurance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InsuranceExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Insurance::with(['types.subTypes', 'state', 'city']);

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

        $columns = ['ID', 'Company Name', 'Agent Name', 'Address', 'City', 'State', 'Pincode', 'Types', 'Status', 'Created At'];

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
                    $insurance->address_line,
                    $insurance->city->name ?? '',
                    $insurance->state->name ?? '',
                    $insurance->pincode,
                    $types,
                    $insurance->is_active ? 'Active' : 'Blocked',
                    $insurance->created_at
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
