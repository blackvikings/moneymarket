<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;

class PaymentReleaseController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['bank', 'bankBranch'])
            ->where('crm_stage', 'Disbursal')
            ->paginate(10);
        return view('crm.payment-release', compact('leads'));
    }

    public function show(Lead $lead)
    {
        $lead->load([
            'assignedTo',
            'bank',
            'bankBranch',
            'createdBy',
            'currentCity',
            'currentState',
            'statusLogs.user',
        ]);

        return view('crm.payment-release-show', compact('lead'));
    }

    public function toggleStatus(Request $request, Lead $lead)
    {
        $status = $request->input('status');
        if (in_array($status, ['Pending', 'Done'])) {
            if ($lead->payment_release_status === 'Done' && $status === 'Pending') {
                return back()->with('error', 'This payment release is already completed and cannot be reopened.');
            }
            $lead->payment_release_status = $status;
            $lead->save();
            return back()->with('success', 'Status updated successfully.');
        }
        return back()->with('error', 'Invalid status.');
    }

    public function storeDetails(Request $request, Lead $lead)
    {
        if ($lead->payment_release_status === 'Done') {
            return back()->with('error', 'Payment release is already completed for this lead.');
        }

        if ($lead->disbursal_amount === null) {
            return back()->with('error', 'Disbursal amount is missing for this lead.');
        }

        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:Cash,Cheque,Online,Bank Transfer',
            'payment_amount' => 'required_if:payment_type,Cash,Cheque,Online,Bank Transfer|nullable|numeric',
            'payment_date' => 'required|date',
            'payment_remarks' => 'nullable|string',
            'payment_cheque_number' => 'required_if:payment_type,Cheque|nullable|string|max:255',
            'payment_cheque_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_upi_id' => 'required_if:payment_type,Online|nullable|string|max:255',
            'payment_online_payment_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_account_holder_name' => 'required_if:payment_type,Bank Transfer|nullable|string|max:255',
            'payment_account_number' => 'required_if:payment_type,Bank Transfer|nullable|string|max:255',
            'payment_bank_name' => 'required_if:payment_type,Bank Transfer|nullable|string|max:255',
            'payment_ifsc_code' => 'required_if:payment_type,Bank Transfer|nullable|string|max:255',
            'payment_bank_transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('payment_release_lead_id', $lead->id);
        }

        if (round((float) $request->payment_amount, 2) !== round((float) $lead->disbursal_amount, 2)) {
            return back()
                ->withErrors(['payment_amount' => 'Payment amount must match the disbursal amount of ' . number_format((float) $lead->disbursal_amount, 2) . '.'])
                ->withInput()
                ->with('payment_release_lead_id', $lead->id);
        }

        // Reset all mode-specific fields to avoid stale data if a lead is edited later.
        $lead->payment_type = $request->payment_type;
        $lead->payment_cheque_number = null;
        $lead->payment_cheque_image_path = null;
        $lead->payment_upi_id = null;
        $lead->payment_online_payment_image_path = null;
        $lead->payment_account_holder_name = null;
        $lead->payment_account_number = null;
        $lead->payment_bank_name = null;
        $lead->payment_ifsc_code = null;
        $lead->payment_bank_transfer_image_path = null;

        $lead->payment_amount = (float) $request->payment_amount;
        $lead->payment_date = $request->payment_date;
        $lead->payment_remarks = $request->payment_remarks;

        if ($request->payment_type === 'Cheque') {
            $lead->payment_cheque_number = $request->payment_cheque_number;
            if ($request->hasFile('payment_cheque_image')) {
                $lead->payment_cheque_image_path = $request->file('payment_cheque_image')
                    ->store('payment_cheque_images', 'public');
            }
        } elseif ($request->payment_type === 'Online') {
            $lead->payment_upi_id = $request->payment_upi_id;
            if ($request->hasFile('payment_online_payment_image')) {
                $lead->payment_online_payment_image_path = $request->file('payment_online_payment_image')
                    ->store('payment_online_payment_images', 'public');
            }
        } elseif ($request->payment_type === 'Bank Transfer') {
            $lead->payment_account_holder_name = $request->payment_account_holder_name;
            $lead->payment_account_number = $request->payment_account_number;
            $lead->payment_bank_name = $request->payment_bank_name;
            $lead->payment_ifsc_code = $request->payment_ifsc_code;
            if ($request->hasFile('payment_bank_transfer_image')) {
                $lead->payment_bank_transfer_image_path = $request->file('payment_bank_transfer_image')
                    ->store('payment_bank_transfer_images', 'public');
            }
        }

        $lead->payment_release_status = 'Done';
        $lead->payment_release_detail_at = now();
        $lead->save();

        return back()->with('success', 'Payment details saved and status updated to Done.');
    }
}
