<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class LeadExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $query = Lead::with(['assignedTo', 'bank', 'bankBranch', 'createdBy', 'firm', 'manufacture', 'currentCity', 'currentState', 'permanentCity', 'permanentState']);

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('status', $this->request->status);
        }

        if ($this->request->has('assigned_to') && $this->request->assigned_to != '') {
            $query->where('assigned_to', $this->request->assigned_to);
        }

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('Team Lead')) {
             $query->where('assigned_to', Auth::id());
        }

        $leads = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=leads.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Customer Name', 'Father Name', 'Firm', 'Contact Number', 'Email',
            'Residence Type', 'Current Address', 'Permanent Address',
            'Job Profile', 'Income Proof', 'Approx Income',
            'Lead Type', 'Loan Type', 'Vehicle Type', 'Purchase Type', 'Total Loan Amount',
            'Status', 'Assigned To', 'Created By', 'Bank', 'Branch', 'Follow Up Date', 'Remark', 'Created At', 'Aging'
        ];

        $callback = function() use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                $currentCityName = $lead->currentCity ? $lead->currentCity->name : $lead->current_address_city;
                $currentStateName = $lead->currentState ? $lead->currentState->name : $lead->current_address_state;
                $permanentCityName = $lead->permanentCity ? $lead->permanentCity->name : $lead->permanent_address_city;
                $permanentStateName = $lead->permanentState ? $lead->permanentState->name : $lead->permanent_address_state;

                $currentAddress = "{$lead->current_address_line}, {$currentCityName}, {$currentStateName} - {$lead->current_address_pincode}";
                $permanentAddress = "{$lead->permanent_address_line}, {$permanentCityName}, {$permanentStateName} - {$lead->permanent_address_pincode}";

                $row = [
                    $lead->id,
                    $lead->customer_name,
                    $lead->father_name,
                    $lead->firm ? $lead->firm->name : '',
                    $lead->contact_number,
                    $lead->email,
                    $lead->residence_type,
                    $currentAddress,
                    $permanentAddress,
                    $lead->job_profile_type,
                    $lead->income_proof_available ? 'Yes' : 'No',
                    $lead->approx_income,
                    $lead->lead_type,
                    $lead->loan_type,
                    $lead->vehicle_loan_type,
                    $lead->vehicle_purchase_type,
                    $lead->total_loan_amount,
                    $lead->status,
                    $lead->assignedTo ? $lead->assignedTo->name : '',
                    $lead->createdBy ? $lead->createdBy->name : '',
                    $lead->bank ? $lead->bank->name : '',
                    $lead->bankBranch ? $lead->bankBranch->name : '',
                    $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '',
                    $lead->remark,
                    $lead->created_at->format('Y-m-d H:i:s'),
                    $lead->aging,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
