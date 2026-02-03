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
        Schema::create('plugin_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_report_id')->constrained()->onDelete('cascade');
            $table->string('plugin_name');
            $table->string('version_before')->nullable();
            $table->string('version_after')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_updates');
    }
};
