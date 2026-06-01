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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('company_name')->nullable();
            $table->char('first_name');
            $table->char('last_name')->nullable();
            $table->char('birthdate')->nullable();
            $table->char('email');
            $table->char('mobile');
            $table->char('phone')->nullable();
            $table->char('fax')->nullable();
            $table->char('address1')->default("");
            $table->char('address2')->default("");
            $table->char('city');
            $table->char('state');
            $table->char('postcode');
            $table->char('active');
            $table->integer("default_site_contact")->nullable();
            $table->json('details')->default('[]');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
