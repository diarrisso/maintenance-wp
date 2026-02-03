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
        Schema::create('agency_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Masinga Tech');
            $table->string('logo')->nullable();
            $table->string('logo_white')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_zip')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_country')->default('Deutschland');
            $table->string('tax_id')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('primary_color')->default('#2563eb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_settings');
    }
};
