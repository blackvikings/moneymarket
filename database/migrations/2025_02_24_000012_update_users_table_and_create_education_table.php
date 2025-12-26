<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_type')->nullable(); // Team Lead, Tele Caller
            $table->string('contact_number')->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group')->nullable();
            $table->foreignId('firm_id')->nullable()->constrained('firms')->onDelete('set null');

            // Current Address
            $table->string('current_address_line')->nullable();
            $table->string('current_address_pincode')->nullable();
            $table->string('current_address_city')->nullable();
            $table->string('current_address_state')->nullable();

            // Permanent Address
            $table->string('permanent_address_line')->nullable();
            $table->string('permanent_address_pincode')->nullable();
            $table->string('permanent_address_city')->nullable();
            $table->string('permanent_address_state')->nullable();

            // Address Proof
            $table->string('address_proof_path')->nullable();
            $table->string('proof_id_type')->nullable(); // Aadhar, PAN, etc.

            // Reporting Manager
            $table->foreignId('reporting_manager_id')->nullable()->constrained('users')->onDelete('set null');

            // Account Detail
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->foreignId('bank_branch_id')->nullable()->constrained('bank_branches')->onDelete('set null');
            $table->string('account_holder_name')->nullable();
            $table->string('ifsc_code')->nullable();

            // Status
            $table->string('status')->default('Active'); // Active, Blocked, Exited
        });

        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('degree');
            $table->string('institution');
            $table->string('passing_year');
            $table->string('percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropForeign(['reporting_manager_id']);
            $table->dropForeign(['bank_id']);
            $table->dropForeign(['bank_branch_id']);

            $table->dropColumn([
                'employee_type',
                'contact_number',
                'dob',
                'blood_group',
                'firm_id',
                'current_address_line',
                'current_address_pincode',
                'current_address_city',
                'current_address_state',
                'permanent_address_line',
                'permanent_address_pincode',
                'permanent_address_city',
                'permanent_address_state',
                'address_proof_path',
                'proof_id_type',
                'reporting_manager_id',
                'bank_id',
                'bank_branch_id',
                'account_holder_name',
                'ifsc_code',
                'status'
            ]);
        });
    }
};
