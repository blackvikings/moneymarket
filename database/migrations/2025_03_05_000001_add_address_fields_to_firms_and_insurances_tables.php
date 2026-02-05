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
        Schema::table('firms', function (Blueprint $table) {
            $table->string('address_line')->nullable()->after('address');
            $table->string('pincode')->nullable()->after('address_line');
            $table->unsignedBigInteger('state_id')->nullable()->after('pincode');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');

            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });

        Schema::table('insurances', function (Blueprint $table) {
            $table->string('address_line')->nullable()->after('address');
            $table->string('pincode')->nullable()->after('address_line');
            $table->unsignedBigInteger('state_id')->nullable()->after('pincode');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');

            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firms', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['address_line', 'pincode', 'state_id', 'city_id']);
        });

        Schema::table('insurances', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['address_line', 'pincode', 'state_id', 'city_id']);
        });
    }
};
