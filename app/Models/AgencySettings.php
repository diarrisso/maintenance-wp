<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AgencySettings extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'logo_white',
        'favicon',
        'email',
        'phone',
        'website',
        'address_street',
        'address_zip',
        'address_city',
        'address_country',
        'tax_id',
        'footer_text',
        'primary_color',
    ];

    /**
     * Get the singleton instance of agency settings
     */
    public static function instance(): self
    {
        return Cache::remember('agency_settings', 3600, function () {
            return self::firstOrCreate([], [
                'name' => 'Masinga Tech',
                'email' => 'info@masingatech.com',
                'website' => 'www.masingatech.com',
                'address_country' => 'Deutschland',
                'primary_color' => '#2563eb',
            ]);
        });
    }

    /**
     * Clear the cached settings
     */
    public static function clearCache(): void
    {
        Cache::forget('agency_settings');
    }

    /**
     * Get the full address formatted
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_street,
            trim($this->address_zip . ' ' . $this->address_city),
            $this->address_country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    /**
     * Get the white logo URL
     */
    public function getLogoWhiteUrlAttribute(): ?string
    {
        return $this->logo_white ? Storage::url($this->logo_white) : null;
    }

    /**
     * Get the favicon URL
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon) {
            return Storage::url($this->favicon);
        }
        return $this->logo_url;
    }

    /**
     * Get the logo path for PDF
     */
    public function getLogoPathAttribute(): ?string
    {
        return $this->logo ? storage_path('app/public/' . $this->logo) : null;
    }

    /**
     * Get the white logo path for PDF
     */
    public function getLogoWhitePathAttribute(): ?string
    {
        return $this->logo_white ? storage_path('app/public/' . $this->logo_white) : null;
    }
}
