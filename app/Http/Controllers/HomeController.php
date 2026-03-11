<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $query = Lead::query();

        // --- Hierarchy Logic ---
        if ($user->hasRole('admin')) {
            // Admin sees all leads
        } elseif ($user->hasRole('Team Lead')) {
            // Team Lead sees own leads + subordinates' leads
            $subordinateIds = User::where('reporting_manager_id', $user->id)->pluck('id')->toArray();
            $subordinateIds[] = $user->id; // Include self
            $query->whereIn('assigned_to', $subordinateIds);
        } else {
            // Other users (Tele Caller, etc.) see only their own leads
            $query->where('assigned_to', $user->id);
        }

        $totalLeads = $query->clone()->count();
        $confirmedLeads = $query->clone()->where('status', 'Lead')->count();
        $assignedToBank = $query->clone()->whereNotNull('bank_id')->count();

        return view('home', compact('totalLeads', 'confirmedLeads', 'assignedToBank'));
    }
}
