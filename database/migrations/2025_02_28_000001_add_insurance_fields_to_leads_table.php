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
            // Fields for creating a new lead from Insurance context
            $table->string('insurance_address_proof_path')->nullable();
            $table->string('insurance_address_proof_type')->nullable();

            // Fields for the 'Insurance' stage form
            $table->text('insurance_type_of_insurance')->nullable(); // Can store a comma-separated list
            $table->decimal('insurance_premium_amount', 15, 2)->nullable();
            $table->decimal('insurance_idv_amount', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_address_proof_path',
                'insurance_address_proof_type',
                'insurance_type_of_insurance',
                'insurance_premium_amount',
                'insurance_idv_amount',
            ]);
        });
    }
};
