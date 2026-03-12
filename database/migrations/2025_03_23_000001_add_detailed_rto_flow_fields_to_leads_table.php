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
            // Name Transfer fields
            $table->string('rto_name_transfer_status', 50)->nullable(); // Yes/No
            $table->string('rto_seller_name')->nullable();
            $table->string('rto_seller_number')->nullable();
            $table->text('rto_seller_address')->nullable();

            // Hypothecation Add fields
            $table->string('rto_hypothecation_add_status', 50)->nullable(); // Yes/No
            $table->foreignId('rto_hypothecation_add_bank_id')->nullable()->constrained('banks')->onDelete('set null');

            // Aging and Work status
            $table->timestamp('rto_agent_assigned_at')->nullable();
            $table->timestamp('rto_work_done_at')->nullable();

            // Stages tracking (ensure nullable for older records)
            $table->timestamp('rto_challan_done_at')->nullable();
            $table->timestamp('rto_doc_uploaded_at')->nullable();
            $table->timestamp('rto_verification_done_at')->nullable();
            $table->timestamp('rto_approval_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['rto_hypothecation_add_bank_id']);
            $table->dropColumn([
                'rto_name_transfer_status',
                'rto_seller_name',
                'rto_seller_number',
                'rto_seller_address',
                'rto_hypothecation_add_status',
                'rto_hypothecation_add_bank_id',
                'rto_agent_assigned_at',
                'rto_work_done_at',
                'rto_challan_done_at',
                'rto_doc_uploaded_at',
                'rto_verification_done_at',
                'rto_approval_at'
            ]);
        });
    }
};
