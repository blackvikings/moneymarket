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
            $table->string('pan_card_number')->nullable()->after('aadhar_number');
            $table->string('pan_card_document_path')->nullable()->after('pan_card_number');
            $table->string('rc_document_path')->nullable()->after('rc_number');
        });

        Schema::create('lead_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('document_type')->nullable(); // e.g., 'address_proof'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_documents');
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['pan_card_number', 'pan_card_document_path', 'rc_document_path']);
        });
    }
};
