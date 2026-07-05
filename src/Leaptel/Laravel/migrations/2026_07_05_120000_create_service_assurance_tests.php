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
        Schema::create('svc_assurance_tests', function (Blueprint $table) {
            // Note this should be a uuid7, so the database doesn't get fragmented
            $table->char('uuid', 40)->primary();
            $table->bigInteger('service_id');
            $table->bigInteger('test_id');
            $table->char('request_status');
            // This is the serialized ServiceAssuranceResult object
            $table->longText('object')->charset('binary')->nullable();
            // For alert tracking or anything else needed
            $table->json("details")->nullable();
            $table->timestamps();
            // Probably don't NEED to index test_id, but let's see how it goes.
            $table->index(['service_id', 'test_id']);
            // This is to update any that aren't complete
            $table->index(['request_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('svc_assurance_tests');
    }
};
