<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_report_id',
        'plugin_name',
        'version_before',
        'version_after',
        'status',
        'notes',
    ];

    public const STATUSES = [
        'updated' => 'Aktualisiert',
        'skipped' => 'Übersprungen',
        'no_access' => 'Kein Zugang (Premium)',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function maintenanceReport(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class);
    }
}
