<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ReferralLog;
use App\Models\ReferralPerson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReferralLogController extends Controller
{
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
                    if ($referralType === 'Employee') {
                        $sourceNames = User::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType === 'Lead') {
                        $sourceNames = Lead::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('customer_name', 'like', '%' . $referralPerson . '%');
                        })->pluck('customer_name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType === 'Referral') {
                        $sourceNames = ReferralPerson::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    }
                } else {
                    $q->where('lead_source_person_name', 'like', '%' . $referralPerson . '%');
                }
            });
        }

        $referrals = $query->latest()->paginate((int) $request->input('per_page', 20));

        return response()->json($referrals);
    }

    public function markAsPaid(Request $request, ReferralLog $referral)
    {
        if ($referral->status !== 'Pending') {
            return response()->json(['message' => 'This referral has already been paid.'], 422);
        }

        $request->validate([
            'payment_mode' => 'required|in:Cash,Cheque,Online,Bank Transfer',
            'amount' => 'required_if:payment_mode,Cash|numeric|min:0',
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

        if ($request->payment_mode === 'Cash') {
            $data['amount'] = $request->amount;
        } elseif ($request->payment_mode === 'Cheque') {
            $data['cheque_number'] = $request->cheque_number;
            if ($request->hasFile('cheque_image')) {
                $data['cheque_image_path'] = $request->file('cheque_image')->store('cheque_images', 'public');
            }
        } elseif ($request->payment_mode === 'Online') {
            $data['upi_id'] = $request->upi_id;
            if ($request->hasFile('online_payment_image')) {
                $data['online_payment_image_path'] = $request->file('online_payment_image')->store('online_payment_images', 'public');
            }
        } elseif ($request->payment_mode === 'Bank Transfer') {
            $data['account_holder_name'] = $request->account_holder_name;
            $data['account_number'] = $request->account_number;
            $data['bank_name'] = $request->bank_name;
            $data['ifsc_code'] = $request->ifsc_code;
            if ($request->hasFile('bank_transfer_image')) {
                $data['bank_transfer_image_path'] = $request->file('bank_transfer_image')->store('bank_transfer_images', 'public');
            }
        }

        $referral->update($data);

        return response()->json([
            'message' => 'Referral marked as paid successfully.',
            'referral' => $referral->fresh(['lead', 'paidBy:id,name']),
        ]);
    }
}
