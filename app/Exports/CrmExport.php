<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class CrmExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download($filename = 'crm-leads.csv')
    {
        $query = Lead::with(['bank', 'bankBranch', 'assignedTo'])
            ->where('status', 'Lead')
            ->whereNotNull('bank_id');

        if ($this->request->has('stage') && $this->request->stage != '') {
            $query->where('crm_stage', $this->request->stage);
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
            'Bank',
            'Branch',
            'CRM Stage',
            'Assigned To',
            'Start Date',
            'Disbursal Date',
            'Finance Amount',
            'Disbursal Amount',
        ];

        $callback = function() use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                $row = [
                    $lead->id,
                    $lead->customer_name,
                    $lead->contact_number,
                    $lead->bank ? $lead->bank->name : '-',
                    $lead->bankBranch ? $lead->bankBranch->name : '-',
                    $lead->crm_stage,
                    $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned',
                    $lead->converted_at ? $lead->converted_at->format('d-m-Y') : '-',
                    $lead->disbursal_date ? $lead->disbursal_date->format('d-m-Y') : '-',
                    $lead->finance_amount,
                    $lead->disbursal_amount,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
