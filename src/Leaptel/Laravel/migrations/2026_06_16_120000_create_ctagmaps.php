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
        Schema::create('ctagmaps', function (Blueprint $table) {
            $table->id();
            $table->char('lvc_id');
            $table->integer('ctag');
            $table->boolean('ipoe')->nullable();
            $table->boolean('pppoe')->nullable();
            $table->char('desc')->nullable();
            $table->bigInteger('service_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->unique(['lvc_id', 'ctag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctagmaps');
    }
};
