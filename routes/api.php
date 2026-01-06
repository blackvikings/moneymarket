<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\RtoController;
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

    // CRM (Bank Assign) API
    Route::get('/crm', [CrmController::class, 'index']);
    Route::get('/crm/{lead}', [CrmController::class, 'show']);
    Route::post('/crm/{lead}/stage', [CrmController::class, 'updateStage']);

    // RTO API
    Route::get('/rto', [RtoController::class, 'index']);
    Route::post('/rto', [RtoController::class, 'store']);
    Route::post('/rto/create-insurance-lead', [RtoController::class, 'storeInsuranceLead']);
    Route::get('/rto/{lead}', [RtoController::class, 'show']);
    Route::post('/rto/{lead}/stage', [RtoController::class, 'updateStage']);
    Route::post('/rto/{lead}/insurance-details', [RtoController::class, 'updateInsuranceDetails']);
});
