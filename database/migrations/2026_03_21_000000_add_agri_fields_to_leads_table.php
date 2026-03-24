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
            $table->string('agri_land_area')->nullable()->after('job_profile_type');
            $table->string('agri_land_document_path')->nullable()->after('agri_land_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('agri_land_area');
            $table->dropColumn('agri_land_document_path');
        });
    }
};
