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
            $table->foreignId('rto_bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('rto_noc_status', 50)->nullable(); // Yes/No
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['rto_bank_id']);
            $table->dropColumn(['rto_bank_id', 'rto_noc_status']);
        });
    }
};
