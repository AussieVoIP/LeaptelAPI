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
        Schema::create('requestlogs', function (Blueprint $table) {
            $table->id();
            $table->char('type');
            $table->char('custid');
            $table->char('state')->default('unsent');
            $table->char('desturl');
            $table->json('details')->nullable();
            $table->json('response')->nullable();
            $table->char('serviceid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestlogs');
    }
};
