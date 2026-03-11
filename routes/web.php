<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\RtoAgentController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\VehicleManufactureController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\RtoController;
use App\Http\Controllers\InsuranceLeadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReferralLogController;
use App\Http\Controllers\LeadSourcePersonController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\ChangePasswordController;

Route::get('/', function () {
    return redirect()->route('employee.login');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('admin.login.view');
    Route::post('login', [AdminLoginController::class, 'login'])->name('admin.login');
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

// Employee Authentication Routes
Route::prefix('employee')->group(function () {
    Route::get('login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
    Route::post('login', [EmployeeLoginController::class, 'login']);
    Route::post('logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');
});

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes...
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Shared Routes (Accessible by both, but logic inside controllers might differ)
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Logout Route (Global fallback if needed, though specific ones are preferred)
Route::post('logout', function () {
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        return app(AdminLoginController::class)->logout(request());
    }
    return app(EmployeeLoginController::class)->logout(request());
})->name('logout');


// Admin Routes
Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/{employee}/update-status', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::get('get-branches/{bank}', [EmployeeController::class, 'getBranches'])->name('get-branches');
    Route::resource('employees', EmployeeController::class);

    Route::get('banks/export', [BankController::class, 'export'])->name('banks.export');
    Route::post('banks/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle-status');
    Route::post('banks/store-ajax', [BankController::class, 'storeAjax'])->name('banks.store-ajax');
    Route::resource('banks', BankController::class);

    Route::get('firms/export', [FirmController::class, 'export'])->name('firms.export');
    Route::post('firms/{firm}/toggle-status', [FirmController::class, 'toggleStatus'])->name('firms.toggle-status');
    Route::resource('firms', FirmController::class);

    Route::get('rto-agents/export', [RtoAgentController::class, 'export'])->name('rto-agents.export');
    Route::post('rto-agents/{rtoAgent}/toggle-status', [RtoAgentController::class, 'toggleStatus'])->name('rto-agents.toggle-status');
    Route::resource('rto-agents', RtoAgentController::class);

    Route::get('insurances/export', [InsuranceController::class, 'export'])->name('insurances.export');
    Route::post('insurances/{insurance}/toggle-status', [InsuranceController::class, 'toggleStatus'])->name('insurances.toggle-status');
    Route::resource('insurances', InsuranceController::class);

    Route::get('vehicle-manufactures/export', [VehicleManufactureController::class, 'export'])->name('vehicle-manufactures.export');
    Route::post('vehicle-manufactures/{vehicleManufacture}/toggle-status', [VehicleManufactureController::class, 'toggleStatus'])->name('vehicle-manufactures.toggle-status');
    Route::get('vehicle-manufactures/{vehicleManufacture}/variants', [VehicleManufactureController::class, 'getVariants'])->name('vehicle-manufactures.variants');
    Route::resource('vehicle-manufactures', VehicleManufactureController::class);

    // Admin can also manage leads, CRM, RTO
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::post('leads/import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('leads/download-sample', [LeadController::class, 'downloadSample'])->name('leads.download-sample');

    // Bulk Assign Routes - Changed URL to avoid conflict
    Route::get('bulk-assign-leads', function() { return redirect()->route('admin.leads.index'); });
    Route::post('bulk-assign-leads', [LeadController::class, 'bulkAssign'])->name('leads.bulk-assign');

    Route::post('leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('leads/{lead}/assign-bank', [LeadController::class, 'assignBank'])->name('leads.assign-bank');
    Route::resource('leads', LeadController::class);

    Route::get('crm/export', [CrmController::class, 'export'])->name('crm.export');
    Route::get('crm', [CrmController::class, 'index'])->name('crm.index');
    Route::get('crm/{lead}', [CrmController::class, 'show'])->name('crm.show');
    Route::post('crm/{lead}/update-stage', [CrmController::class, 'updateStage'])->name('crm.update-stage');
    Route::post('crm/{lead}/store-disbursal', [CrmController::class, 'storeDisbursal'])->name('crm.store-disbursal');

    Route::get('rto/export', [RtoController::class, 'export'])->name('rto.export');
    Route::get('rto', [RtoController::class, 'index'])->name('rto.index');
    Route::get('rto/create', [RtoController::class, 'create'])->name('rto.create');
    Route::post('rto', [RtoController::class, 'store'])->name('rto.store');
    Route::get('rto/create-insurance-lead', [RtoController::class, 'createInsuranceLead'])->name('rto.create-insurance-lead');
    Route::post('rto/store-insurance-lead', [RtoController::class, 'storeInsuranceLead'])->name('rto.store-insurance-lead');
    Route::get('rto/{lead}', [RtoController::class, 'show'])->name('rto.show');
    Route::post('rto/{lead}/update-stage', [RtoController::class, 'updateStage'])->name('rto.update-stage');
    Route::post('rto/{lead}/update-checklist', [RtoController::class, 'updateChecklist'])->name('rto.update-checklist');
    Route::post('rto/{lead}/update-loan-return', [RtoController::class, 'updateLoanReturn'])->name('rto.update-loan-return');
    Route::post('rto/{lead}/update-vehicle-detail', [RtoController::class, 'updateVehicleDetail'])->name('rto.update-vehicle-detail');
    Route::post('rto/{lead}/update-rto-done', [RtoController::class, 'updateRtoDone'])->name('rto.update-rto-done');
    Route::post('rto/{lead}/reject-lead', [RtoController::class, 'rejectLead'])->name('rto.reject-lead');

    // Insurance Lead Module
    Route::get('insurance-leads/export', [InsuranceLeadController::class, 'export'])->name('insurance-leads.export');
    Route::get('insurance-leads', [InsuranceLeadController::class, 'index'])->name('insurance-leads.index');
    Route::get('insurance-leads/create', [InsuranceLeadController::class, 'create'])->name('insurance-leads.create');
    Route::post('insurance-leads', [InsuranceLeadController::class, 'store'])->name('insurance-leads.store');
    Route::get('insurance-leads/{lead}', [InsuranceLeadController::class, 'show'])->name('insurance-leads.show');
    Route::post('insurance-leads/{lead}/update-details', [InsuranceLeadController::class, 'updateDetails'])->name('insurance-leads.update-details');
    Route::delete('insurance-leads/document/{document}', [InsuranceLeadController::class, 'deleteDocument'])->name('insurance-leads.delete-document');

    // Referral Log Module
    Route::get('referral-logs/export', [ReferralLogController::class, 'export'])->name('referral-logs.export');
    Route::get('referral-logs', [ReferralLogController::class, 'index'])->name('referral-logs.index');
    Route::patch('referral-logs/{referral}/mark-as-paid', [ReferralLogController::class, 'markAsPaid'])->name('referral-logs.markAsPaid');
});

// Employee Routes
Route::group(['middleware' => ['auth'], 'prefix' => 'employee', 'as' => 'employee.'], function () {
    // Change Password Routes
    Route::get('change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('change-password', [ChangePasswordController::class, 'changePassword'])->name('update-password');

    // Debug Permissions Route
    Route::get('debug-permissions', function() {
        $user = auth()->user();
        return response()->json([
            'user' => $user->name,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    });

    // Log Viewer Route (Restricted to Admin)


    // Employees typically manage leads
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::post('leads/import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('leads/download-sample', [LeadController::class, 'downloadSample'])->name('leads.download-sample');
    Route::post('leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('leads/{lead}/assign-bank', [LeadController::class, 'assignBank'])->name('leads.assign-bank');
    Route::resource('leads', LeadController::class);

    // Depending on permissions, they might access CRM/RTO too, or just parts of it.
    // For now, I'll expose the same modules but under the 'employee' prefix.
    // Access control inside controllers (via permissions) will still apply.

    Route::get('crm/export', [CrmController::class, 'export'])->name('crm.export');
    Route::get('crm', [CrmController::class, 'index'])->name('crm.index');
    Route::get('crm/{lead}', [CrmController::class, 'show'])->name('crm.show');
    Route::post('crm/{lead}/update-stage', [CrmController::class, 'updateStage'])->name('crm.update-stage');
    Route::post('crm/{lead}/store-disbursal', [CrmController::class, 'storeDisbursal'])->name('crm.store-disbursal');

    Route::get('rto/export', [RtoController::class, 'export'])->name('rto.export');
    Route::get('rto', [RtoController::class, 'index'])->name('rto.index');
    Route::get('rto/create', [RtoController::class, 'create'])->name('rto.create');
    Route::post('rto', [RtoController::class, 'store'])->name('rto.store');
    Route::get('rto/create-insurance-lead', [RtoController::class, 'createInsuranceLead'])->name('rto.create-insurance-lead');
    Route::post('rto/store-insurance-lead', [RtoController::class, 'storeInsuranceLead'])->name('rto.store-insurance-lead');
    Route::get('rto/{lead}', [RtoController::class, 'show'])->name('rto.show');
    Route::post('rto/{lead}/update-stage', [RtoController::class, 'updateStage'])->name('rto.update-stage');
    Route::post('rto/{lead}/update-checklist', [RtoController::class, 'updateChecklist'])->name('rto.update-checklist');
    Route::post('rto/{lead}/update-loan-return', [RtoController::class, 'updateLoanReturn'])->name('rto.update-loan-return');
    Route::post('rto/{lead}/update-vehicle-detail', [RtoController::class, 'updateVehicleDetail'])->name('rto.update-vehicle-detail');
    Route::post('rto/{lead}/update-rto-done', [RtoController::class, 'updateRtoDone'])->name('rto.update-rto-done');
    Route::post('rto/{lead}/reject-lead', [RtoController::class, 'rejectLead'])->name('rto.reject-lead');

    // Insurance Lead Module
    Route::get('insurance-leads/export', [InsuranceLeadController::class, 'export'])->name('insurance-leads.export');
    Route::get('insurance-leads', [InsuranceLeadController::class, 'index'])->name('insurance-leads.index');
    Route::get('insurance-leads/create', [InsuranceLeadController::class, 'create'])->name('insurance-leads.create');
    Route::post('insurance-leads', [InsuranceLeadController::class, 'store'])->name('insurance-leads.store');
    Route::get('insurance-leads/{lead}', [InsuranceLeadController::class, 'show'])->name('insurance-leads.show');
    Route::post('insurance-leads/{lead}/update-details', [InsuranceLeadController::class, 'updateDetails'])->name('insurance-leads.update-details');
    Route::delete('insurance-leads/document/{document}', [InsuranceLeadController::class, 'deleteDocument'])->name('insurance-leads.delete-document');

    // Employees need access to get branches for dropdowns
    Route::get('get-branches/{bank}', [EmployeeController::class, 'getBranches'])->name('get-branches');

    // Masters Routes for Employees (if they have permission)
    Route::get('banks/export', [BankController::class, 'export'])->name('banks.export');
    Route::post('banks/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle-status');
    Route::post('banks/store-ajax', [BankController::class, 'storeAjax'])->name('banks.store-ajax');
    Route::resource('banks', BankController::class);

    // Masters Routes for Employees (if they have permission)
    Route::get('firms/export', [FirmController::class, 'export'])->name('firms.export');
    Route::post('firms/{firm}/toggle-status', [FirmController::class, 'toggleStatus'])->name('firms.toggle-status');
    Route::resource('firms', FirmController::class);

    Route::get('rto-agents/export', [RtoAgentController::class, 'export'])->name('rto-agents.export');
    Route::post('rto-agents/{rtoAgent}/toggle-status', [RtoAgentController::class, 'toggleStatus'])->name('rto-agents.toggle-status');
    Route::resource('rto-agents', RtoAgentController::class);

    Route::get('insurances/export', [InsuranceController::class, 'export'])->name('insurances.export');
    Route::post('insurances/{insurance}/toggle-status', [InsuranceController::class, 'toggleStatus'])->name('insurances.toggle-status');
    Route::resource('insurances', InsuranceController::class);

    Route::get('vehicle-manufactures/export', [VehicleManufactureController::class, 'export'])->name('vehicle-manufactures.export');
    Route::post('vehicle-manufactures/{vehicleManufacture}/toggle-status', [VehicleManufactureController::class, 'toggleStatus'])->name('vehicle-manufactures.toggle-status');
    Route::get('vehicle-manufactures/{vehicleManufacture}/variants', [VehicleManufactureController::class, 'getVariants'])->name('vehicle-manufactures.variants');
    Route::resource('vehicle-manufactures', VehicleManufactureController::class);

    // Employee Management Routes for Employees (if they have permission)
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/{employee}/update-status', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::resource('employees', EmployeeController::class);

    // Roles and Permissions for Employees (if they have permission)
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Referral Log Module
    Route::get('referral-logs/export', [ReferralLogController::class, 'export'])->name('referral-logs.export');
    Route::get('referral-logs', [ReferralLogController::class, 'index'])->name('referral-logs.index');
    Route::patch('referral-logs/{referral}/mark-as-paid', [ReferralLogController::class, 'markAsPaid'])->name('referral-logs.markAsPaid');
});

// Location Routes (Shared)
Route::get('/get-states', [LocationController::class, 'getStates'])->name('get-states');
Route::get('/get-cities/{state}', [LocationController::class, 'getCities'])->name('get-cities');
Route::get('/get-location-by-pincode/{pincode}', [LocationController::class, 'getLocationByPincode'])->name('get-location-by-pincode');
Route::get('/get-vehicle-names/{vehicleManufacture}', [VehicleManufactureController::class, 'getVehicleNames'])->name('get-vehicle-names');
Route::get('/get-variants', [VehicleManufactureController::class, 'getVariantsByName'])->name('get-variants');
Route::get('/get-employees-by-firm/{firm}', [EmployeeController::class, 'getEmployeesByFirm'])->name('get-employees-by-firm');
Route::post('/vehicle-manufactures/store-ajax', [VehicleManufactureController::class, 'storeAjax'])->name('vehicle-manufactures.store-ajax');

// Lead Source Person Routes
Route::get('/lead-source-persons/search', [LeadSourcePersonController::class, 'search'])->name('lead-source-persons.search');
Route::post('/lead-source-persons/store', [LeadSourcePersonController::class, 'store'])->name('lead-source-persons.store');

Route::get('logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return 'No log file found.';
    }
    return '<pre>' . file_get_contents($logFile) . '</pre>';
})->name('logs');
