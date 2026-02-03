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
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'completed', 'sent'])->default('draft');
            $table->date('maintenance_date');

            // Phase 1 - Vorbereitung
            $table->boolean('backup_completed')->default(false);
            $table->dateTime('backup_datetime')->nullable();
            $table->boolean('php_compatible')->default(false);

            // Phase 2 - Aktualisierungen
            $table->string('wp_version_before')->nullable();
            $table->string('wp_version_after')->nullable();
            $table->string('theme_name')->nullable();
            $table->string('theme_version_before')->nullable();
            $table->string('theme_version_after')->nullable();

            // Phase 3 - Prüfungen
            $table->boolean('check_frontend')->default(false);
            $table->boolean('check_navigation')->default(false);
            $table->boolean('check_forms')->default(false);
            $table->boolean('check_responsive')->default(false);
            $table->boolean('check_admin_login')->default(false);
            $table->boolean('check_media_upload')->default(false);
            $table->boolean('check_no_errors')->default(false);
            $table->boolean('check_woocommerce')->nullable();
            $table->boolean('check_ssl')->default(false);
            $table->boolean('check_security')->default(false);
            $table->decimal('loading_time', 5, 2)->nullable();

            // Notizen
            $table->text('issues_found')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('next_maintenance_date')->nullable();

            // PDF & Versand
            $table->timestamp('sent_at')->nullable();
            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
    }
};
