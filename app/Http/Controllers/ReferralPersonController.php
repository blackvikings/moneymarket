<?php

namespace App\Http\Controllers;

use App\Models\ReferralPerson;
use Illuminate\Http\Request;

class ReferralPersonController extends Controller
{
    public function index()
    {
        $referralPersons = ReferralPerson::latest()->paginate(20);
        return view('referral_persons.index', compact('referralPersons'));
    }

    public function create()
    {
        return view('referral_persons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        ReferralPerson::create($request->all());

        return redirect()->route(get_route_prefix() . 'referral-persons.index')->with('success', 'Referral person created successfully.');
    }

    public function edit(ReferralPerson $referralPerson)
    {
        return view('referral_persons.edit', compact('referralPerson'));
    }

    public function update(Request $request, ReferralPerson $referralPerson)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $referralPerson->update($request->all());

        return redirect()->route(get_route_prefix() . 'referral-persons.index')->with('success', 'Referral person updated successfully.');
    }

    public function destroy(ReferralPerson $referralPerson)
    {
        $referralPerson->delete();
        return redirect()->route(get_route_prefix() . 'referral-persons.index')->with('success', 'Referral person deleted successfully.');
    }
}
