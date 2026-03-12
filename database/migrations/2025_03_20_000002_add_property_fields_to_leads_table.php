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
            $table->string('property_location')->nullable()->after('sub_loan_type');
            $table->string('property_area')->nullable()->after('property_location');
            $table->decimal('property_approx_value', 15, 2)->nullable()->after('property_area');
            $table->string('property_type')->nullable()->after('property_approx_value');
            $table->string('land_type')->nullable()->after('property_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['property_location', 'property_area', 'property_approx_value', 'property_type', 'land_type']);
        });
    }
};
