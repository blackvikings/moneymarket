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
        Schema::table('leads', function (Blueprint $table) {
            // Loan Return Stage
            $table->date('loan_return_date')->nullable();
            $table->text('loan_return_reason')->nullable();

            // Vehicle Detail Stage
            $table->string('rto_vehicle_type', 50)->nullable(); // New Vehicle, Old Vehicle
            $table->boolean('rto_loan_closed_status')->default(false);
            $table->boolean('rto_hp_termination_status')->default(false);
            $table->foreignId('rto_agent_id')->nullable()->constrained('rto_agents');
            $table->boolean('rto_challan_verified')->default(false);
            $table->boolean('rto_verification_status')->default(false);
            $table->boolean('rto_approval_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['rto_agent_id']);
            $table->dropColumn([
                'loan_return_date',
                'loan_return_reason',
                'rto_vehicle_type',
                'rto_loan_closed_status',
                'rto_hp_termination_status',
                'rto_agent_id',
                'rto_challan_verified',
                'rto_verification_status',
                'rto_approval_status',
            ]);
        });
    }
};
