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
        $query = $this->buildDashboardLeadQuery($user);

        $totalLeads = $query->clone()->count();
        $confirmedLeads = $query->clone()->where('status', 'Lead')->count();
        $assignedToBank = $query->clone()->whereNotNull('bank_id')->count();
        $directRtoLeads = Lead::where(function ($q) {
            $q->whereNotNull('rto_stage')
                ->orWhere('lead_type', 'RTO');
        })->count();
        $directInsuranceLeads = $query->clone()
            ->where('lead_type', 'Insurance')
            ->where('status', 'Lead')
            ->count();

        return view(
            'home',
            compact('totalLeads', 'confirmedLeads', 'assignedToBank', 'directRtoLeads', 'directInsuranceLeads')
        );
    }

    private function buildDashboardLeadQuery(User $user)
    {
        $query = Lead::query();

        // Highest access wins when a user has multiple roles.
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasAnyRole(['Branch Manager', 'Team Lead'])) {
            $visibleUserIds = $this->getReportingTreeUserIds($user->id);

            return $query->whereIn('assigned_to', $visibleUserIds);
        }

        return $query->where('assigned_to', $user->id);
    }

    private function getReportingTreeUserIds(int $managerId): array
    {
        $visibleUserIds = [$managerId];
        $frontier = [$managerId];

        while (!empty($frontier)) {
            $children = User::whereIn('reporting_manager_id', $frontier)
                ->pluck('id')
                ->all();

            $frontier = array_values(array_diff($children, $visibleUserIds));
            $visibleUserIds = array_values(array_unique(array_merge($visibleUserIds, $frontier)));
        }

        return $visibleUserIds;
    }
}
