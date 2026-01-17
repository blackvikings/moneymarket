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
            $table->text('rto_hsrp_remark')->nullable();
            $table->text('rto_hypothecation_remark')->nullable();
            $table->text('rto_insurance_remark')->nullable();
            $table->text('rto_fitness_remark')->nullable();
            $table->text('rto_tax_remark')->nullable();
            $table->text('rto_puc_remark')->nullable();
            $table->text('rto_echallan_remark')->nullable();
            $table->text('rto_ncr_remark')->nullable();
            $table->text('rto_challan_remark')->nullable();
            $table->text('rto_permit_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'rto_hsrp_remark',
                'rto_hypothecation_remark',
                'rto_insurance_remark',
                'rto_fitness_remark',
                'rto_tax_remark',
                'rto_puc_remark',
                'rto_echallan_remark',
                'rto_ncr_remark',
                'rto_challan_remark',
                'rto_permit_remark',
            ]);
        });
    }
};
