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
        Schema::create('service_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_id')->unique();
            $table->bigInteger('plan_id');
            $table->char('product_id');
            $table->bigInteger('customer_id');
            $table->dateTime('start_date');
            $table->dateTime('finish_date')->nullable();
            $table->char('location_id');
            $table->integer('portnum')->nullable();
            $table->char('avc_id')->nullable();
            $table->char('lvc_id')->nullable();
            $table->char('lvc_name')->nullable();
            $table->char('lvc_c_tag')->nullable();
            $table->json('raw')->nullable();
            $table->json('alldetails')->nullable();
            $table->timestamps();
            $table->index('customer_id');
            $table->index(['location_id', 'portnum']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_details');
    }
};
