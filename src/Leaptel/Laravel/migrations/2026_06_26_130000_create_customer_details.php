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
        Schema::create('customer_details', function (Blueprint $table) {
            $table->bigInteger('customer_id')->primary();
            $table->char('display_name');
            $table->char('email_contact');
            $table->char('phone_contact');
            $table->char('url_contact')->default('https://example.com');
            $table->char('sms_from')->default('NBN.Info');
            $table->char('email_from')->default('noreply@nbn.info');
            $table->boolean("send_sms_to_client")->default(0);
            $table->boolean("send_email_to_client")->default(0);
            $table->char('smsdest_override')->nullable();
            $table->char('emaildest_override')->nullable();
            $table->char('sms_header')->nullable();
            $table->char('sms_footer')->nullable();
            $table->char('email_view')->nullable();
            $table->json('details')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_details');
    }
};
