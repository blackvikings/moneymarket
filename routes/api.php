<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\RtoController;
use App\Http\Controllers\Api\InsuranceLeadController;
use App\Http\Controllers\Api\ReferralLogController;
use App\Http\Controllers\Api\DataController;

// Public route for login
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes for employees
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Master Data APIs
    Route::get('/firms', [DataController::class, 'getFirms']);
    Route::get('/states', [DataController::class, 'getStates']);
    Route::get('/cities/{state}', [DataController::class, 'getCities']);
    Route::get('/location/{pincode}', [DataController::class, 'getLocationByPincode']);

    // Leads API
    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);
    Route::get('/leads/{lead}', [LeadController::class, 'show']);
    Route::put('/leads/{lead}', [LeadController::class, 'update']);
    Route::post('/leads/{lead}/status', [LeadController::class, 'updateStatus']);
    Route::post('/leads/{lead}/assign-bank', [LeadController::class, 'assignBank']);
    Route::get('/leads/meta/assignable-users', [LeadController::class, 'assignableUsers']);
    Route::get('/leads/meta/managers/{user}', [LeadController::class, 'managersByUser']);
    Route::get('/lead-source-persons/search', [LeadController::class, 'searchLeadSourcePersons']);
    Route::post('/lead-source-persons', [LeadController::class, 'storeLeadSourcePerson']);

    // CRM (Bank Assign) API
    Route::get('/crm', [CrmController::class, 'index']);
    Route::get('/crm/{lead}', [CrmController::class, 'show']);
    Route::post('/crm/{lead}/stage', [CrmController::class, 'updateStage']);

    // RTO API
    Route::get('/rto', [RtoController::class, 'index']);
    Route::post('/rto', [RtoController::class, 'store']);
    Route::post('/rto/store-insurance-lead', [RtoController::class, 'storeInsuranceLead']);
    Route::get('/rto/{lead}', [RtoController::class, 'show']);
    Route::post('/rto/{lead}/stage', [RtoController::class, 'updateStage']);
    Route::post('/rto/{lead}/update-vehicle-detail', [RtoController::class, 'updateVehicleDetail']);
    Route::post('/rto/{lead}/assign-agent', [RtoController::class, 'assignAgent']);
    Route::post('/rto/{lead}/update-challan', [RtoController::class, 'updateChallan']);
    Route::post('/rto/{lead}/upload-doc', [RtoController::class, 'uploadRtoDoc']);
    Route::post('/rto/{lead}/update-verification', [RtoController::class, 'updateVerification']);
    Route::post('/rto/{lead}/update-approval', [RtoController::class, 'updateApproval']);
    Route::post('/rto/{lead}/rto-done', [RtoController::class, 'updateRtoDone']);
    Route::post('/rto/{lead}/reject', [RtoController::class, 'rejectLead']);

    // Insurance Lead API
    Route::get('/insurance-leads', [InsuranceLeadController::class, 'index']);
    Route::get('/insurance-leads/expiring', [InsuranceLeadController::class, 'expiring']);
    Route::post('/insurance-leads', [InsuranceLeadController::class, 'store']);
    Route::get('/insurance-leads/{lead}', [InsuranceLeadController::class, 'show']);
    Route::post('/insurance-leads/{lead}/details', [InsuranceLeadController::class, 'updateDetails']);

    // Referral Log API
    Route::get('/referral-logs', [ReferralLogController::class, 'index']);
    Route::patch('/referral-logs/{referral}/mark-as-paid', [ReferralLogController::class, 'markAsPaid']);
});
