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
//        if (!Schema::hasColumn('leads', 'bank_branch_id')) {
//            Schema::table('leads', function (Blueprint $table) {
//                $table->bigInteger('bank_branch_id')->nullable()->constrained('bank_branches')->onDelete('set null');
//            });
//        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
//            $table->dropColumn('bank_branch_id');
        });
    }
};
