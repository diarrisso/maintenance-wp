<div class="space-y-6" x-data="{
    maintenancePackage: '{{ old('maintenance_package', $website->maintenance_package ?? 'monthly') }}',
    maintenanceFrequency: {{ old('maintenance_frequency', $website->maintenance_frequency ?? 1) }}
}">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kunde <span class="text-red-500">*</span></label>
        <select name="client_id" required class="w-full rounded-lg border-gray-300">
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ (old('client_id', $selectedClientId ?? $website->client_id ?? '') == $client->id) ? 'selected' : '' }}>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $website->name ?? '') }}" required class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-red-500">*</span></label>
            <input type="url" name="url" value="{{ old('url', $website->url ?? '') }}" required class="w-full rounded-lg border-gray-300">
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hosting-Anbieter</label>
            <input type="text" name="hosting_provider" value="{{ old('hosting_provider', $website->hosting_provider ?? '') }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PHP-Version</label>
            <input type="text" name="php_version" value="{{ old('php_version', $website->php_version ?? '') }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">WordPress-Version</label>
            <input type="text" name="wordpress_version" value="{{ old('wordpress_version', $website->wordpress_version ?? '') }}" class="w-full rounded-lg border-gray-300">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin-URL</label>
            <input type="url" name="admin_url" value="{{ old('admin_url', $website->admin_url ?? '') }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nächster Wartungstermin</label>
            <input type="date" name="next_maintenance_date" value="{{ old('next_maintenance_date', optional($website ?? null)->next_maintenance_date?->format('Y-m-d') ?? '') }}" class="w-full rounded-lg border-gray-300">
        </div>
    </div>

    <!-- Wartungspaket dynamique -->
    <div class="border-t pt-6 mt-6">
        <h4 class="font-medium text-gray-900 mb-4">Wartungspaket <span class="text-red-500">*</span></h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intervall</label>
                <select name="maintenance_package" x-model="maintenancePackage" required class="w-full rounded-lg border-gray-300">
                    <option value="monthly">Monatlich</option>
                    <option value="quarterly">Vierteljährlich</option>
                    <option value="yearly">Jährlich</option>
                    <option value="one_time">Einmalig</option>
                </select>
            </div>
            <div x-show="maintenancePackage === 'monthly'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1">Häufigkeit pro Monat</label>
                <select name="maintenance_frequency" x-model="maintenanceFrequency" class="w-full rounded-lg border-gray-300">
                    <option value="1">1x pro Monat</option>
                    <option value="2">2x pro Monat</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Project Manager -->
    <div class="border-t pt-6 mt-6">
        <h4 class="font-medium text-gray-900 mb-4">Benachrichtigungen an Project Manager</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Manager</label>
                <select name="pm_user_id" class="w-full rounded-lg border-gray-300">
                    <option value="">-- Kein PM --</option>
                    @foreach(\App\Models\User::where('role', 'manager')->orderBy('name')->get() as $manager)
                        <option value="{{ $manager->id }}" {{ old('pm_user_id', $website->pm_user_id ?? '') == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }} ({{ $manager->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Erhält eine Kopie des Wartungsberichts per E-Mail</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Oder E-Mail direkt eingeben</label>
                <input type="email" name="pm_email" value="{{ old('pm_email', $website->pm_email ?? '') }}" placeholder="pm@firma.de" class="w-full rounded-lg border-gray-300">
                <p class="text-xs text-gray-500 mt-1">Falls PM kein Benutzer im System ist</p>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Teams Webhook URL (optional)</label>
            <input type="url" name="teams_webhook_url" value="{{ old('teams_webhook_url', $website->teams_webhook_url ?? '') }}" placeholder="https://outlook.office.com/webhook/..." class="w-full rounded-lg border-gray-300">
            <p class="text-xs text-gray-500 mt-1">Sendet Benachrichtigung an Teams-Kanal</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notizen</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300">{{ old('notes', $website->notes ?? '') }}</textarea>
    </div>
</div>
