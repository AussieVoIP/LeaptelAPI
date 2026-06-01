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
        Schema::create('location_lookup', function (Blueprint $table) {
            $table->id();
            $table->char('source');
            $table->char('state');
            $table->char('lochash', 200);
            $table->timestamps();
            $table->index(["source"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_lookup');
    }
};
