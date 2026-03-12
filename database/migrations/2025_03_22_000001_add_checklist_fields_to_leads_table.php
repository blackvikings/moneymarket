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
            // Options for each checklist item: Pending, Previously Done, Done
            $table->string('rto_hsrp_status')->nullable()->change();
            $table->string('rto_insurance_status')->nullable()->change();
            $table->string('rto_fitness_status')->nullable()->change();
            $table->string('rto_tax_status')->nullable()->change();
            $table->string('rto_puc_status')->nullable()->change();
            $table->string('rto_echallan_status')->nullable()->change();
            $table->string('rto_ncr_status')->nullable()->change();
            $table->string('rto_challan_status')->nullable()->change();
            $table->string('rto_permit_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Reverting back to boolean is complex if data exists, but for migration purposes:
            // $table->boolean('rto_hsrp_status')->default(false)->change();
            // ...
        });
    }
};
