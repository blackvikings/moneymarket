<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Models\ReferralPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadSourcePersonController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $results = [];

        if (!$query) {
            return response()->json($results);
        }

        // 1. Search Employees (Users)
        $employees = User::where('name', 'like', "%$query%")
            ->orWhere('employee_id', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'name', 'employee_id']);

        foreach ($employees as $emp) {
            $results[] = [
                'id' => $emp->name, // Using name as ID for the lead_source_person_name field
                'text' => $emp->name . " (Employee - " . $emp->employee_id . ")",
                'type' => 'Employee',
                'name' => $emp->name
            ];
        }

        // 2. Search Past Leads
        $pastLeads = Lead::where('customer_name', 'like', "%$query%")
            ->orWhere('contact_number', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'customer_name', 'contact_number']);

        foreach ($pastLeads as $lead) {
            $results[] = [
                'id' => $lead->customer_name,
                'text' => $lead->customer_name . " (Lead Customer - " . $lead->contact_number . ")",
                'type' => 'Lead',
                'name' => $lead->customer_name
            ];
        }

        // 3. Search Referral Persons
        $referrals = ReferralPerson::where('name', 'like', "%$query%")
            ->orWhere('contact_number', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'name', 'contact_number']);

        foreach ($referrals as $ref) {
            $results[] = [
                'id' => $ref->name,
                'text' => $ref->name . " (Referral - " . $ref->contact_number . ")",
                'type' => 'Referral',
                'name' => $ref->name
            ];
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $person = ReferralPerson::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'address' => $request->address,
            'unique_id' => 'REF' . time(), // Simple unique ID generation
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral person created successfully',
            'person' => $person
        ]);
    }
}
