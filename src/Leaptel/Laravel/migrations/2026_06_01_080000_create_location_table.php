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
        Schema::create('locations', function (Blueprint $table) {
            $table->char("prikeyhash", 200)->primary();
            $table->char('source');
            $table->char('name');
            $table->integer("street_number")->nullable();
            $table->char('street_name');
            $table->char('suburb');
            $table->char('state');
            $table->char('postcode');
            $table->char('street_line');
            $table->integer("lot_no")->nullable();
            $table->integer("unit")->nullable();
            $table->integer("level")->nullable();
            $table->char('nbnlocid')->nullable();
            $table->json('details')->default('[]');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
