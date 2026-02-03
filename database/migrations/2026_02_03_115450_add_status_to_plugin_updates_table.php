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
        Schema::table('plugin_updates', function (Blueprint $table) {
            $table->enum('status', ['updated', 'skipped', 'no_access'])->default('updated')->after('version_after');
            $table->string('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_updates', function (Blueprint $table) {
            $table->dropColumn(['status', 'notes']);
        });
    }
};
