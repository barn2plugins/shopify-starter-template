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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('plan')->nullable();
            $table->string('plan_display_name')->nullable();
            $table->boolean('is_partner_development')->default(false);
            $table->string('country_code')->nullable();
            $table->string('currency')->nullable();
            $table->string('timezone')->nullable();
            $table->string('iana_timezone')->nullable();
            $table->string('money_format')->nullable();
            $table->string('money_with_currency_format')->nullable();
            $table->string('money_in_emails_format')->nullable();
            $table->string('money_with_currency_in_emails_format')->nullable();
            $table->boolean('checkout_api_supported')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
