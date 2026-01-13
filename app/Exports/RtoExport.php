<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class RtoExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download($filename = 'rto-leads.csv')
    {
        $query = Lead::with(['vehicleName', 'manufacture', 'assignedTo'])
            ->where(function($q) {
                $q->whereNotNull('rto_stage')
                  ->orWhere('crm_stage', 'Closed');
            });

        if ($this->request->has('stage') && $this->request->stage != '') {
            $query->where('rto_stage', $this->request->stage);
        }

        $leads = $query->latest()->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID',
            'Customer Name',
            'Contact Number',
            'RTO Stage',
            'Assigned To',
            'Vehicle Name',
            'Manufacturer',
            'RC Number',
        ];

        $callback = function() use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                $row = [
                    $lead->id,
                    $lead->customer_name,
                    $lead->contact_number,
                    $lead->rto_stage,
                    $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned',
                    $lead->vehicleName ? $lead->vehicleName->name : '-',
                    $lead->manufacture ? $lead->manufacture->name : '-',
                    $lead->rc_number,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
