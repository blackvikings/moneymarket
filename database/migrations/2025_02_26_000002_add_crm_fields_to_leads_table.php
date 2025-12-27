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
            // CRM Stages
            $table->string('crm_stage', 50)->nullable()->default('New'); // New, Login, FI Document, Vehicle Evaluation, Credit, Approved, Agreement, Disbursal, Closed

            // Disbursal Details
            $table->date('disbursal_date')->nullable();
            $table->decimal('finance_amount', 15, 2)->nullable();
            $table->decimal('disbursal_amount', 15, 2)->nullable();

            // Amount Received Breakdown
            $table->decimal('amt_rec_firm', 15, 2)->nullable();
            $table->decimal('amt_rec_lead', 15, 2)->nullable();
            $table->decimal('amt_rec_old_financer', 15, 2)->nullable();

            // Timestamps for stages (optional but good for tracking/aging)
            $table->timestamp('crm_new_at')->nullable();
            $table->timestamp('crm_login_at')->nullable();
            $table->timestamp('crm_fi_document_at')->nullable();
            $table->timestamp('crm_vehicle_evaluation_at')->nullable();
            $table->timestamp('crm_credit_at')->nullable();
            $table->timestamp('crm_approved_at')->nullable();
            $table->timestamp('crm_agreement_at')->nullable();
            $table->timestamp('crm_disbursal_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'crm_stage',
                'disbursal_date',
                'finance_amount',
                'disbursal_amount',
                'amt_rec_firm',
                'amt_rec_lead',
                'amt_rec_old_financer',
                'crm_new_at',
                'crm_login_at',
                'crm_fi_document_at',
                'crm_vehicle_evaluation_at',
                'crm_credit_at',
                'crm_approved_at',
                'crm_agreement_at',
                'crm_disbursal_at',
            ]);
        });
    }
};
