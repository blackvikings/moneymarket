<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->text('remark')->nullable();
            $table->foreignId('created_by')->constrained('users'); // User who changed the status
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_status_logs');
    }
};
