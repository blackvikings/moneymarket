<?php

namespace App\Http\Controllers;

use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralLogController extends Controller
{
    public function __construct()
    {
        // Permissions can be added here later if needed
    }

    public function index(Request $request)
    {
        $query = ReferralLog::with(['lead.customer_name', 'paidBy:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
}
