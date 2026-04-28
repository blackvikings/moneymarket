<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $this->buildDashboardLeadQuery($user);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $totalLeads = (clone $query)->count();
        $confirmedLeads = (clone $query)->where('status', 'Lead')->count();
        $assignedToBank = (clone $query)->whereNotNull('bank_id')->count();
        $directRtoLeads = (clone $query)
            ->where(function ($q) {
                $q->whereNotNull('rto_stage')
                    ->orWhere('lead_type', 'RTO');
            })
            ->count();
        $directInsuranceLeads = (clone $query)
            ->forInsuranceModule()
            ->where('status', 'Lead')
            ->count();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();
        $thisMonthExpiringInsuranceLeads = (clone $this->buildDashboardLeadQuery($user))
            ->whereNotNull('insurance_lead_stage')
            ->whereBetween('insurance_end_date', [$monthStart, $monthEnd])
            ->count();

        return view(
            'home',
            compact('totalLeads', 'confirmedLeads', 'assignedToBank', 'directRtoLeads', 'directInsuranceLeads', 'thisMonthExpiringInsuranceLeads')
        );
    }

    private function buildDashboardLeadQuery(User $user)
    {
        $query = Lead::query();

        // Highest access wins when a user has multiple roles.
        if ($user->hasRole('admin')) {
            return $query;
        }

        $subordinateIds = $this->getReportingTreeUserIds($user->id);

        return $query->where(function ($q) use ($user, $subordinateIds) {
            $q->where('assigned_to', $user->id);

            if (!empty($subordinateIds)) {
                $q->orWhereIn('assigned_to', $subordinateIds);
            }

            $q->orWhere('reporting_manager_id', $user->id);

            if ($this->hasReportingManagerIdsColumn()) {
                $q->orWhereJsonContains('reporting_manager_ids', $user->id);
            }
        });
    }

    private function getReportingTreeUserIds(int $managerId): array
    {
        $visibleUserIds = [];
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

    private function hasReportingManagerIdsColumn(): bool
    {
        static $hasColumn = null;

        if ($hasColumn === null) {
            $hasColumn = Schema::hasColumn('leads', 'reporting_manager_ids');
        }

        return $hasColumn;
    }
}
