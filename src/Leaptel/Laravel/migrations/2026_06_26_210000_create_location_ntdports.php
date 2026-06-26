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
        Schema::create('location_ntdports', function (Blueprint $table) {
            $table->id();
            $table->char('location_id');
            $table->char('port_name');
            $table->char('ntdid');
            $table->char('status')->nullable();
            $table->char('port_id')->nullable();
            $table->char('service_provider_id')->nullable();
            $table->char('service_provider_name')->nullable();
            $table->char('resourceRef')->nullable();
            $table->char('avc_id')->nullable();
            $table->json('raw')->default('[]');
            $table->unique(['location_id', 'port_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_ntdports');
    }
};
