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
            if (!Schema::hasColumn('leads', 'premium_amount_collected')) {
                $table->enum('premium_amount_collected', ['Yes', 'No'])->default('No')->after('insurance_end_date');
            }
            if (!Schema::hasColumn('leads', 'premium_payment_type')) {
                $table->string('premium_payment_type')->nullable()->after('premium_amount_collected');
            }
            if (!Schema::hasColumn('leads', 'premium_payment_amount')) {
                $table->decimal('premium_payment_amount', 15, 2)->nullable()->after('premium_payment_type');
            }
            if (!Schema::hasColumn('leads', 'premium_payment_date')) {
                $table->date('premium_payment_date')->nullable()->after('premium_payment_amount');
            }
            if (!Schema::hasColumn('leads', 'premium_cheque_number')) {
                $table->text('premium_cheque_number')->nullable()->after('premium_payment_date');
            }
            if (!Schema::hasColumn('leads', 'premium_cheque_image_path')) {
                $table->text('premium_cheque_image_path')->nullable()->after('premium_cheque_number');
            }
            if (!Schema::hasColumn('leads', 'premium_upi_id')) {
                $table->text('premium_upi_id')->nullable()->after('premium_cheque_image_path');
            }
            if (!Schema::hasColumn('leads', 'premium_online_payment_image_path')) {
                $table->text('premium_online_payment_image_path')->nullable()->after('premium_upi_id');
            }
            if (!Schema::hasColumn('leads', 'premium_account_holder_name')) {
                $table->text('premium_account_holder_name')->nullable()->after('premium_online_payment_image_path');
            }
            if (!Schema::hasColumn('leads', 'premium_account_number')) {
                $table->text('premium_account_number')->nullable()->after('premium_account_holder_name');
            }
            if (!Schema::hasColumn('leads', 'premium_bank_name')) {
                $table->text('premium_bank_name')->nullable()->after('premium_account_number');
            }
            if (!Schema::hasColumn('leads', 'premium_ifsc_code')) {
                $table->text('premium_ifsc_code')->nullable()->after('premium_bank_name');
            }
            if (!Schema::hasColumn('leads', 'premium_bank_transfer_image_path')) {
                $table->text('premium_bank_transfer_image_path')->nullable()->after('premium_ifsc_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $columns = [
                'premium_amount_collected',
                'premium_payment_type',
                'premium_payment_amount',
                'premium_payment_date',
                'premium_cheque_number',
                'premium_cheque_image_path',
                'premium_upi_id',
                'premium_online_payment_image_path',
                'premium_account_holder_name',
                'premium_account_number',
                'premium_bank_name',
                'premium_ifsc_code',
                'premium_bank_transfer_image_path',
            ];
            $table->dropColumn(array_filter($columns, function($column) use ($table) {
                return Schema::hasColumn($table->getTable(), $column);
            }));
        });
    }
};
