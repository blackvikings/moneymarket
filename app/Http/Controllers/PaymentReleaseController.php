<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

class PaymentReleaseController extends Controller
{
    public function index()
    {
        $leads = Lead::where('crm_stage', 'Disbursal')->paginate(10);
        return view('crm.payment-release', compact('leads'));
    }

    public function toggleStatus(Request $request, Lead $lead)
    {
        $status = $request->input('status');
        if (in_array($status, ['Pending', 'Done'])) {
            $lead->payment_release_status = $status;
            $lead->save();
            return back()->with('success', 'Status updated successfully.');
        }
        return back()->with('error', 'Invalid status.');
    }

    public function storeDetails(Request $request, Lead $lead)
    {
        $request->validate([
            'payment_amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_remarks' => 'nullable|string',
        ]);

        $lead->payment_amount = $request->payment_amount;
        $lead->payment_date = $request->payment_date;
        $lead->payment_remarks = $request->payment_remarks;
        $lead->payment_release_status = 'Done';
        $lead->save();

        return back()->with('success', 'Payment details saved and status updated to Done.');
    }
}
