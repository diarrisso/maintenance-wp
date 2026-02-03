<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Client;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Website::with('client')->withCount('maintenanceReports');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $websites = $query->orderBy('name')->paginate(15);

        return view('websites.index', compact('websites'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $selectedClientId = $request->get('client_id');
        $website = null;

        return view('websites.create', compact('clients', 'selectedClientId', 'website'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'hosting_provider' => 'nullable|string|max:255',
            'php_version' => 'nullable|string|max:255',
            'wordpress_version' => 'nullable|string|max:255',
            'admin_url' => 'nullable|url|max:255',
            'maintenance_package' => 'required|in:monthly,quarterly,yearly,one_time',
            'maintenance_frequency' => 'nullable|integer|in:1,2',
            'next_maintenance_date' => 'nullable|date',
            'pm_user_id' => 'nullable|exists:users,id',
            'pm_email' => 'nullable|email|max:255',
            'teams_webhook_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
        ]);

        // Set default frequency if not monthly
        if ($validated['maintenance_package'] !== 'monthly') {
            $validated['maintenance_frequency'] = 1;
        }

        Website::create($validated);

        return redirect()->route('websites.index')
            ->with('success', 'Website erfolgreich erstellt.');
    }

    public function show(Website $website)
    {
        $website->load(['client', 'maintenanceReports.user']);

        return view('websites.show', compact('website'));
    }

    public function edit(Website $website)
    {
        $clients = Client::orderBy('name')->get();

        return view('websites.edit', compact('website', 'clients'));
    }

    public function update(Request $request, Website $website)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'hosting_provider' => 'nullable|string|max:255',
            'php_version' => 'nullable|string|max:255',
            'wordpress_version' => 'nullable|string|max:255',
            'admin_url' => 'nullable|url|max:255',
            'maintenance_package' => 'required|in:monthly,quarterly,yearly,one_time',
            'maintenance_frequency' => 'nullable|integer|in:1,2',
            'next_maintenance_date' => 'nullable|date',
            'pm_user_id' => 'nullable|exists:users,id',
            'pm_email' => 'nullable|email|max:255',
            'teams_webhook_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
        ]);

        // Set default frequency if not monthly
        if ($validated['maintenance_package'] !== 'monthly') {
            $validated['maintenance_frequency'] = 1;
        }

        $website->update($validated);

        return redirect()->route('websites.show', $website)
            ->with('success', 'Website erfolgreich aktualisiert.');
    }

    public function destroy(Website $website)
    {
        $website->delete();

        return redirect()->route('websites.index')
            ->with('success', 'Website erfolgreich gelöscht.');
    }
}
