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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Basic Details
            $table->string('customer_name');
            $table->string('father_name')->nullable();
            $table->foreignId('firm_id')->nullable()->constrained('firms')->onDelete('set null');
            $table->string('residence_type', 50)->nullable(); // Rented/Own

            // Current Address
            $table->text('current_address_line')->nullable();
            $table->string('current_address_pincode', 20)->nullable();
            $table->string('current_address_city', 100)->nullable();
            $table->string('current_address_state', 100)->nullable();

            // Permanent Address
            $table->text('permanent_address_line')->nullable();
            $table->string('permanent_address_pincode', 20)->nullable();
            $table->string('permanent_address_city', 100)->nullable();
            $table->string('permanent_address_state', 100)->nullable();

            $table->string('contact_number', 50);
            $table->string('email')->nullable();

            // Job Profile (Main Applicant)
            $table->string('job_profile_type', 100)->nullable(); // Self Employed, Farmer, Salaried
            $table->text('nature_of_work')->nullable(); // If Self Employed
            $table->string('salaried_type', 100)->nullable(); // Government, Corporate, Other
            $table->text('other_company_name')->nullable(); // If Salaried Other
            $table->string('designation')->nullable(); // If Salaried

            $table->boolean('income_proof_available')->default(false);
            $table->decimal('itr_amount', 15, 2)->nullable();
            $table->text('itr_document_path')->nullable();
            $table->decimal('approx_income', 15, 2)->nullable();

            $table->boolean('business_proof_available')->default(false);
            $table->text('business_proof_document_path')->nullable();

            // Loan Details
            $table->string('loan_type', 100)->nullable(); // Vehicle, Housing, LAP, Business, Personal, Other
            $table->text('other_loan_name')->nullable();

            // Vehicle Loan Specifics
            $table->string('vehicle_loan_type', 100)->nullable(); // Car, Commercial, Construction, Tractor, Other
            $table->text('other_vehicle_loan_name')->nullable();
            $table->string('vehicle_purchase_type', 100)->nullable(); // New, Repurchase, Refinance, Balance Transfer

            // New Purchase Details
            $table->foreignId('manufacture_id')->nullable()->constrained('vehicle_manufactures')->onDelete('set null');
            $table->foreignId('vehicle_name_id')->nullable()->constrained('vehicle_names')->onDelete('set null');
            $table->text('vehicle_variant')->nullable();
            $table->decimal('showroom_price', 15, 2)->nullable();
            $table->decimal('on_road_price', 15, 2)->nullable();

            // Repurchase/Refinance/BT Details
            $table->string('rc_number', 100)->nullable();
            $table->string('engine_number', 100)->nullable();
            $table->string('chassis_number', 100)->nullable();
            $table->string('mfg_year', 20)->nullable();
            $table->string('mfg_name', 100)->nullable();
            $table->string('model_name', 100)->nullable();
            $table->string('variant', 100)->nullable();
            $table->string('owner_serial_number', 100)->nullable();

            // Insurance
            $table->boolean('insurance_available')->default(false);
            $table->foreignId('insurance_id')->nullable()->constrained('insurances')->onDelete('set null');
            $table->date('insurance_start_date')->nullable();
            $table->date('insurance_end_date')->nullable();
            $table->decimal('insurance_amount', 15, 2)->nullable();

            // Hypothecation
            $table->boolean('hypothecation_status')->default(false);
            $table->string('old_financer')->nullable();
            $table->decimal('close_amount', 15, 2)->nullable();
            $table->integer('emi_paid_count')->nullable();

            // References
            $table->string('ref1_name')->nullable();
            $table->string('ref1_mobile', 50)->nullable();
            $table->string('ref1_email')->nullable();
            $table->text('ref1_address')->nullable();

            $table->string('ref2_name')->nullable();
            $table->string('ref2_mobile', 50)->nullable();
            $table->string('ref2_email')->nullable();
            $table->text('ref2_address')->nullable();

            // Loan Amount Breakdown
            $table->decimal('old_loan_close_amount', 15, 2)->nullable();
            $table->decimal('required_in_hand_amount', 15, 2)->nullable();
            $table->decimal('total_loan_amount', 15, 2)->nullable();

            // Co-applicant
            $table->boolean('has_co_applicant')->default(false);
            $table->string('co_name')->nullable();
            $table->string('co_relation', 100)->nullable();
            $table->string('co_residence_type', 50)->nullable();
            $table->text('co_current_address_line')->nullable();
            $table->string('co_current_address_pincode', 20)->nullable();
            $table->string('co_current_address_city', 100)->nullable();
            $table->string('co_current_address_state', 100)->nullable();
            $table->text('co_permanent_address_line')->nullable();
            $table->string('co_permanent_address_pincode', 20)->nullable();
            $table->string('co_permanent_address_city', 100)->nullable();
            $table->string('co_permanent_address_state', 100)->nullable();
            $table->string('co_job_profile_type', 100)->nullable();
            $table->text('co_nature_of_work')->nullable();
            $table->string('co_salaried_type', 100)->nullable();
            $table->text('co_other_company_name')->nullable();
            $table->boolean('co_income_proof_available')->default(false);
            $table->decimal('co_itr_amount', 15, 2)->nullable();
            $table->text('co_itr_document_path')->nullable();
            $table->decimal('co_approx_income', 15, 2)->nullable();
            $table->boolean('co_business_proof_available')->default(false);
            $table->text('co_business_proof_document_path')->nullable();

            // Guarantor
            $table->boolean('has_guarantor')->default(false);
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_relation', 100)->nullable();
            $table->string('guarantor_residence_type', 50)->nullable();
            $table->text('guarantor_current_address_line')->nullable();
            $table->string('guarantor_current_address_pincode', 20)->nullable();
            $table->string('guarantor_current_address_city', 100)->nullable();
            $table->string('guarantor_current_address_state', 100)->nullable();
            $table->text('guarantor_permanent_address_line')->nullable();
            $table->string('guarantor_permanent_address_pincode', 20)->nullable();
            $table->string('guarantor_permanent_address_city', 100)->nullable();
            $table->string('guarantor_permanent_address_state', 100)->nullable();
            $table->string('guarantor_job_profile_type', 100)->nullable();
            $table->text('guarantor_nature_of_work')->nullable();
            $table->string('guarantor_salaried_type', 100)->nullable();
            $table->text('guarantor_other_company_name')->nullable();
            $table->boolean('guarantor_income_proof_available')->default(false);
            $table->decimal('guarantor_itr_amount', 15, 2)->nullable();
            $table->text('guarantor_itr_document_path')->nullable();
            $table->decimal('guarantor_approx_income', 15, 2)->nullable();
            $table->boolean('guarantor_business_proof_available')->default(false);
            $table->text('guarantor_business_proof_document_path')->nullable();

            // System Fields
            $table->string('status', 50)->default('New');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Bank Assignment Details
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->foreignId('bank_branch_id')->nullable()->constrained('bank_branches')->onDelete('set null');
            // Note: 'firm_id' is already present in Basic Details section above

            $table->date('follow_up_date')->nullable();
            $table->time('follow_up_time')->nullable();
            $table->text('remark')->nullable();

            $table->timestamp('converted_at')->nullable(); // For Aging

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
