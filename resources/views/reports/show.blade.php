<x-app-layout>
    <x-slot name="header">Wartungsbericht</x-slot>

    @if(session('success'))<div class="mb-4 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">{{ session('warning') }}</div>@endif

    <div class="space-y-6 max-w-5xl">

        {{-- Header Card --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $report->website->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $report->website->client->name }} &middot; {{ $report->maintenance_date->format('d.m.Y') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap justify-end">
                    @if($report->status === 'draft')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Entwurf</span>
                        @if(Auth::user()->isDeveloper())
                            <a href="{{ route('maintenance.edit', $report) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">Bearbeiten</a>
                            <form action="{{ route('maintenance.complete', $report) }}" method="POST" class="inline" x-data
                                  @submit.prevent="confirmAction($el, 'Bericht abschließen', 'Möchten Sie den Bericht abschließen und das PDF erstellen?', 'Abschließen')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-lg text-sm">Abschließen</button>
                            </form>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data
                                  @submit.prevent="confirmDelete($el, 'Entwurf löschen', 'Möchten Sie diesen Entwurf wirklich löschen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    @elseif($report->status === 'completed')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Abgeschlossen</span>
                        <a href="{{ route('reports.download', $report) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">PDF</a>
                        <form action="{{ route('reports.send-email', $report) }}" method="POST" class="inline" x-data
                              @submit.prevent="confirmAction($el, 'E-Mail senden', 'Möchten Sie die E-Mail an den Kunden und PM senden?', 'Senden')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm">E-Mail senden</button>
                        </form>
                        @if($report->website->teams_webhook_url)
                            <form action="{{ route('reports.send-teams', $report) }}" method="POST" class="inline">@csrf
                                <button type="submit" class="p-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm" title="Teams senden">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 8.04c-.24 0-.47.02-.7.05A5.99 5.99 0 0012 4a6 6 0 00-5.94 5.05 4.5 4.5 0 00.44 8.95h12.35a3.5 3.5 0 00.5-6.96z"/></svg>
                                </button>
                            </form>
                        @endif
                        @if(Auth::user()->isDeveloper())
                            <form action="{{ route('reports.duplicate', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'Bericht duplizieren', 'Möchten Sie diesen Bericht als neuen Entwurf duplizieren?', 'Duplizieren')">@csrf
                                <button type="submit" class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm" title="Duplizieren"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
                            </form>
                            <form action="{{ route('reports.regenerate-pdf', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'PDF neu generieren', 'Möchten Sie das PDF neu generieren?', 'Generieren')">@csrf
                                <button type="submit" class="p-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm" title="PDF neu generieren"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></button>
                            </form>
                            <a href="{{ route('maintenance.edit', $report) }}" class="p-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm" title="Bearbeiten"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmDelete($el, 'Bericht löschen', 'Möchten Sie diesen Bericht wirklich löschen?')">@csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-success-100 text-success-800 rounded-full text-sm font-medium">Gesendet</span>
                        @if($report->sent_at)<span class="text-sm text-gray-500">{{ $report->sent_at->format('d.m.Y H:i') }}</span>@endif
                        <a href="{{ route('reports.download', $report) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">PDF</a>
                        <form action="{{ route('reports.send-email', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'E-Mail erneut senden', 'Möchten Sie die E-Mail erneut senden?', 'Senden')">@csrf
                            <button type="submit" class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">Erneut senden</button>
                        </form>
                        @if($report->website->teams_webhook_url)
                            <form action="{{ route('reports.send-teams', $report) }}" method="POST" class="inline">@csrf
                                <button type="submit" class="p-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm" title="Teams"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 8.04c-.24 0-.47.02-.7.05A5.99 5.99 0 0012 4a6 6 0 00-5.94 5.05 4.5 4.5 0 00.44 8.95h12.35a3.5 3.5 0 00.5-6.96z"/></svg></button>
                            </form>
                        @endif
                        <form action="{{ route('reports.archive-report', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'Archivieren', 'Möchten Sie diesen Bericht archivieren?', 'Archivieren')">@csrf
                            <button type="submit" class="p-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg text-sm" title="Archivieren"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></button>
                        </form>
                        @if(Auth::user()->isDeveloper())
                            <form action="{{ route('reports.duplicate', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'Duplizieren', 'Möchten Sie diesen Bericht als neuen Entwurf duplizieren?', 'Duplizieren')">@csrf
                                <button type="submit" class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm" title="Duplizieren"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
                            </form>
                            <form action="{{ route('reports.regenerate-pdf', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmAction($el, 'PDF neu generieren', 'Möchten Sie das PDF neu generieren?', 'Generieren')">@csrf
                                <button type="submit" class="p-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm" title="PDF neu generieren"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></button>
                            </form>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline" x-data @submit.prevent="confirmDelete($el, 'Bericht löschen', 'Möchten Sie diesen Bericht wirklich löschen?')">@csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Löschen"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-8">

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 rounded-lg p-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Webseite</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $report->website->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Kunde</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $report->website->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Durchgeführt von</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $report->entwickler?->name ?? $report->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Wartungsintervall</p>
                        <p class="font-semibold text-gray-900 mt-1">{{ $report->website->client->maintenance_type_label ?? 'Standard' }}</p>
                    </div>
                </div>

                {{-- Website Condition --}}
                @if($report->website_condition)
                    @php
                        $condStyles = [
                            'excellent' => 'bg-green-50 border-green-200 text-green-800',
                            'good' => 'bg-blue-50 border-blue-200 text-blue-800',
                            'needs_improvement' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                            'critical' => 'bg-red-50 border-red-200 text-red-800',
                        ];
                        $condLabels = ['excellent' => 'Ausgezeichnet', 'good' => 'Gut', 'needs_improvement' => 'Verbesserung nötig', 'critical' => 'Kritisch'];
                    @endphp
                    <div class="text-center py-3 rounded-lg border {{ $condStyles[$report->website_condition] ?? '' }}">
                        <span class="font-bold text-lg">Website-Zustand: {{ $condLabels[$report->website_condition] ?? '' }}</span>
                        @if($report->website_condition_notes)
                            <p class="text-sm mt-1 opacity-75">{{ $report->website_condition_notes }}</p>
                        @endif
                    </div>
                @endif

                {{-- 1. Vorbereitung --}}
                <div>
                    <h4 class="text-base font-bold text-gray-800 mb-3">1. Vorbereitung</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 p-3 rounded-lg {{ $report->backup_completed ? 'bg-green-50' : 'bg-red-50' }}">
                            <span class="{{ $report->backup_completed ? 'text-green-600' : 'text-red-600' }} text-lg">{{ $report->backup_completed ? '✓' : '✗' }}</span>
                            <div>
                                <p class="font-medium text-sm">Backup</p>
                                @if($report->backup_datetime)<p class="text-xs text-gray-500">{{ $report->backup_datetime->format('d.m.Y H:i') }}</p>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-lg {{ $report->php_compatible ? 'bg-green-50' : 'bg-red-50' }}">
                            <span class="{{ $report->php_compatible ? 'text-green-600' : 'text-red-600' }} text-lg">{{ $report->php_compatible ? '✓' : '✗' }}</span>
                            <p class="font-medium text-sm">PHP kompatibel</p>
                        </div>
                    </div>
                </div>

                {{-- 2. Aktualisierungen --}}
                @if($report->wp_version_before || $report->wp_version_after || $report->pluginUpdates->count() > 0)
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-3">2. Aktualisierungen</h4>
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-800 text-white">
                                        <th class="px-4 py-2.5 text-left font-medium">Komponente</th>
                                        <th class="px-4 py-2.5 text-left font-medium">Typ</th>
                                        <th class="px-4 py-2.5 text-left font-medium">Vorher</th>
                                        <th class="px-4 py-2.5 text-left font-medium">Nachher</th>
                                        <th class="px-4 py-2.5 text-center font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @if($report->wp_version_before || $report->wp_version_after)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5 font-medium">WordPress</td>
                                            <td class="px-4 py-2.5 text-gray-500">Core</td>
                                            <td class="px-4 py-2.5">{{ $report->wp_version_before ?? '-' }}</td>
                                            <td class="px-4 py-2.5">{{ $report->wp_version_after ?? '-' }}</td>
                                            <td class="px-4 py-2.5 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">OK</span></td>
                                        </tr>
                                    @endif
                                    @if($report->theme_name)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5 font-medium">{{ $report->theme_name }}</td>
                                            <td class="px-4 py-2.5 text-gray-500">Theme</td>
                                            <td class="px-4 py-2.5">{{ $report->theme_version_before ?? '-' }}</td>
                                            <td class="px-4 py-2.5">{{ $report->theme_version_after ?? '-' }}</td>
                                            <td class="px-4 py-2.5 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">OK</span></td>
                                        </tr>
                                    @endif
                                    @foreach($report->pluginUpdates as $plugin)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5">{{ $plugin->plugin_name }}</td>
                                            <td class="px-4 py-2.5 text-gray-500">Plugin</td>
                                            <td class="px-4 py-2.5">{{ $plugin->version_before ?? '-' }}</td>
                                            <td class="px-4 py-2.5">{{ $plugin->version_after ?? '-' }}</td>
                                            <td class="px-4 py-2.5 text-center">
                                                @if($plugin->status === 'updated')
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">OK</span>
                                                @elseif($plugin->status === 'skipped')
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Übersprungen</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">Kein Zugang</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- 3. Prüfungen --}}
                <div>
                    <h4 class="text-base font-bold text-gray-800 mb-3">3. Funktionsprüfungen</h4>
                    @php
                        $checks = [
                            ['Startseite', $report->check_frontend],
                            ['Navigation', $report->check_navigation],
                            ['Formulare', $report->check_forms],
                            ['Responsive', $report->check_responsive],
                            ['Admin-Login', $report->check_admin_login],
                            ['Medien-Upload', $report->check_media_upload],
                            ['Fehlerfrei', $report->check_no_errors],
                            ['SSL', $report->check_ssl],
                            ['Sicherheit', $report->check_security],
                        ];
                        $passed = collect($checks)->where('1', true)->count();
                    @endphp
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($checks as $check)
                            <div class="flex items-center gap-2 px-3 py-2 rounded {{ $check[1] ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                <span class="font-bold">{{ $check[1] ? '✓' : '✗' }}</span>
                                <span class="text-sm">{{ $check[0] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 font-bold {{ $passed === count($checks) ? 'text-green-600' : 'text-red-600' }}">
                        {{ $passed }}/{{ count($checks) }} Prüfungen bestanden
                    </p>
                    @if($report->loading_time)
                        <p class="mt-1 text-sm text-gray-600">Ladezeit: <strong>{{ $report->loading_time }}s</strong></p>
                    @endif
                </div>

                {{-- Sicherheit --}}
                @if($report->security_plugin || $report->firewall_status !== null || $report->brute_force_attacks_week)
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-3">4. Sicherheit</h4>
                        <div class="grid grid-cols-3 gap-3">
                            @if($report->security_plugin)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500">Plugin</p>
                                    <p class="font-semibold mt-1">{{ ucfirst($report->security_plugin) }}</p>
                                </div>
                            @endif
                            @if($report->firewall_status !== null)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500">Firewall</p>
                                    <p class="font-semibold mt-1 {{ $report->firewall_status >= 75 ? 'text-green-600' : ($report->firewall_status >= 50 ? 'text-orange-500' : 'text-red-600') }}">{{ $report->firewall_status }}%</p>
                                </div>
                            @endif
                            @if($report->brute_force_attacks_week)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500">Angriffe (Woche)</p>
                                    <p class="font-semibold mt-1 {{ $report->brute_force_attacks_week > 1000 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($report->brute_force_attacks_week, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>
                        @if($report->security_issues_details)
                            <p class="mt-3 text-sm text-red-600">{{ $report->security_issues_details }}</p>
                        @endif
                        @if($report->security_actions_taken)
                            <p class="mt-2 text-sm text-green-700">{{ $report->security_actions_taken }}</p>
                        @endif
                    </div>
                @endif

                {{-- Probleme --}}
                @if($report->issues_found)
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-2">Festgestellte Probleme</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $report->issues_found }}</p>
                    </div>
                @endif

                {{-- Empfehlungen --}}
                @if($report->recommendations()->count() > 0)
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-3">Empfehlungen</h4>
                        <div class="space-y-2">
                            @foreach($report->recommendations()->get() as $rec)
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white
                                        {{ $rec->priority === 'critical' ? 'bg-red-500' : ($rec->priority === 'high' ? 'bg-orange-500' : ($rec->priority === 'medium' ? 'bg-yellow-500' : 'bg-blue-500')) }}">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900">{{ $rec->title }}</p>
                                        @if($rec->description)<p class="text-sm text-gray-500 mt-0.5">{{ $rec->description }}</p>@endif
                                        @if($rec->current_item && $rec->suggested_item)
                                            <p class="text-sm mt-1">{{ $rec->current_item }} <span class="text-gray-400">→</span> <span class="text-green-600">{{ $rec->suggested_item }}</span></p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Zusätzliche Hinweise --}}
                @if($report->recommendations)
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-2">Zusätzliche Hinweise</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $report->recommendations }}</p>
                    </div>
                @endif

                {{-- Nächste Wartung --}}
                @if($report->next_maintenance_date)
                    <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-xs text-blue-600 font-medium">Nächste Wartung</p>
                            <p class="font-bold text-blue-900">{{ $report->next_maintenance_date->format('d.m.Y') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
