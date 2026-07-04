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
        Schema::create('webhooks', function (Blueprint $table) {
            // Note this should be a uuid7, so the database doesn't get fragmented
            $table->char('uuid', 40)->primary();
            $table->char('type');
            $table->char('ntype');
            $table->char('service_id')->nullable();
            $table->char('order_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->json('payload');
            $table->json('headers');
            $table->json('server');
            $table->char('path');
            $table->timestamps();
            $table->index(['customer_id', 'service_id']);
            $table->index(['service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
