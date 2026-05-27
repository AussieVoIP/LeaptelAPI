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
        Schema::create('generic_query_cache', function (Blueprint $table) {
            $table->char('prikeyhash', 200)->primary();
            $table->char('baseurl', 200);
            $table->json('rawparams')->nullable(); // Mainly for debugging
            $table->longText('respbody')->charset('binary')->nullable();
            $table->timestamps();
            $table->index(['baseurl'], 'urlidx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generic_query_cache');
    }
};
