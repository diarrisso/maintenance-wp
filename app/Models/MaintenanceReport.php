<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceReport extends Model
{
    use HasFactory;

    public const WEBSITE_CONDITIONS = [
        'excellent' => 'Ausgezeichnet',
        'good' => 'Gut',
        'needs_improvement' => 'Verbesserung nötig',
        'critical' => 'Kritisch',
    ];

    public const WEBSITE_CONDITION_COLORS = [
        'excellent' => 'bg-green-100 text-green-800',
        'good' => 'bg-blue-100 text-blue-800',
        'needs_improvement' => 'bg-yellow-100 text-yellow-800',
        'critical' => 'bg-red-100 text-red-800',
    ];

    protected $fillable = [
        'website_id',
        'user_id',
        'entwickler_id',
        'status',
        'website_condition',
        'website_condition_notes',
        'maintenance_date',
        'maintenance_number',
        'backup_completed',
        'backup_datetime',
        'php_compatible',
        'wp_version_before',
        'wp_version_after',
        'theme_name',
        'theme_version_before',
        'theme_version_after',
        'check_frontend',
        'check_navigation',
        'check_forms',
        'check_responsive',
        'check_admin_login',
        'check_media_upload',
        'check_no_errors',

        'check_ssl',
        'check_security',
        'security_plugin',
        'security_issues_count',
        'security_issues_details',
        'firewall_status',
        'firewall_notes',
        'brute_force_attacks_day',
        'brute_force_attacks_week',
        'security_actions_taken',
        'loading_time',
        'issues_found',
        'recommendations',
        'next_maintenance_date',
        'sent_at',
        'pdf_path',
        'signature',
    ];

    public const FIREWALL_STATUSES = [
        0 => 'Nicht aktiv',
        25 => 'Niedrig (25%)',
        50 => 'Mittel (50%)',
        75 => 'Gut (75%)',
        100 => 'Optimal (100%)',
    ];

    public function getFirewallStatusLabelAttribute(): string
    {
        return self::FIREWALL_STATUSES[$this->firewall_status] ?? 'Unbekannt';
    }

    public function getWebsiteConditionLabelAttribute(): string
    {
        return self::WEBSITE_CONDITIONS[$this->website_condition] ?? 'Nicht bewertet';
    }

    public function getWebsiteConditionColorAttribute(): string
    {
        return self::WEBSITE_CONDITION_COLORS[$this->website_condition] ?? 'bg-gray-100 text-gray-800';
    }

    protected $casts = [
        'maintenance_date' => 'date',
        'backup_completed' => 'boolean',
        'backup_datetime' => 'datetime',
        'php_compatible' => 'boolean',
        'check_frontend' => 'boolean',
        'check_navigation' => 'boolean',
        'check_forms' => 'boolean',
        'check_responsive' => 'boolean',
        'check_admin_login' => 'boolean',
        'check_media_upload' => 'boolean',
        'check_no_errors' => 'boolean',

        'check_ssl' => 'boolean',
        'check_security' => 'boolean',
        'next_maintenance_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entwickler(): BelongsTo
    {
        return $this->belongsTo(Entwickler::class);
    }

    public function pluginUpdates(): HasMany
    {
        return $this->hasMany(PluginUpdate::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}
