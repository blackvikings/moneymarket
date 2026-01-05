<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RtoController extends Controller
{
    protected $stages = [
        'New',
        'Checking',
        'Loan Return',
        'Vehicle Detail',
        'Insurance',
        'RTO Done'
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Lead::with(['vehicleName', 'manufacture'])->where(function($q) {
            $q->whereNotNull('rto_stage')
              ->orWhere('crm_stage', 'Closed');
        });

        // Non-admin users can only see their own leads
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('stage')) {
            $query->where('rto_stage', $request->stage);
        }

        $leads = $query->latest()->paginate(15);

        return response()->json($leads);
    }

    public function show(Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lead->load(['bank', 'rtoAgent', 'rtoCloserDeposits', 'rtoChallans', 'insurance', 'vehicleName', 'manufacture']);
        return response()->json($lead);
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:Yes,No',
            'current_stage' => 'required|in:' . implode(',', $this->stages),
        ]);

        $currentStageIndex = array_search($request->current_stage, $this->stages);

        if ($request->action === 'Yes') {
            if ($currentStageIndex < count($this->stages) - 1) {
                $nextStage = $this->stages[$currentStageIndex + 1];
                $lead->update(['rto_stage' => $nextStage]);
                return response()->json(['message' => 'Stage updated to ' . $nextStage, 'lead' => $lead]);
            }
        }

        return response()->json(['message' => 'No action taken.']);
    }
}
