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
            $table->decimal('payment_amount', 15, 2)->nullable()->after('payment_release_status');
            $table->date('payment_date')->nullable()->after('payment_amount');
            $table->text('payment_remarks')->nullable()->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['payment_amount', 'payment_date', 'payment_remarks']);
        });
    }
};
