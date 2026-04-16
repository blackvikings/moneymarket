<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'insurance_premium_amount')) {
                $table->decimal('insurance_premium_amount', 15, 2)->nullable()->after('insurance_amount');
            }

            if (!Schema::hasColumn('leads', 'insurance_idv_amount')) {
                $table->decimal('insurance_idv_amount', 15, 2)->nullable()->after('insurance_premium_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['insurance_premium_amount', 'insurance_idv_amount']);
        });
    }
};
