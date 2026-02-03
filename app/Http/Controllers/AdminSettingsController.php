<?php

namespace App\Http\Controllers;

use App\Models\AgencySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = AgencySettings::instance();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_zip' => 'nullable|string|max:20',
            'address_city' => 'nullable|string|max:255',
            'address_country' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'logo_white' => 'nullable|image|max:2048',
            'favicon' => 'nullable|file|max:1024',
        ]);

        $settings = AgencySettings::instance();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')->store('agency', 'public');
        }

        // Handle white logo upload
        if ($request->hasFile('logo_white')) {
            if ($settings->logo_white) {
                Storage::disk('public')->delete($settings->logo_white);
            }
            $validated['logo_white'] = $request->file('logo_white')->store('agency', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('agency', 'public');
        }

        $settings->update($validated);
        AgencySettings::clearCache();

        return redirect()->route('admin.settings')
            ->with('success', 'Einstellungen erfolgreich gespeichert.');
    }

    public function deleteLogo(Request $request)
    {
        $settings = AgencySettings::instance();
        $type = $request->input('type', 'logo');

        if ($type === 'logo' && $settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $settings->update(['logo' => null]);
        } elseif ($type === 'logo_white' && $settings->logo_white) {
            Storage::disk('public')->delete($settings->logo_white);
            $settings->update(['logo_white' => null]);
        } elseif ($type === 'favicon' && $settings->favicon) {
            Storage::disk('public')->delete($settings->favicon);
            $settings->update(['favicon' => null]);
        }

        AgencySettings::clearCache();

        return redirect()->route('admin.settings')
            ->with('success', 'Logo erfolgreich gelöscht.');
    }
}
