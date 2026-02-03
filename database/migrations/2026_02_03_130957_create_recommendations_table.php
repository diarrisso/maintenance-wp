<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_report_id')->constrained()->onDelete('cascade');
            $table->string('type'); // security, plugin, theme, performance, other
            $table->string('priority'); // critical, high, medium, low
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('action')->nullable(); // replace, remove, update, configure
            $table->string('current_item')->nullable(); // e.g. "Admin Menü Manager"
            $table->string('suggested_item')->nullable(); // e.g. "Admin Menu Editor"
            $table->boolean('approved_by_client')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
