<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'email',
        'phone',
        'company',
        'maintenance_type',
        'notes',
    ];

    public const MAINTENANCE_TYPES = [
        '2x_monthly' => '2x pro Monat (1 Jahr)',
        '1x_monthly' => '1x pro Monat (1 Jahr)',
    ];

    public function getMaintenanceTypeLabelAttribute(): string
    {
        return self::MAINTENANCE_TYPES[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }
}
