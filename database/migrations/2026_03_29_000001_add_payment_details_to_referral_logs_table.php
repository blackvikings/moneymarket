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
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->string('payment_mode')->nullable()->after('status');
            $table->string('cheque_number')->nullable()->after('payment_mode');
            $table->string('cheque_image_path')->nullable()->after('cheque_number');
            $table->string('upi_id')->nullable()->after('cheque_image_path');
            $table->string('online_payment_image_path')->nullable()->after('upi_id');
            $table->string('account_holder_name')->nullable()->after('online_payment_image_path');
            $table->string('account_number')->nullable()->after('account_holder_name');
            $table->string('bank_name')->nullable()->after('account_number');
            $table->string('ifsc_code')->nullable()->after('bank_name');
            $table->string('bank_transfer_image_path')->nullable()->after('ifsc_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_logs', function (Blueprint $table) {
            $table->dropColumn([
                'payment_mode',
                'cheque_number',
                'cheque_image_path',
                'upi_id',
                'online_payment_image_path',
                'account_holder_name',
                'account_number',
                'bank_name',
                'ifsc_code',
                'bank_transfer_image_path',
            ]);
        });
    }
};
