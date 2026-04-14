<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('leads', 'payment_cheque_number')) {
                $table->text('payment_cheque_number')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('leads', 'payment_cheque_image_path')) {
                $table->text('payment_cheque_image_path')->nullable()->after('payment_cheque_number');
            }
            if (!Schema::hasColumn('leads', 'payment_upi_id')) {
                $table->text('payment_upi_id')->nullable()->after('payment_cheque_image_path');
            }
            if (!Schema::hasColumn('leads', 'payment_online_payment_image_path')) {
                $table->text('payment_online_payment_image_path')->nullable()->after('payment_upi_id');
            }
            if (!Schema::hasColumn('leads', 'payment_account_holder_name')) {
                $table->text('payment_account_holder_name')->nullable()->after('payment_online_payment_image_path');
            }
            if (!Schema::hasColumn('leads', 'payment_account_number')) {
                $table->text('payment_account_number')->nullable()->after('payment_account_holder_name');
            }
            if (!Schema::hasColumn('leads', 'payment_bank_name')) {
                $table->text('payment_bank_name')->nullable()->after('payment_account_number');
            }
            if (!Schema::hasColumn('leads', 'payment_ifsc_code')) {
                $table->text('payment_ifsc_code')->nullable()->after('payment_bank_name');
            }
            if (!Schema::hasColumn('leads', 'payment_bank_transfer_image_path')) {
                $table->text('payment_bank_transfer_image_path')->nullable()->after('payment_ifsc_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $columns = [
                'payment_type',
                'payment_cheque_number',
                'payment_cheque_image_path',
                'payment_upi_id',
                'payment_online_payment_image_path',
                'payment_account_holder_name',
                'payment_account_number',
                'payment_bank_name',
                'payment_ifsc_code',
                'payment_bank_transfer_image_path',
            ];

            $table->dropColumn(array_filter($columns, function ($column) use ($table) {
                return Schema::hasColumn($table->getTable(), $column);
            }));
        });
    }
};

