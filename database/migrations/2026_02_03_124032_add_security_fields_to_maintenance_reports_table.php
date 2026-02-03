<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            // Security scan results
            $table->string('security_plugin')->nullable()->after('check_security');
            $table->unsignedInteger('security_issues_count')->nullable()->after('security_plugin');
            $table->text('security_issues_details')->nullable()->after('security_issues_count');

            // Firewall status
            $table->unsignedTinyInteger('firewall_status')->nullable()->after('security_issues_details');
            $table->text('firewall_notes')->nullable()->after('firewall_status');

            // Brute force / attacks
            $table->unsignedInteger('brute_force_attacks_day')->nullable()->after('firewall_notes');
            $table->unsignedInteger('brute_force_attacks_week')->nullable()->after('brute_force_attacks_day');

            // Security actions taken
            $table->text('security_actions_taken')->nullable()->after('brute_force_attacks_week');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn([
                'security_plugin',
                'security_issues_count',
                'security_issues_details',
                'firewall_status',
                'firewall_notes',
                'brute_force_attacks_day',
                'brute_force_attacks_week',
                'security_actions_taken',
            ]);
        });
    }
};
