<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // Import Log facade

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

        // Add ageing to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->ageing = $lead->created_at->diffForHumans();
            return $lead;
        });

        return response()->json($leads);
    }

    public function show(Request $request, Lead $lead)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized to perform this action.');
        }

        $lead->load(['bank', 'bankBranch', 'firm']);
        return response()->json($lead);
    }

    public function updateStage(Request $request, Lead $lead)
    {
         Log::info('CRM Update Stage Request:', $request->all()); // Removed logging for direct console debugging

        $user = Auth::user();

        if (!$user->hasRole('admin') && $lead->assigned_to !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized to perform this action.');
        }
//        dd($request->all());
        // Validate the request for completed_stages
        $request->validate([
            'completed_stages' => 'nullable|array', // Allow array of stages, can be empty
            'completed_stages.*' => 'in:' . implode(',', $this->stages), // Each stage must be valid
        ]);

        $currentLeadStageIndex = array_search($lead->crm_stage, $this->stages);
        $newStage = $lead->crm_stage; // Initialize with current stage

        // If completed_stages are provided, find the highest selected stage that is a valid progression
        if ($request->has('completed_stages') && is_array($request->completed_stages)) {
            $selectedStages = $request->completed_stages;
            $highestSelectedStageIndex = $currentLeadStageIndex;
            $potentialNewStage = $lead->crm_stage;

            foreach ($selectedStages as $stage) {
                $stageIndex = array_search($stage, $this->stages);
                // Ensure the selected stage is valid and is a progression from the current stage
                if ($stageIndex !== false && $stageIndex > $highestSelectedStageIndex) {
                    $highestSelectedStageIndex = $stageIndex;
                    $potentialNewStage = $stage;
                }
            }

            // Check if the potential new stage is actually different and a valid progression
            if ($potentialNewStage !== $lead->crm_stage) {
                $lead->crm_stage = $potentialNewStage;

                // Update timestamp for the new stage
                // The timestamp field should correspond to the *new* stage being set
                $timestampField = 'crm_' . strtolower(str_replace(' ', '_', $potentialNewStage)) . '_at';
                if (Schema::hasColumn('leads', $timestampField)) {
                    $lead->$timestampField = now();
                }

                $lead->save();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Stage updated to ' . $potentialNewStage, 'lead' => $lead]);
                }
                return redirect()->back()->with('success', 'Stage updated to ' . $potentialNewStage);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'No action taken or no stage change detected.']);
        }
        return redirect()->back()->with('info', 'No action taken or no stage change detected.');
    }
}
