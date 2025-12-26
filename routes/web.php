<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\RtoAgentController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\VehicleManufactureController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes...
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes...
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes...
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/{employee}/update-status', [EmployeeController::class, 'updateStatus'])->name('employees.update-status');
    Route::get('get-branches/{bank}', [EmployeeController::class, 'getBranches'])->name('get-branches');
    Route::resource('employees', EmployeeController::class);

    Route::get('banks/export', [BankController::class, 'export'])->name('banks.export');
    Route::post('banks/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle-status');
    Route::resource('banks', BankController::class);
    Route::resource('bank-branches', BankBranchController::class);

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
    Route::resource('vehicle-manufactures', VehicleManufactureController::class);

    // Cities module removed, but keeping route cleanup in mind if needed.
});
