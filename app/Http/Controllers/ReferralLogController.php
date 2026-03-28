<?php

namespace App\Http\Controllers;

use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ReferralLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ReferralLogController extends Controller
{
    public function __construct()
    {
        // Permissions can be added here later if needed
    }

    public function index(Request $request)
    {
        $query = ReferralLog::with(['lead', 'paidBy:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $referralPerson = $request->input('referral_person');
        $referralType = $request->input('referral_type');

        if ($referralType || $referralPerson) {
            $query->whereHas('lead', function ($q) use ($referralType, $referralPerson) {
                if ($referralType) {
                    if ($referralType == 'Employee') {
                        $sourceNames = \App\Models\User::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType == 'Lead') {
                        $sourceNames = \App\Models\Lead::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('customer_name', 'like', '%' . $referralPerson . '%');
                        })->pluck('customer_name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType == 'Referral') {
                        $sourceNames = \App\Models\ReferralPerson::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    }
                } else {
                    // Only referral_person is filled
                    $q->where('lead_source_person_name', 'like', '%' . $referralPerson . '%');
                }
            });
        }

        $referrals = $query->latest()->paginate(20);

        return view('referral_logs.index', compact('referrals'));
    }

    public function markAsPaid(Request $request, ReferralLog $referral)
    {
        if ($referral->status !== 'Pending') {
            return redirect()->back()->with('error', 'This referral has already been paid.');
        }

        $request->validate([
            'payment_mode' => 'required|in:Cash,Cheque,Online,Bank Transfer',
            'amount' => 'required_if:payment_mode,Cash|numeric',
            'payment_date' => 'required|date',
            'cheque_number' => 'required_if:payment_mode,Cheque',
            'cheque_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'upi_id' => 'required_if:payment_mode,Online',
            'online_payment_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'account_holder_name' => 'required_if:payment_mode,Bank Transfer',
            'account_number' => 'required_if:payment_mode,Bank Transfer',
            'bank_name' => 'required_if:payment_mode,Bank Transfer',
            'ifsc_code' => 'required_if:payment_mode,Bank Transfer',
            'bank_transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = [
            'status' => 'Paid',
            'paid_at' => $request->payment_date,
            'paid_by' => Auth::id(),
            'payment_mode' => $request->payment_mode,
        ];

        if ($request->payment_mode == 'Cash') {
            $data['amount'] = $request->amount;
        } elseif ($request->payment_mode == 'Cheque') {
            $data['cheque_number'] = $request->cheque_number;
            if ($request->hasFile('cheque_image')) {
                $data['cheque_image_path'] = $request->file('cheque_image')->store('cheque_images', 'public');
            }
        } elseif ($request->payment_mode == 'Online') {
            $data['upi_id'] = $request->upi_id;
            if ($request->hasFile('online_payment_image')) {
                $data['online_payment_image_path'] = $request->file('online_payment_image')->store('online_payment_images', 'public');
            }
        } elseif ($request->payment_mode == 'Bank Transfer') {
            $data['account_holder_name'] = $request->account_holder_name;
            $data['account_number'] = $request->account_number;
            $data['bank_name'] = $request->bank_name;
            $data['ifsc_code'] = $request->ifsc_code;
            if ($request->hasFile('bank_transfer_image')) {
                $data['bank_transfer_image_path'] = $request->file('bank_transfer_image')->store('bank_transfer_images', 'public');
            }
        }

        $referral->update($data);

        return redirect()->back()->with('success', 'Referral marked as paid successfully.');
    }

    public function export(Request $request)
    {
        return Excel::download(new ReferralLogExport($request->all()), 'referral_logs.xlsx');
    }
}
