<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InsuranceLeadExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download($filename = 'insurance-leads.csv')
    {
        $query = Lead::with(['insurance', 'vehicleName', 'manufacture'])
            ->whereNotNull('insurance_lead_stage');

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
            'Insurance Company',
            'Policy Number',
            'Start Date',
            'End Date',
            'Premium Amount',
            'IDV Amount',
            'Vehicle Name',
            'Manufacturer',
        ];

        $callback = function() use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                $row = [
                    $lead->id,
                    $lead->customer_name,
                    $lead->contact_number,
                    $lead->insurance ? $lead->insurance->name : '-',
                    $lead->insurance_policy_number,
                    $lead->insurance_start_date ? $lead->insurance_start_date->format('Y-m-d') : '-',
                    $lead->insurance_end_date ? $lead->insurance_end_date->format('Y-m-d') : '-',
                    $lead->insurance_premium_amount,
                    $lead->insurance_idv_amount,
                    $lead->vehicleName ? $lead->vehicleName->name : '-',
                    $lead->manufacture ? $lead->manufacture->name : '-',
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
