<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'url',
        'hosting_provider',
        'php_version',
        'wordpress_version',
        'admin_url',
        'maintenance_package',
        'maintenance_frequency',
        'next_maintenance_date',
        'pm_email',
        'pm_user_id',
        'teams_webhook_url',
        'notes',
    ];

    protected $casts = [
        'next_maintenance_date' => 'date',
        'maintenance_frequency' => 'integer',
    ];

    public const MAINTENANCE_PACKAGES = [
        'monthly' => 'Monatlich',
        'quarterly' => 'Vierteljährlich',
        'yearly' => 'Jährlich',
        'one_time' => 'Einmalig',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_user_id');
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function getMaintenancePackageLabelAttribute(): string
    {
        $label = self::MAINTENANCE_PACKAGES[$this->maintenance_package] ?? $this->maintenance_package;

        if ($this->maintenance_package === 'monthly' && $this->maintenance_frequency > 1) {
            $label .= " ({$this->maintenance_frequency}x)";
        }

        return $label;
    }
}
