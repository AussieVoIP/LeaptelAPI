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
        Schema::create('service_order_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->char('orderhash', 200);
            $table->bigInteger('service_id')->nullable();
            $table->char('description', 64);
            $table->bigInteger('customer_id');
            $table->char('action', 32)->nullable();
            $table->char('status', 32)->nullable();
            // This is the serialized object
            $table->longText('object')->charset('binary')->nullable();
            // Add the request in here, I guess
            $table->json("details")->nullable();
            $table->timestamps();
            $table->index(['order_id', 'orderhash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_history');
    }
};
