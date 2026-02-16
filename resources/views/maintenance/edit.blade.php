<x-app-layout>
    <x-slot name="header">Wartung bearbeiten</x-slot>

    @if(session('success'))<div class="mb-4 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif

    <div class="max-w-5xl" x-data="{
        plugins: {{ json_encode($report->pluginUpdates->map(fn($p) => ['name' => $p->plugin_name, 'version_before' => $p->version_before, 'version_after' => $p->version_after, 'status' => $p->status ?? 'updated', 'notes' => $p->notes ?? ''])) }},
        recommendations: {{ json_encode($report->getRelation('recommendations')->map(fn($r) => ['type' => $r->type, 'priority' => $r->priority, 'title' => $r->title, 'description' => $r->description ?? '', 'action' => $r->action ?? 'replace', 'current_item' => $r->current_item ?? '', 'suggested_item' => $r->suggested_item ?? ''])) }},
        addPlugin() { this.plugins.push({ name: '', version_before: '', version_after: '', status: 'updated', notes: '' }); },
        removePlugin(index) { this.plugins.splice(index, 1); },
        addRecommendation() { this.recommendations.push({ type: 'plugin', priority: 'medium', title: '', description: '', action: 'replace', current_item: '', suggested_item: '' }); },
        removeRecommendation(index) { this.recommendations.splice(index, 1); }
    }">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">{{ $report->website->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $report->website->client->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Entwurf</span>
                    <form action="{{ route('maintenance.complete', $report) }}" method="POST" x-data
                          @submit.prevent="confirmAction($el, 'Wartung abschließen', 'Möchten Sie die Wartung abschließen und das PDF erstellen?', 'Abschließen')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-success-600 hover:bg-success-700 text-white rounded-lg font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Abschließen (PDF erstellen)
                        </button>
                    </form>
                </div>
            </div>

            <form action="{{ route('maintenance.update', $report) }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <!-- Website-Zustand -->
                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-4 border border-blue-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Website-Zustand <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-4 gap-3">
                        <label class="flex items-center p-3 bg-green-100 border-2 border-transparent rounded-lg cursor-pointer hover:border-green-500 has-[:checked]:border-green-500 has-[:checked]:ring-2 has-[:checked]:ring-green-500">
                            <input type="radio" name="website_condition" value="excellent" class="sr-only" {{ old('website_condition', $report->website_condition) === 'excellent' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <div class="text-2xl mb-1">🌟</div>
                                <div class="font-medium text-green-800">Ausgezeichnet</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-blue-100 border-2 border-transparent rounded-lg cursor-pointer hover:border-blue-500 has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-500">
                            <input type="radio" name="website_condition" value="good" class="sr-only" {{ old('website_condition', $report->website_condition) === 'good' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <div class="text-2xl mb-1">✅</div>
                                <div class="font-medium text-blue-800">Gut</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-yellow-100 border-2 border-transparent rounded-lg cursor-pointer hover:border-yellow-500 has-[:checked]:border-yellow-500 has-[:checked]:ring-2 has-[:checked]:ring-yellow-500">
                            <input type="radio" name="website_condition" value="needs_improvement" class="sr-only" {{ old('website_condition', $report->website_condition) === 'needs_improvement' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <div class="text-2xl mb-1">⚠️</div>
                                <div class="font-medium text-yellow-800">Verbesserung nötig</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-red-100 border-2 border-transparent rounded-lg cursor-pointer hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:ring-2 has-[:checked]:ring-red-500">
                            <input type="radio" name="website_condition" value="critical" class="sr-only" {{ old('website_condition', $report->website_condition) === 'critical' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <div class="text-2xl mb-1">🚨</div>
                                <div class="font-medium text-red-800">Kritisch</div>
                            </div>
                        </label>
                    </div>
                    <div class="mt-3">
                        <textarea name="website_condition_notes" rows="2" placeholder="Notizen zum Zustand (optional)..." class="w-full rounded-lg border-gray-300 text-sm">{{ old('website_condition_notes', $report->website_condition_notes) }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-{{ $report->website->client->maintenance_type === '2x_monthly' ? '3' : '2' }} gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Wartungsdatum</label>
                        <input type="date" name="maintenance_date" value="{{ old('maintenance_date', $report->maintenance_date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300">
                    </div>
                    @if($report->website->client->maintenance_type === '2x_monthly')
                        @php
                            // Calculer le numéro suggéré basé sur la date du rapport
                            $reportDate = \Carbon\Carbon::parse(old('maintenance_date', $report->maintenance_date));
                            // Si avant le 15 du mois → 1ère wartung, sinon → 2ème wartung
                            $suggestedNumber = $reportDate->day < 15 ? 1 : 2;
                        @endphp
                        <div>
                            <label class="block text-sm font-medium mb-2">Wartung Nr. <span class="text-red-500">*</span></label>
                            <select name="maintenance_number" required class="w-full rounded-lg border-gray-300">
                                <option value="1" {{ old('maintenance_number', $report->maintenance_number) == 1 ? 'selected' : '' }}>1. Wartung (Anfang des Monats)</option>
                                <option value="2" {{ old('maintenance_number', $report->maintenance_number) == 2 ? 'selected' : '' }}>2. Wartung (Mitte des Monats)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Paket: {{ $report->website->client->maintenance_type_label }}
                                @if($suggestedNumber === ($report->maintenance_number ?? 1))
                                    <span class="text-green-600 font-medium">• Korrekt für {{ $reportDate->format('d.m.Y') }}</span>
                                @else
                                    <span class="text-orange-600 font-medium">• Vorschlag für {{ $reportDate->format('d.m.Y') }}: {{ $suggestedNumber }}. Wartung</span>
                                @endif
                            </p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium mb-2">Entwickler <span class="text-red-500">*</span></label>
                        <select name="entwickler_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">-- Entwickler wählen --</option>
                            @foreach(\App\Models\Entwickler::active()->orderBy('name')->get() as $entwickler)
                                <option value="{{ $entwickler->id }}" {{ old('entwickler_id', $report->entwickler_id) == $entwickler->id ? 'selected' : '' }}>
                                    {{ $entwickler->name }} {{ $entwickler->position ? '('.$entwickler->position.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Phase 1 - Vorbereitung</h3>
                    <label class="flex items-center mb-2"><input type="checkbox" name="backup_completed" value="1" {{ $report->backup_completed ? 'checked' : '' }} class="rounded mr-2"> Backup durchgeführt</label>
                    <div class="mb-2"><label class="block text-sm mb-1">Backup Datum/Uhrzeit</label><input type="datetime-local" name="backup_datetime" value="{{ $report->backup_datetime?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border-gray-300"></div>
                    <label class="flex items-center"><input type="checkbox" name="php_compatible" value="1" {{ $report->php_compatible ? 'checked' : '' }} class="rounded mr-2"> PHP-Version kompatibel</label>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Phase 2 - Aktualisierungen</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><input type="text" name="wp_version_before" value="{{ $report->wp_version_before }}" placeholder="WP vorher" class="w-full rounded-lg border-gray-300"></div>
                        <div><input type="text" name="wp_version_after" value="{{ $report->wp_version_after }}" placeholder="WP nachher" class="w-full rounded-lg border-gray-300"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="theme_name" value="{{ $report->theme_name }}" placeholder="Theme Name" class="rounded-lg border-gray-300">
                        <input type="text" name="theme_version_before" value="{{ $report->theme_version_before }}" placeholder="Theme vorher" class="rounded-lg border-gray-300">
                        <input type="text" name="theme_version_after" value="{{ $report->theme_version_after }}" placeholder="Theme nachher" class="rounded-lg border-gray-300">
                    </div>

                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <label class="font-medium text-gray-900">Plugin-Updates</label>
                                <p class="text-sm text-gray-500">Alle aktualisierten Plugins</p>
                            </div>
                            <button type="button" @click="addPlugin()" class="inline-flex items-center bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Plugin hinzufügen
                            </button>
                        </div>

                        <template x-if="plugins.length === 0">
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Keine Plugins. Klicken Sie auf "Plugin hinzufügen".
                            </div>
                        </template>

                        <template x-if="plugins.length > 0">
                            <div>
                                <div class="grid grid-cols-6 gap-2 mb-2 text-xs font-medium text-gray-500 uppercase">
                                    <div>Plugin Name</div>
                                    <div>Vorher</div>
                                    <div>Nachher</div>
                                    <div>Status</div>
                                    <div>Notiz</div>
                                    <div></div>
                                </div>
                            </div>
                        </template>

                        <template x-for="(plugin, index) in plugins" :key="index">
                            <div class="grid grid-cols-6 gap-2 mb-2">
                                <input type="text" :name="`plugins[${index}][name]`" x-model="plugin.name" placeholder="z.B. Yoast SEO" class="rounded-lg border-gray-300 text-sm">
                                <input type="text" :name="`plugins[${index}][version_before]`" x-model="plugin.version_before" placeholder="z.B. 21.0" class="rounded-lg border-gray-300 text-sm">
                                <input type="text" :name="`plugins[${index}][version_after]`" x-model="plugin.version_after" placeholder="z.B. 21.5" class="rounded-lg border-gray-300 text-sm">
                                <select :name="`plugins[${index}][status]`" x-model="plugin.status" class="rounded-lg border-gray-300 text-sm">
                                    <option value="updated">Aktualisiert</option>
                                    <option value="skipped">Übersprungen</option>
                                    <option value="no_access">Kein Zugang</option>
                                </select>
                                <input type="text" :name="`plugins[${index}][notes]`" x-model="plugin.notes" placeholder="Optional" class="rounded-lg border-gray-300 text-sm">
                                <button type="button" @click="removePlugin(index)" class="bg-red-500 hover:bg-red-600 text-white rounded-lg px-3 py-2 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>

                        <template x-if="plugins.length > 0">
                            <div class="mt-3 pt-3 border-t border-gray-200 text-sm text-gray-600">
                                <span x-text="plugins.length"></span> Plugin(s)
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Phase 3 - Prüfungen</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center"><input type="checkbox" name="check_frontend" value="1" {{ $report->check_frontend ? 'checked' : '' }} class="rounded mr-2"> Startseite</label>
                        <label class="flex items-center"><input type="checkbox" name="check_navigation" value="1" {{ $report->check_navigation ? 'checked' : '' }} class="rounded mr-2"> Navigation</label>
                        <label class="flex items-center"><input type="checkbox" name="check_forms" value="1" {{ $report->check_forms ? 'checked' : '' }} class="rounded mr-2"> Formulare</label>
                        <label class="flex items-center"><input type="checkbox" name="check_responsive" value="1" {{ $report->check_responsive ? 'checked' : '' }} class="rounded mr-2"> Responsive</label>
                        <label class="flex items-center"><input type="checkbox" name="check_admin_login" value="1" {{ $report->check_admin_login ? 'checked' : '' }} class="rounded mr-2"> Admin-Login</label>
                        <label class="flex items-center"><input type="checkbox" name="check_media_upload" value="1" {{ $report->check_media_upload ? 'checked' : '' }} class="rounded mr-2"> Medien-Upload</label>
                        <label class="flex items-center"><input type="checkbox" name="check_no_errors" value="1" {{ $report->check_no_errors ? 'checked' : '' }} class="rounded mr-2"> Keine Fehler</label>
                        <label class="flex items-center"><input type="checkbox" name="check_woocommerce" value="1" {{ $report->check_woocommerce ? 'checked' : '' }} class="rounded mr-2"> WooCommerce</label>
                        <label class="flex items-center"><input type="checkbox" name="check_ssl" value="1" {{ $report->check_ssl ? 'checked' : '' }} class="rounded mr-2"> SSL</label>
                        <label class="flex items-center"><input type="checkbox" name="check_security" value="1" {{ $report->check_security ? 'checked' : '' }} class="rounded mr-2"> Sicherheit</label>
                    </div>
                    <div class="mt-4"><input type="number" step="0.01" name="loading_time" value="{{ $report->loading_time }}" placeholder="Ladezeit (Sek.)" class="w-full rounded-lg border-gray-300"></div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Phase 4 - Sicherheit</h3>
                    <div class="bg-red-50 rounded-lg p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Security Plugin</label>
                                <select name="security_plugin" class="w-full rounded-lg border-gray-300">
                                    <option value="">-- Kein Plugin --</option>
                                    <option value="wordfence" {{ $report->security_plugin === 'wordfence' ? 'selected' : '' }}>Wordfence</option>
                                    <option value="sucuri" {{ $report->security_plugin === 'sucuri' ? 'selected' : '' }}>Sucuri</option>
                                    <option value="ithemes" {{ $report->security_plugin === 'ithemes' ? 'selected' : '' }}>iThemes Security</option>
                                    <option value="all_in_one" {{ $report->security_plugin === 'all_in_one' ? 'selected' : '' }}>All In One WP Security</option>
                                    <option value="other" {{ $report->security_plugin === 'other' ? 'selected' : '' }}>Anderes</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Firewall Status</label>
                                <select name="firewall_status" class="w-full rounded-lg border-gray-300">
                                    <option value="">-- Nicht geprüft --</option>
                                    <option value="0" {{ $report->firewall_status === 0 ? 'selected' : '' }}>Nicht aktiv (0%)</option>
                                    <option value="25" {{ $report->firewall_status === 25 ? 'selected' : '' }}>Niedrig (25%)</option>
                                    <option value="50" {{ $report->firewall_status === 50 ? 'selected' : '' }}>Mittel (50%)</option>
                                    <option value="75" {{ $report->firewall_status === 75 ? 'selected' : '' }}>Gut (75%)</option>
                                    <option value="100" {{ $report->firewall_status === 100 ? 'selected' : '' }}>Optimal (100%)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Sicherheitsprobleme</label>
                                <input type="number" name="security_issues_count" min="0" value="{{ $report->security_issues_count }}" placeholder="Anzahl" class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Brute-Force (heute)</label>
                                <input type="number" name="brute_force_attacks_day" min="0" value="{{ $report->brute_force_attacks_day }}" placeholder="z.B. 189" class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Brute-Force (Woche)</label>
                                <input type="number" name="brute_force_attacks_week" min="0" value="{{ $report->brute_force_attacks_week }}" placeholder="z.B. 5448" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Sicherheitsprobleme (Details)</label>
                            <textarea name="security_issues_details" rows="3" placeholder="z.B. 10 Probleme beim Scan..." class="w-full rounded-lg border-gray-300">{{ $report->security_issues_details }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Firewall Notizen</label>
                            <textarea name="firewall_notes" rows="2" placeholder="z.B. Rate Limiting aktiviert..." class="w-full rounded-lg border-gray-300">{{ $report->firewall_notes }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Durchgeführte Sicherheitsmaßnahmen</label>
                            <textarea name="security_actions_taken" rows="3" placeholder="z.B. 2FA aktiviert, Login-URL geändert..." class="w-full rounded-lg border-gray-300">{{ $report->security_actions_taken }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Empfehlungen -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Phase 5 - Empfehlungen</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <label class="font-medium text-gray-900">Empfehlungen für den Kunden</label>
                                <p class="text-sm text-gray-500">Plugins ersetzen, entfernen oder aktualisieren</p>
                            </div>
                            <button type="button" @click="addRecommendation()" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Empfehlung hinzufügen
                            </button>
                        </div>

                        <template x-if="recommendations.length === 0">
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Keine Empfehlungen. Klicken Sie auf "Empfehlung hinzufügen".
                            </div>
                        </template>

                        <template x-for="(rec, index) in recommendations" :key="index">
                            <div class="bg-white rounded-lg p-4 mb-3 border border-purple-200">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-sm font-medium text-purple-700">Empfehlung #<span x-text="index + 1"></span></span>
                                    <button type="button" @click="removeRecommendation(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Typ</label>
                                        <select :name="`recommendations[${index}][type]`" x-model="rec.type" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="security">Sicherheit</option>
                                            <option value="plugin">Plugin</option>
                                            <option value="theme">Theme</option>
                                            <option value="performance">Performance</option>
                                            <option value="other">Sonstige</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Priorität</label>
                                        <select :name="`recommendations[${index}][priority]`" x-model="rec.priority" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="critical">Kritisch</option>
                                            <option value="high">Hoch</option>
                                            <option value="medium">Mittel</option>
                                            <option value="low">Niedrig</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Aktion</label>
                                        <select :name="`recommendations[${index}][action]`" x-model="rec.action" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="replace">Ersetzen</option>
                                            <option value="remove">Entfernen</option>
                                            <option value="update">Aktualisieren</option>
                                            <option value="configure">Konfigurieren</option>
                                            <option value="install">Installieren</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Titel</label>
                                    <input type="text" :name="`recommendations[${index}][title]`" x-model="rec.title" placeholder="z.B. Plugin Admin Menü Manager ersetzen" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Aktuelles Element</label>
                                        <input type="text" :name="`recommendations[${index}][current_item]`" x-model="rec.current_item" placeholder="z.B. Admin Menü Manager" class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Vorgeschlagene Alternative</label>
                                        <input type="text" :name="`recommendations[${index}][suggested_item]`" x-model="rec.suggested_item" placeholder="z.B. Admin Menu Editor" class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Beschreibung / Begründung</label>
                                    <textarea :name="`recommendations[${index}][description]`" x-model="rec.description" rows="2" placeholder="z.B. Das Plugin ist ein bekanntes Sicherheitsrisiko..." class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                                </div>
                            </div>
                        </template>

                        <template x-if="recommendations.length > 0">
                            <div class="mt-3 pt-3 border-t border-purple-200 text-sm text-purple-700">
                                <span x-text="recommendations.length"></span> Empfehlung(en)
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Notizen</h3>
                    <textarea name="issues_found" rows="3" placeholder="Probleme" class="w-full rounded-lg border-gray-300 mb-4">{{ $report->issues_found }}</textarea>
                    <textarea name="recommendations_text" rows="3" placeholder="Zusätzliche Hinweise..." class="w-full rounded-lg border-gray-300 mb-4">{{ $report->recommendations }}</textarea>
                    <input type="date" name="next_maintenance_date" value="{{ $report->next_maintenance_date?->format('Y-m-d') }}" placeholder="Nächste Wartung" class="w-full rounded-lg border-gray-300">
                </div>

                <!-- Unterschrift -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Unterschrift des Technikers</h3>
                    <div x-data="{
                        isDrawing: false,
                        hasSignature: {{ $report->signature ? 'true' : 'false' }},
                        signatureData: '{{ $report->signature }}',
                        lastPoint: null,
                        canvas: null,
                        ctx: null,
                        initCanvas() {
                            this.canvas = this.$refs.signatureCanvas;
                            this.ctx = this.canvas.getContext('2d');
                            const dpr = window.devicePixelRatio || 1;
                            const rect = this.canvas.parentElement.getBoundingClientRect();
                            const w = Math.min(500, rect.width - 4);
                            const h = Math.round(w * 0.4);
                            this.canvas.width = w * dpr;
                            this.canvas.height = h * dpr;
                            this.canvas.style.width = w + 'px';
                            this.canvas.style.height = h + 'px';
                            this.ctx.scale(dpr, dpr);
                            this.ctx.fillStyle = '#ffffff';
                            this.ctx.fillRect(0, 0, w, h);
                            this.ctx.strokeStyle = '#000000';
                            this.ctx.lineWidth = 2;
                            this.ctx.lineCap = 'round';
                            this.ctx.lineJoin = 'round';
                            if (this.signatureData) {
                                const img = new Image();
                                img.onload = () => { this.ctx.drawImage(img, 0, 0, w, h); };
                                img.src = this.signatureData;
                            }
                        },
                        getCoords(e) {
                            const rect = this.canvas.getBoundingClientRect();
                            if (e.touches) { const t = e.touches[0]; return { x: t.clientX - rect.left, y: t.clientY - rect.top }; }
                            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
                        },
                        startDraw(e) {
                            e.preventDefault();
                            this.isDrawing = true;
                            this.lastPoint = this.getCoords(e);
                            this.hasSignature = true;
                        },
                        draw(e) {
                            if (!this.isDrawing) return;
                            e.preventDefault();
                            const coords = this.getCoords(e);
                            this.ctx.beginPath();
                            this.ctx.moveTo(this.lastPoint.x, this.lastPoint.y);
                            this.ctx.lineTo(coords.x, coords.y);
                            this.ctx.stroke();
                            this.lastPoint = coords;
                        },
                        stopDraw() { this.isDrawing = false; this.lastPoint = null; },
                        clearSignature() {
                            const w = parseInt(this.canvas.style.width);
                            const h = parseInt(this.canvas.style.height);
                            this.ctx.fillStyle = '#ffffff';
                            this.ctx.fillRect(0, 0, w, h);
                            this.hasSignature = false;
                            this.signatureData = '';
                        },
                        saveSignature() {
                            if (!this.hasSignature) return;
                            this.signatureData = this.canvas.toDataURL('image/png');
                        }
                    }" x-init="initCanvas()">
                        <div class="relative rounded-lg border-2 border-dashed border-gray-400 bg-white inline-block touch-none">
                            <canvas x-ref="signatureCanvas"
                                class="rounded-lg cursor-crosshair"
                                @mousedown="startDraw($event)" @mousemove="draw($event)" @mouseup="stopDraw()" @mouseleave="stopDraw()"
                                @touchstart="startDraw($event)" @touchmove="draw($event)" @touchend="stopDraw()" @touchcancel="stopDraw()">
                            </canvas>
                            <div x-show="!hasSignature" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-gray-400">
                                    <svg class="mx-auto mb-2 h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <p class="text-sm">Hier unterschreiben</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="signature" x-model="signatureData">
                        <div class="mt-3 flex gap-2">
                            <button type="button" @click="clearSignature()" :disabled="!hasSignature"
                                class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm disabled:opacity-50 hover:bg-gray-50">
                                Löschen
                            </button>
                            <button type="button" @click="saveSignature()" :disabled="!hasSignature"
                                class="inline-flex items-center rounded-md bg-primary-600 px-3 py-1.5 text-sm text-white hover:bg-primary-700 disabled:opacity-50">
                                Unterschrift bestätigen
                            </button>
                        </div>
                        <template x-if="signatureData">
                            <p class="mt-2 text-sm text-green-600 font-medium">Unterschrift gespeichert</p>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('reports.show', $report) }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Abbrechen</a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
