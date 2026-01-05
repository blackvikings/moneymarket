<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CrmController extends Controller
{
    protected $stages = [
        'New',
        'Login',
        'FI Document',
        'Vehicle Evaluation',
        'Credit',
        'Approved',
        'Agreement',
        'Disbursal',
        'Closed'
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['bank', 'bankBranch'])
            ->where('status', 'Lead')
            ->whereNotNull('bank_id');

        // Non-admin users can only see their own leads
        if (!$user->hasRole('admin')) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('stage')) {
            $query->where('crm_stage', $request->stage);
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

        $lead->load(['bank', 'bankBranch', 'firm']);
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
                $lead->crm_stage = $nextStage;

                $timestampField = 'crm_' . strtolower(str_replace(' ', '_', $request->current_stage)) . '_at';
                if (Schema::hasColumn('leads', $timestampField)) {
                    $lead->$timestampField = now();
                }

                $lead->save();
                return response()->json(['message' => 'Stage updated to ' . $nextStage, 'lead' => $lead]);
            }
        }

        return response()->json(['message' => 'No action taken.']);
    }
}
