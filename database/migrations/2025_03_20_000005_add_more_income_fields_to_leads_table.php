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
            $table->decimal('last_month_salary', 15, 2)->nullable()->after('approx_income');
            $table->integer('itr_years')->nullable()->after('itr_amount')->comment('1, 2, or 3 years');
            $table->boolean('has_itr')->default(false)->after('income_proof_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['last_month_salary', 'itr_years', 'has_itr']);
        });
    }
};
