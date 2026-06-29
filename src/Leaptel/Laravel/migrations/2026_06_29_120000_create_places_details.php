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
        Schema::create('places_details', function (Blueprint $table) {
            $table->char('location_id')->primary();
            $table->char('formattedAddress');
            $table->char('address1');
            $table->char('address2');
            $table->char('locality');
            $table->char('latitude');
            $table->char('longitude');
            $table->char('csaId');
            $table->char('serviceType');
            $table->char('serviceStatus');
            $table->char('techType');
            $table->char('area_description');
            $table->json('raw')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places_details');
    }
};
