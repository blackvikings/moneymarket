<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

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
        $totalLeads = Lead::count();
        $confirmedLeads = Lead::where('status', 'Lead')->count();
        $assignedToBank = Lead::whereNotNull('bank_id')->count();

        return view('home', compact('totalLeads', 'confirmedLeads', 'assignedToBank'));
    }
}
