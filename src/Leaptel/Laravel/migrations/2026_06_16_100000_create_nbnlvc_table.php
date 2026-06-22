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
        Schema::create('nbnlvc', function (Blueprint $table) {
            $table->id();
            $table->char('lvc_id');
            $table->char('lvc_name');
            $table->integer('s_tag');
            $table->char('description')->nullable();
            $table->boolean('poi_nsw')->nullable();
            $table->boolean('poi_act')->nullable();
            $table->boolean('poi_vic')->nullable();
            $table->boolean('poi_tas')->nullable();
            $table->boolean('poi_sa')->nullable();
            $table->boolean('poi_nt')->nullable();
            $table->boolean('poi_qld')->nullable();
            $table->boolean('poi_wa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nbnlvc');
    }
};
