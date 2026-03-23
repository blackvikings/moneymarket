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
            if (!Schema::hasColumn('leads', 'has_guarantor')) {
                $table->boolean('has_guarantor')->default(false)->after('co_relation');
            }
            if (!Schema::hasColumn('leads', 'guarantor_name')) {
                $table->string('guarantor_name')->nullable()->after('has_guarantor');
            }
            if (!Schema::hasColumn('leads', 'guarantor_relation')) {
                $table->string('guarantor_relation')->nullable()->after('guarantor_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'has_guarantor')) {
                $table->dropColumn('has_guarantor');
            }
            if (Schema::hasColumn('leads', 'guarantor_name')) {
                $table->dropColumn('guarantor_name');
            }
            if (Schema::hasColumn('leads', 'guarantor_relation')) {
                $table->dropColumn('guarantor_relation');
            }
        });
    }
};
