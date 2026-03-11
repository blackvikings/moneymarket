<?php

namespace App\Http\Controllers;

use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ReferralLogExport;
use Maatwebsite\Excel\Facades\Excel;

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

    public function markAsPaid(ReferralLog $referral)
    {
        if ($referral->status === 'Pending') {
            $referral->update([
                'status' => 'Paid',
                'paid_at' => now(),
                'paid_by' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Referral marked as paid.');
        }
        return redirect()->back()->with('error', 'This referral has already been paid.');
    }

    public function export(Request $request)
    {
        return Excel::download(new ReferralLogExport($request->all()), 'referral_logs.xlsx');
    }
}
