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
            // RTO Stage
            $table->string('rto_stage', 50)->nullable()->default('New'); // New, Checking, Loan Return, Vehicle Detail, Insurance, RTO Done

            // RTO-specific lead creation fields
            $table->string('rto_address_proof_path')->nullable();

            // Checking Stage Checklist
            $table->boolean('rto_hsrp_status')->default(false);
            $table->boolean('rto_hypothecation_status')->default(false);
            $table->foreignId('rto_hypothecation_bank_id')->nullable()->constrained('banks');
            $table->boolean('rto_insurance_status')->default(false);
            $table->boolean('rto_fitness_status')->default(false);
            $table->boolean('rto_tax_status')->default(false);
            $table->boolean('rto_puc_status')->default(false);
            $table->boolean('rto_echallan_status')->default(false);
            $table->boolean('rto_ncr_status')->default(false);
            $table->boolean('rto_challan_status')->default(false);
            $table->boolean('rto_permit_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['rto_hypothecation_bank_id']);
            $table->dropColumn([
                'rto_stage',
                'rto_address_proof_path',
                'rto_hsrp_status',
                'rto_hypothecation_status',
                'rto_hypothecation_bank_id',
                'rto_insurance_status',
                'rto_fitness_status',
                'rto_tax_status',
                'rto_puc_status',
                'rto_echallan_status',
                'rto_ncr_status',
                'rto_challan_status',
                'rto_permit_status',
            ]);
        });
    }
};
