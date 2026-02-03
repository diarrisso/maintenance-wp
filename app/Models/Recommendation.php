<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    public const TYPES = [
        'security' => 'Sicherheit',
        'plugin' => 'Plugin',
        'theme' => 'Theme',
        'performance' => 'Performance',
        'other' => 'Sonstige',
    ];

    public const PRIORITIES = [
        'critical' => 'Kritisch',
        'high' => 'Hoch',
        'medium' => 'Mittel',
        'low' => 'Niedrig',
    ];

    public const PRIORITY_COLORS = [
        'critical' => 'bg-red-100 text-red-800',
        'high' => 'bg-orange-100 text-orange-800',
        'medium' => 'bg-yellow-100 text-yellow-800',
        'low' => 'bg-blue-100 text-blue-800',
    ];

    public const ACTIONS = [
        'replace' => 'Ersetzen',
        'remove' => 'Entfernen',
        'update' => 'Aktualisieren',
        'configure' => 'Konfigurieren',
        'install' => 'Installieren',
    ];

    protected $fillable = [
        'maintenance_report_id',
        'type',
        'priority',
        'title',
        'description',
        'action',
        'current_item',
        'suggested_item',
        'approved_by_client',
    ];

    protected $casts = [
        'approved_by_client' => 'boolean',
    ];

    public function maintenanceReport(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute(): string
    {
        return self::PRIORITY_COLORS[$this->priority] ?? 'bg-gray-100 text-gray-800';
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }
}
