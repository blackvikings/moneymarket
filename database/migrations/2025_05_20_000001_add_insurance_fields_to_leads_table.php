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
            $table->string('insurance_type', 50)->nullable()->after('insurance_amount'); // Vehicle, Health, Property
            $table->string('insurance_coverage_type', 50)->nullable()->after('insurance_type'); // First, Third, Addon
            $table->text('insurance_addon_description')->nullable()->after('insurance_coverage_type'); // If Addon
            $table->string('health_policy_type', 50)->nullable()->after('insurance_addon_description'); // Individual, Family Floater
            $table->string('insurance_lead_status', 50)->default('In Progress')->after('health_policy_type'); // In Progress, Done
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_type',
                'insurance_coverage_type',
                'insurance_addon_description',
                'health_policy_type',
                'insurance_lead_status'
            ]);
        });
    }
};
