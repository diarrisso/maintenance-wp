<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->foreignId('pm_user_id')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('maintenance_frequency')->default(1)->after('maintenance_package');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropForeign(['pm_user_id']);
            $table->dropColumn(['pm_user_id', 'maintenance_frequency']);
        });
    }
};
