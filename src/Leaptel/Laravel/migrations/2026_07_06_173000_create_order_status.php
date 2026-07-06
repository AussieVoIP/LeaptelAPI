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
        Schema::create('service_order_status', function (Blueprint $table) {
            $table->bigInteger('order_id')->primary();
            $table->dateTime('event_time');
            $table->char('action', 32)->nullable();
            $table->char('status', 32)->nullable();
            $table->char('description', 64);
            $table->json("details")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_status');
    }
};
